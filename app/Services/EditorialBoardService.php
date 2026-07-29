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
     * Delete editorial board member.
     */
    public function deleteMember(int $id): array
    {
        $member = JournalEditorialBoard::find($id);
        if (!$member) {
            return ['success' => false, 'message' => __('journals::app.error'), 'code' => 404];
        }

        $member->delete();
        return ['success' => true, 'message' => __('journals::app.deleted_success'), 'code' => 200];
    }
}
