<?php

namespace Modules\Journals\Services;

use Illuminate\Support\Facades\DB;
use Modules\Journals\Models\JournalEditorialBoard;

class EditorialBoardService
{
    /**
     * Add member to editorial board.
     */
    public function addMember(array $data): JournalEditorialBoard
    {
        return DB::transaction(function () use ($data) {
            $data['is_active'] = isset($data['is_active']) ? (bool) $data['is_active'] : true;
            $data['order_no'] = isset($data['order_no']) ? (int) $data['order_no'] : 0;
            return JournalEditorialBoard::create($data);
        });
    }

    /**
     * Update editorial board member.
     */
    public function updateMember(JournalEditorialBoard $member, array $data): JournalEditorialBoard
    {
        return DB::transaction(function () use ($member, $data) {
            $data['is_active'] = isset($data['is_active']) ? (bool) $data['is_active'] : false;
            $member->update($data);
            return $member;
        });
    }

    /**
     * Get paginated & filtered data for KTUI DataTable.
     */
    public function getDatatableData(\Illuminate\Http\Request $request): array
    {
        $query = JournalEditorialBoard::with(['journal', 'user']);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('role', 'like', '%' . $search . '%')
                  ->orWhere('affiliation', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhereHas('journal', function ($jq) use ($search) {
                      $jq->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        if ($request->filled('journal_id')) {
            $query->where('journal_id', $request->get('journal_id'));
        }

        if ($request->filled('sortOrder') && $request->filled('sortField')) {
            $query->orderBy($request->get('sortField'), $request->get('sortOrder'));
        } else {
            $query->orderBy('order_no', 'asc')->latest();
        }

        $totalRecords = JournalEditorialBoard::count();
        $filteredRecords = $query->count();

        $page = (int) $request->get('page', 1);
        $size = (int) $request->get('size', 10);
        $offset = ($page - 1) * $size;

        $data = $query->skip($offset)->take($size)->get();

        $data->transform(function ($member) {
            $member->display_name = $member->display_name;
            $member->display_email = $member->display_email;
            $member->journal_name = $member->journal?->name ?? '-';
            return $member;
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
     * Bulk delete selected editorial board members.
     */
    public function bulkDeleteMembers(array $ids): array
    {
        if (empty($ids)) {
            return ['success' => false, 'message' => __('journals::app.no_eligible_items'), 'code' => 422];
        }

        JournalEditorialBoard::whereIn('id', $ids)->delete();
        return ['success' => true, 'message' => count($ids) . ' member(s) deleted successfully.', 'code' => 200];
    }

    /**
     * Export editorial board data to UTF-8 BOM CSV.
     */
    public function exportMembers(?string $search): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $query = JournalEditorialBoard::with(['journal', 'user']);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('role', 'like', '%' . $search . '%')
                  ->orWhere('affiliation', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $members = $query->orderBy('order_no', 'asc')->get();
        $fileName = 'editorial_board_' . date('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($members) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, ['ID', 'Journal', 'Name', 'Role', 'Affiliation', 'Email', 'Order No', 'Status', 'Created At']);

            foreach ($members as $m) {
                fputcsv($file, [
                    $m->id,
                    $m->journal?->name ?? '-',
                    $m->display_name,
                    $m->role,
                    $m->affiliation ?? '-',
                    $m->display_email,
                    $m->order_no,
                    $m->is_active ? 'Active' : 'Inactive',
                    $m->created_at?->format('Y-m-d H:i:s') ?? '-'
                ]);
            }
            fclose($file);
        }, $fileName, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }
}
