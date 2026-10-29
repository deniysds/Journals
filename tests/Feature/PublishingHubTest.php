<?php

namespace Modules\Journals\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Journals\Models\Journal;
use Modules\Usermanagement\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PublishingHubTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $userWithoutPermission;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('module:migrate', ['module' => 'Usermanagement']);
        $this->artisan('module:migrate', ['module' => 'Journals']);
        $this->artisan('module:migrate', ['module' => 'Submissions']);
        $this->artisan('module:migrate', ['module' => 'Issues']);

        $this->seed(\Modules\Journals\Database\Seeders\JournalsDatabaseSeeder::class);
        $this->seed(\Modules\Issues\Database\Seeders\IssuesDatabaseSeeder::class);
        $role = Role::firstOrCreate(['name' => 'administrator', 'guard_name' => 'web']);

        $actions = ['read', 'create', 'update', 'delete', 'export'];
        foreach ($actions as $act) {
            $p = Permission::where('name', 'journals.' . $act)->first();
            if ($p) {
                $role->givePermissionTo($p);
            }
            $pIssue = Permission::where('name', 'issues.' . $act)->first();
            if ($pIssue) {
                $role->givePermissionTo($pIssue);
            }
        }

        $this->user = User::factory()->create();
        $this->user->assignRole($role);

        $this->userWithoutPermission = User::factory()->create();
    }

    public function test_can_access_publishing_hub(): void
    {
        $journal = Journal::create([
            'name'       => 'Indonesian Journal of Genomics',
            'slug'       => 'ijg',
            'short_name' => 'IJG',
            'issn_p'     => '1234-5678',
            'issn_e'     => '8765-4321',
            'is_active'  => true,
        ]);

        $response = $this->actingAs($this->user)->get(route('journals.publishing-hub'));

        $response->assertStatus(200);
        $response->assertViewIs('journals::publishing-hub.index');
        $response->assertSee('Indonesian Journal of Genomics');
        $response->assertSee('Langkah 1: Profil', false);
        $response->assertSee('Langkah 2: Susunan Dewan Redaksi', false);
        $response->assertSee('Langkah 3: Pembuatan Edisi / Issue Terbitan', false);
        $response->assertSee('Langkah 4: Naskah Masuk', false);
        $response->assertSee('Langkah 5: Penataan Akhir', false);
    }

    public function test_user_without_permission_cannot_access_publishing_hub(): void
    {
        $response = $this->actingAs($this->userWithoutPermission)->get(route('journals.publishing-hub'));
        $response->assertStatus(403);
    }

    public function test_administrator_role_can_always_access_publishing_hub_even_without_direct_permissions(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'administrator', 'guard_name' => 'web']);
        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        Journal::create(['name' => 'Admin Monitored Journal', 'slug' => 'admin-journal', 'is_active' => true]);

        $response = $this->actingAs($admin)->get(route('journals.publishing-hub'));
        $response->assertStatus(200);
        $response->assertSee('Admin Monitored Journal');
    }

    public function test_journal_manager_and_editor_in_chief_roles_can_access_publishing_hub(): void
    {
        $jmRole = Role::firstOrCreate(['name' => 'journal-manager', 'guard_name' => 'web']);
        $jm = User::factory()->create();
        $jm->assignRole($jmRole);

        $eicRole = Role::firstOrCreate(['name' => 'editor-in-chief', 'guard_name' => 'web']);
        $eic = User::factory()->create();
        $eic->assignRole($eicRole);

        Journal::create(['name' => 'Ecosystem Journal', 'slug' => 'ecosystem-journal', 'is_active' => true]);

        $this->actingAs($jm)->get(route('journals.publishing-hub'))->assertStatus(200);
        $this->actingAs($eic)->get(route('journals.publishing-hub'))->assertStatus(200);
    }

    public function test_reader_role_is_strictly_forbidden_from_accessing_publishing_hub(): void
    {
        $readerRole = Role::firstOrCreate(['name' => 'reader', 'guard_name' => 'web']);
        $reader = User::factory()->create();
        $reader->assignRole($readerRole);

        $response = $this->actingAs($reader)->get(route('journals.publishing-hub'));
        $response->assertStatus(403);
    }

    public function test_publishing_hub_allows_switching_selected_journal(): void
    {
        $j1 = Journal::create(['name' => 'Journal Alpha', 'slug' => 'journal-alpha', 'is_active' => true]);
        $j2 = Journal::create(['name' => 'Journal Beta', 'slug' => 'journal-beta', 'is_active' => true]);

        $response = $this->actingAs($this->user)->get(route('journals.publishing-hub', ['journal_id' => $j2->id]));

        $response->assertStatus(200);
        $response->assertSee('Journal Beta');
    }

    public function test_publishing_hub_displays_editorial_and_issue_workflow(): void
    {
        $journal = Journal::create([
            'name'      => 'Biomedical Innovations',
            'slug'      => 'bio-innovations',
            'is_active' => true,
        ]);

        $journal->editorialBoards()->create([
            'name'        => 'Dr. Siti Rahma, Sp.A',
            'role'        => 'Editor-in-Chief',
            'affiliation' => 'Universitas Indonesia',
            'email'       => 'siti@ui.ac.id',
            'order_no'    => 1,
        ]);

        $issue = \Modules\Issues\Models\Issue::create([
            'journal_id'       => $journal->id,
            'volume'           => 1,
            'number'           => 1,
            'publication_year' => 2026,
            'title'            => 'Vol. 1 No. 1 (2026): Inisiasi Biomedis',
            'is_published'     => false,
        ]);

        $response = $this->actingAs($this->user)->get(route('journals.publishing-hub', ['journal_id' => $journal->id]));

        $response->assertStatus(200);
        $response->assertSee('Dr. Siti Rahma, Sp.A');
        $response->assertSee('Editor-in-Chief');
        $response->assertSee('Vol. 1 No. 1 (2026): Inisiasi Biomedis');
    }

    public function test_can_add_editorial_board_member_with_redirect_to(): void
    {
        $journal = Journal::create(['name' => 'Journal Gamma', 'slug' => 'journal-gamma', 'is_active' => true]);

        $redirectTarget = route('journals.publishing-hub', ['journal_id' => $journal->id]);

        $response = $this->actingAs($this->user)->post(route('editorial-boards.store'), [
            'journal_id'  => $journal->id,
            'name'        => 'Prof. Hendra',
            'role'        => 'Editor-in-Chief',
            'affiliation' => 'ITB',
            'email'       => 'hendra@itb.ac.id',
            'order_no'    => 1,
            'redirect_to' => $redirectTarget,
        ]);

        $response->assertRedirect($redirectTarget);
        $this->assertDatabaseHas('journal_editorial_boards', [
            'journal_id' => $journal->id,
            'name'       => 'Prof. Hendra',
        ]);
    }

    public function test_can_toggle_publish_issue_with_redirect_to(): void
    {
        $journal = Journal::create(['name' => 'Journal Delta', 'slug' => 'journal-delta', 'is_active' => true]);

        $issue = \Modules\Issues\Models\Issue::create([
            'journal_id'       => $journal->id,
            'volume'           => 1,
            'number'           => 1,
            'publication_year' => 2026,
            'title'            => 'Vol. 1 No. 1 (2026)',
            'is_published'     => false,
        ]);

        $redirectTarget = route('journals.publishing-hub', ['journal_id' => $journal->id]);

        $response = $this->actingAs($this->user)->post(route('issues.toggle_publish', $issue->id), [
            'redirect_to' => $redirectTarget,
        ]);

        $response->assertRedirect($redirectTarget);
        $this->assertTrue($issue->fresh()->is_published);
    }
}
