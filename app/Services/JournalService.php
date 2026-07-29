<?php

namespace Modules\Journals\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Journals\Models\Journal;
use Symfony\Component\HttpFoundation\StreamedResponse;

class JournalService
{
    /**
     * Get paginated & filtered data for KTUI DataTable.
     */
    public function getDatatableData(Request $request): array
    {
        $query = Journal::withCount('editorialBoards');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('short_name', 'like', '%' . $search . '%')
                  ->orWhere('issn_p', 'like', '%' . $search . '%')
                  ->orWhere('issn_e', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('sortOrder') && $request->filled('sortField')) {
            $query->orderBy($request->get('sortField'), $request->get('sortOrder'));
        } else {
            $query->latest();
        }

        $totalRecords = Journal::count();
        $filteredRecords = $query->count();

        $page = (int) $request->get('page', 1);
        $size = (int) $request->get('size', 10);
        $offset = ($page - 1) * $size;

        $data = $query->skip($offset)->take($size)->get();

        $data->transform(function ($journal) {
            $journal->can_delete = !$journal->hasActiveRelations();
            return $journal;
        });

        $pageCount = $size > 0 ? (int) ceil($filteredRecords / $size) : 0;

        return [
            'draw'            => $request->get('draw'),
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'pageCount'       => $pageCount,
            'page'            => $page,
            'pageSize'        => $size,
            'totalCount'      => $filteredRecords,
            'data'            => $data,
        ];
    }

    /**
     * Create a new journal with unique slug.
     */
    public function createJournal(array $data): Journal
    {
        return DB::transaction(function () use ($data) {
            if (empty($data['slug'])) {
                $data['slug'] = Str::slug($data['name']);
            }

            // Ensure unique slug
            $originalSlug = $data['slug'];
            $count = 1;
            while (Journal::where('slug', $data['slug'])->exists()) {
                $data['slug'] = $originalSlug . '-' . $count++;
            }

            $data['is_active'] = isset($data['is_active']) ? (bool) $data['is_active'] : true;

            return Journal::create($data);
        });
    }

    /**
     * Update an existing journal.
     */
    public function updateJournal(Journal $journal, array $data): Journal
    {
        return DB::transaction(function () use ($journal, $data) {
            if (empty($data['slug'])) {
                $data['slug'] = Str::slug($data['name']);
            }

            // Ensure unique slug ignoring current journal
            $originalSlug = $data['slug'];
            $count = 1;
            while (Journal::where('slug', $data['slug'])->where('id', '!=', $journal->id)->exists()) {
                $data['slug'] = $originalSlug . '-' . $count++;
            }

            $data['is_active'] = isset($data['is_active']) ? (bool) $data['is_active'] : false;

            $journal->update($data);
            return $journal;
        });
    }

    /**
     * Delete a single journal with relational check guard.
     */
    public function deleteJournal(int $id): array
    {
        $journal = Journal::find($id);
        if (!$journal) {
            return ['success' => false, 'message' => __('journals::app.error'), 'code' => 404];
        }

        if ($journal->hasActiveRelations()) {
            return [
                'success' => false,
                'message' => __('journals::app.cannot_delete_has_relations'),
                'code'    => 422,
            ];
        }

        $journal->delete();
        return ['success' => true, 'message' => __('journals::app.deleted_success'), 'code' => 200];
    }

    /**
     * Bulk delete selected journals with relational guard.
     */
    public function bulkDeleteJournals(array $ids): array
    {
        if (empty($ids)) {
            return ['success' => false, 'message' => __('journals::app.no_eligible_items'), 'code' => 422];
        }

        $journals = Journal::whereIn('id', $ids)->get();
        $deletableIds = [];
        $blockedCount = 0;

        foreach ($journals as $j) {
            if ($j->hasActiveRelations()) {
                $blockedCount++;
            } else {
                $deletableIds[] = $j->id;
            }
        }

        if (empty($deletableIds)) {
            return [
                'success' => false,
                'message' => __('journals::app.cannot_delete_has_relations'),
                'code'    => 422,
            ];
        }

        Journal::whereIn('id', $deletableIds)->delete();

        $msg = count($deletableIds) . ' journal(s) deleted successfully.';
        if ($blockedCount > 0) {
            $msg .= ' (' . $blockedCount . ' item(s) skipped due to active relations)';
        }

        return ['success' => true, 'message' => $msg, 'code' => 200];
    }

    /**
     * Export journals data to UTF-8 BOM CSV.
     */
    public function exportJournals(?string $search): StreamedResponse
    {
        $query = Journal::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('short_name', 'like', '%' . $search . '%')
                  ->orWhere('issn_p', 'like', '%' . $search . '%')
                  ->orWhere('issn_e', 'like', '%' . $search . '%');
            });
        }

        $journals = $query->latest()->get();
        $fileName = 'journals_' . date('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($journals) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, ['ID', 'Name', 'Slug', 'Short Name', 'P-ISSN', 'E-ISSN', 'Status', 'Created At']);

            foreach ($journals as $j) {
                fputcsv($file, [
                    $j->id,
                    $j->name,
                    $j->slug,
                    $j->short_name ?? '-',
                    $j->issn_p ?? '-',
                    $j->issn_e ?? '-',
                    $j->is_active ? 'Active' : 'Inactive',
                    $j->created_at?->format('Y-m-d H:i:s') ?? '-'
                ]);
            }
            fclose($file);
        }, $fileName, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }
}
