<?php

namespace Modules\Journals\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Journals\Http\Requests\JournalRequest;
use Modules\Journals\Models\Journal;
use Modules\Journals\Services\JournalService;

class JournalsController extends Controller
{
    protected JournalService $journalService;

    public function __construct(JournalService $journalService)
    {
        $this->journalService = $journalService;
    }

    public function index()
    {
        $user = Auth::user();
        if (!$user || !$user->can('journals.read')) {
            abort(403, 'Sorry! You are not allowed to view journals.');
        }

        return view('journals::index');
    }

    public function dataForDatatables(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->can('journals.read')) {
            return response()->json(['message' => 'Sorry! You are not allowed to view journals.', 'success' => false], 403);
        }

        return response()->json($this->journalService->getDatatableData($request));
    }

    public function create()
    {
        $user = Auth::user();
        if (!$user || !$user->can('journals.create')) {
            abort(403, 'Sorry! You are not allowed to create a journal.');
        }

        return view('journals::create');
    }

    public function store(JournalRequest $request)
    {
        $user = Auth::user();
        if (!$user || !$user->can('journals.create')) {
            abort(403, 'Sorry! You are not allowed to create a journal.');
        }

        $journal = $this->journalService->createJournal($request->validated());

        return redirect()->route('journals.show', $journal->id)
            ->with('success', __('journals::app.created_success'));
    }

    public function show($id)
    {
        $user = Auth::user();
        if (!$user || !$user->can('journals.read')) {
            abort(403, 'Sorry! You are not allowed to view journal details.');
        }

        $journal = Journal::with(['editorialBoards.user'])->findOrFail($id);

        return view('journals::show', compact('journal'));
    }

    public function edit($id)
    {
        $user = Auth::user();
        if (!$user || !$user->can('journals.update')) {
            abort(403, 'Sorry! You are not allowed to edit journals.');
        }

        $journal = Journal::findOrFail($id);

        return view('journals::create', compact('journal'));
    }

    public function update(JournalRequest $request, $id)
    {
        $user = Auth::user();
        if (!$user || !$user->can('journals.update')) {
            abort(403, 'Sorry! You are not allowed to edit journals.');
        }

        $journal = Journal::findOrFail($id);
        $this->journalService->updateJournal($journal, $request->validated());

        return redirect()->route('journals.index')
            ->with('success', __('journals::app.updated_success'));
    }

    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user || !$user->can('journals.delete')) {
            return response()->json(['message' => 'Sorry! You are not allowed to delete journals.', 'success' => false], 403);
        }

        $result = $this->journalService->deleteJournal((int) $id);
        return response()->json(['message' => $result['message'], 'success' => $result['success']], $result['code']);
    }

    public function bulkDestroy(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->can('journals.delete')) {
            return response()->json(['message' => 'Sorry! You are not allowed to delete journals.', 'success' => false], 403);
        }

        $ids = $request->input('ids', []);
        $result = $this->journalService->bulkDeleteJournals($ids);
        return response()->json(['message' => $result['message'], 'success' => $result['success']], $result['code']);
    }

    public function export(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->can('journals.export')) {
            abort(403, 'Sorry! You are not allowed to export journals data.');
        }

        return $this->journalService->exportJournals($request->get('search'));
    }
}
