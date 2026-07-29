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

    public function index()
    {
        $user = Auth::user();
        if (!$user || !$user->can('journals.read')) {
            abort(403, 'Sorry! You are not allowed to view editorial boards.');
        }

        $journals = \Modules\Journals\Models\Journal::where('is_active', true)->get();

        return view('journals::editorial-boards.index', compact('journals'));
    }

    public function dataForDatatables(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->can('journals.read')) {
            return response()->json(['message' => 'Sorry! You are not allowed to view editorial boards.', 'success' => false], 403);
        }

        return response()->json($this->editorialBoardService->getDatatableData($request));
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

    public function bulkDestroy(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->can('journals.update')) {
            return response()->json(['message' => 'Sorry! You are not allowed to delete editorial members.', 'success' => false], 403);
        }

        $ids = $request->input('ids', []);
        $result = $this->editorialBoardService->bulkDeleteMembers($ids);
        return response()->json(['message' => $result['message'], 'success' => $result['success']], $result['code']);
    }

    public function export(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->can('journals.export')) {
            abort(403, 'Sorry! You are not allowed to export editorial boards data.');
        }

        return $this->editorialBoardService->exportMembers($request->get('search'));
    }
}
