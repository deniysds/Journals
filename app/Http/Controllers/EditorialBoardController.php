<?php

namespace Modules\Journals\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Journals\Http\Requests\EditorialBoardRequest;
use Modules\Journals\Models\JournalEditorialBoard;
use Modules\Journals\Services\EditorialBoardService;

class EditorialBoardController extends Controller
{
    protected EditorialBoardService $editorialBoardService;

    public function __construct(EditorialBoardService $editorialBoardService)
    {
        $this->editorialBoardService = $editorialBoardService;
    }

    public function store(EditorialBoardRequest $request)
    {
        $user = Auth::user();
        if (!$user || !$user->can('journals.update')) {
            abort(403, 'Sorry! You are not allowed to update editorial board.');
        }

        $member = $this->editorialBoardService->addMember($request->validated());

        return redirect()->route('journals.show', $member->journal_id)
            ->with('success', __('journals::app.created_success'));
    }

    public function update(EditorialBoardRequest $request, $id)
    {
        $user = Auth::user();
        if (!$user || !$user->can('journals.update')) {
            abort(403, 'Sorry! You are not allowed to update editorial board.');
        }

        $member = JournalEditorialBoard::findOrFail($id);
        $this->editorialBoardService->updateMember($member, $request->validated());

        return redirect()->route('journals.show', $member->journal_id)
            ->with('success', __('journals::app.updated_success'));
    }

    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user || !$user->can('journals.update')) {
            return response()->json(['message' => 'Sorry! You are not allowed to delete editorial members.', 'success' => false], 403);
        }

        $result = $this->editorialBoardService->deleteMember((int) $id);
        return response()->json(['message' => $result['message'], 'success' => $result['success']], $result['code']);
    }
}
