<?php

namespace Modules\Journals\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Modules\Journals\Models\Journal;
use Modules\Usermanagement\Models\User;

class PublishingHubController extends Controller
{
    /**
     * Display the One-Stop End-to-End Journal Publishing Hub.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->can('journals.read')) {
            abort(403, 'Sorry! You are not allowed to access the Publishing Hub.');
        }

        // 1. Ambil seluruh data jurnal untuk dropdown switcher
        $journals = Journal::with(['editorialBoards.user'])->orderBy('name', 'asc')->get();

        // 2. Tentukan jurnal aktif yang sedang dikelola
        $selectedJournalId = $request->query('journal_id');
        $selectedJournal = null;

        if ($selectedJournalId) {
            $selectedJournal = $journals->firstWhere('id', (int) $selectedJournalId);
        }

        if (!$selectedJournal) {
            $selectedJournal = $journals->firstWhere('is_active', true) ?? $journals->first();
        }

        // Inisialisasi koleksi & variabel
        $editorialBoards = collect();
        $issues = collect();
        $targetIssue = null;
        $issueArticles = collect();
        $submissions = collect();
        $readyToPublishSubmissions = collect();
        $availableUsers = collect();

        $stats = [
            'total_journals'           => $journals->count(),
            'editorial_count'          => 0,
            'editor_in_chief_count'    => 0,
            'issues_count'             => 0,
            'published_issues_count'   => 0,
            'submissions_count'        => 0,
            'in_review_count'          => 0,
            'accepted_count'           => 0,
            'published_articles_count' => 0,
            'progress_percentage'      => 0,
            'step_completed'           => [
                1 => false, // Inisiasi Jurnal
                2 => false, // Dewan Redaksi
                3 => false, // Pembuatan Edisi / Issue
                4 => false, // Naskah & Penelaahan
                5 => false, // Publikasi Terbitan
            ],
        ];

        if ($selectedJournal) {
            // Evaluasi Langkah 1: Profil Jurnal
            $stats['step_completed'][1] = $selectedJournal->is_active && !empty($selectedJournal->name);

            // Evaluasi Langkah 2: Dewan Redaksi
            $editorialBoards = $selectedJournal->editorialBoards()->with('user')->orderBy('order_no', 'asc')->get();
            $stats['editorial_count'] = $editorialBoards->count();
            $stats['editor_in_chief_count'] = $editorialBoards->where('role', 'Editor-in-Chief')->count();
            $stats['step_completed'][2] = $stats['editorial_count'] > 0;

            // Evaluasi Langkah 3 & 5: Edisi / Issues
            if (Schema::hasTable('issues')) {
                $issues = \Modules\Issues\Models\Issue::where('journal_id', $selectedJournal->id)
                    ->withCount('articles')
                    ->orderBy('publication_year', 'desc')
                    ->orderBy('volume', 'desc')
                    ->orderBy('number', 'desc')
                    ->get();

                $stats['issues_count'] = $issues->count();
                $stats['published_issues_count'] = $issues->where('is_published', true)->count();
                $stats['step_completed'][3] = $stats['issues_count'] > 0;

                // Tentukan Issue target kerja: prioritas query param -> draft terbaru -> issue terbaru
                $selectedIssueId = $request->query('issue_id');
                if ($selectedIssueId) {
                    $targetIssue = $issues->firstWhere('id', (int) $selectedIssueId);
                }

                if (!$targetIssue) {
                    $targetIssue = $issues->firstWhere('is_published', false) ?? $issues->first();
                }

                if ($targetIssue && Schema::hasTable('articles')) {
                    $issueArticles = \Modules\Issues\Models\Article::where('issue_id', $targetIssue->id)
                        ->with(['submission.authors', 'submission.files'])
                        ->orderBy('id', 'asc')
                        ->get();

                    if ($targetIssue->is_published) {
                        $stats['step_completed'][5] = true;
                    }
                }
            }

            // Evaluasi Langkah 4: Naskah Submissions
            if (Schema::hasTable('submissions')) {
                $submissions = \Modules\Submissions\Models\Submission::where('journal_id', $selectedJournal->id)
                    ->with(['authors', 'files', 'author', 'editor'])
                    ->latest()
                    ->get();

                $stats['submissions_count'] = $submissions->where('status', '!=', 'draft')->count();
                $stats['in_review_count'] = $submissions->whereIn('status', [
                    'submitted',
                    'under_editor_review',
                    'under_review',
                    'revision_required',
                    'resubmitted'
                ])->count();
                $stats['accepted_count'] = $submissions->where('status', 'accepted')->count();
                $stats['published_articles_count'] = $submissions->where('status', 'published')->count();

                // Identifikasi naskah berstatus Accepted yang belum diplot ke target issue
                $assignedSubmissionIds = [];
                if (Schema::hasTable('articles') && $targetIssue) {
                    $assignedSubmissionIds = \Modules\Issues\Models\Article::where('issue_id', $targetIssue->id)
                        ->pluck('submission_id')
                        ->filter()
                        ->toArray();
                }

                $readyToPublishSubmissions = $submissions
                    ->where('status', 'accepted')
                    ->whereNotIn('id', $assignedSubmissionIds);

                $stats['step_completed'][4] = ($issueArticles->count() > 0) || ($stats['accepted_count'] > 0);
            }

            // Hitung persentase kesiapan penerbitan
            $completedStepsCount = count(array_filter($stats['step_completed']));
            $stats['progress_percentage'] = $completedStepsCount * 20;

            // Daftar user yang tersedia untuk penugasan redaksi
            $availableUsers = User::orderBy('name', 'asc')->get();
        }

        return view('journals::publishing-hub.index', compact(
            'journals',
            'selectedJournal',
            'editorialBoards',
            'issues',
            'targetIssue',
            'issueArticles',
            'submissions',
            'readyToPublishSubmissions',
            'availableUsers',
            'stats'
        ));
    }
}
