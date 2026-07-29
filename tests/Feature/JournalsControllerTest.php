<?php

namespace Modules\Journals\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Journals\Models\Journal;
use Modules\Usermanagement\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class JournalsControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('module:migrate', ['module' => 'Usermanagement']);
        $this->artisan('module:migrate', ['module' => 'Journals']);

        // Create user with journal permissions
        $this->user = User::factory()->create();
        $this->seed(\Modules\Journals\Database\Seeders\JournalsDatabaseSeeder::class);
        $role = Role::firstOrCreate(['name' => 'administrator', 'guard_name' => 'web']);
        
        $actions = ['read', 'create', 'update', 'delete', 'export'];
        foreach ($actions as $act) {
            $p = Permission::where('name', 'journals.' . $act)->first();
            if ($p) {
                $role->givePermissionTo($p);
            }
        }
        $this->user->assignRole($role);
    }

    public function test_can_view_journals_index_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('journals.index'));
        $response->assertStatus(200);
        $response->assertViewIs('journals::index');
    }

    public function test_can_fetch_journals_datatables(): void
    {
        Journal::create([
            'name' => 'Test Journal',
            'slug' => 'test-journal',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->getJson(route('journals.datatables'));
        $response->assertStatus(200);
        $response->assertJsonStructure(['recordsTotal', 'data']);
    }

    public function test_can_create_journal_via_service(): void
    {
        $data = [
            'name' => 'Journal of Science',
            'short_name' => 'JOS',
            'issn_p' => '1234-5678',
            'is_active' => 1,
        ];

        $response = $this->actingAs($this->user)->post(route('journals.store'), $data);
        $response->assertRedirect();
        $this->assertDatabaseHas('journals', ['name' => 'Journal of Science', 'short_name' => 'JOS']);
    }

    public function test_cannot_delete_journal_with_relations(): void
    {
        $journal = Journal::create(['name' => 'Protected Journal', 'slug' => 'protected-journal']);
        $journal->editorialBoards()->create([
            'role' => 'Editor-in-Chief',
            'name' => 'John Doe',
        ]);

        $response = $this->actingAs($this->user)->deleteJson(route('journals.destroy', $journal->id));
        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
    }
}
