<?php

namespace Modules\Journals\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Journals\Models\Journal;
use Modules\Journals\Models\JournalAnnouncement;

class AdminAnnouncementController extends Controller
{
    /**
     * Display a listing of announcements and call for papers.
     */
    public function index(Request $request)
    {
        $query = JournalAnnouncement::with('journal');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('journal_id')) {
            $query->where('journal_id', $request->journal_id);
        }

        $announcements = $query->orderBy('is_pinned', 'desc')
            ->latest('created_at')
            ->paginate(10)
            ->withQueryString();

        $journals = Journal::where('is_active', true)->get();

        return view('journals::announcements.index', compact('announcements', 'journals'));
    }

    /**
     * Show the form for creating a new announcement/CFP.
     */
    public function create()
    {
        $journals = Journal::where('is_active', true)->get();
        return view('journals::announcements.create', compact('journals'));
    }

    /**
     * Store a newly created announcement in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'           => 'required|string|max:255',
            'journal_id'      => 'nullable|exists:journals,id',
            'type'            => 'required|in:call_for_papers,announcement,event',
            'summary'         => 'nullable|string|max:500',
            'content'         => 'required|string',
            'deadline'        => 'nullable|date',
            'is_pinned'       => 'nullable|boolean',
            'is_published'    => 'nullable|boolean',
            'banner_image'    => 'nullable|image|max:3072',
            'attachment_file' => 'nullable|file|mimes:pdf,docx,doc|max:10240',
        ]);

        $user = Auth::user();
        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(5);
        $validated['created_by'] = $user?->id;
        $validated['is_pinned'] = $request->boolean('is_pinned');
        $validated['is_published'] = $request->boolean('is_published', true);
        $validated['published_at'] = $validated['is_published'] ? now() : null;

        if ($request->hasFile('banner_image')) {
            $validated['banner_image'] = $request->file('banner_image')->store('announcements/banners', 'public');
        }

        if ($request->hasFile('attachment_file')) {
            $validated['attachment_file'] = $request->file('attachment_file')->store('announcements/docs', 'public');
        }

        $announcement = JournalAnnouncement::create($validated);

        return redirect()->route('announcements.index')
            ->with('success', 'Pengumuman / Call for Papers berhasil dipublikasikan.');
    }

    /**
     * Show the form for editing the specified announcement.
     */
    public function edit($id)
    {
        $announcement = JournalAnnouncement::findOrFail($id);
        $journals = Journal::where('is_active', true)->get();

        return view('journals::announcements.edit', compact('announcement', 'journals'));
    }

    /**
     * Update the specified announcement in storage.
     */
    public function update(Request $request, $id)
    {
        $announcement = JournalAnnouncement::findOrFail($id);

        $validated = $request->validate([
            'title'           => 'required|string|max:255',
            'journal_id'      => 'nullable|exists:journals,id',
            'type'            => 'required|in:call_for_papers,announcement,event',
            'summary'         => 'nullable|string|max:500',
            'content'         => 'required|string',
            'deadline'        => 'nullable|date',
            'is_pinned'       => 'nullable|boolean',
            'is_published'    => 'nullable|boolean',
            'banner_image'    => 'nullable|image|max:3072',
            'attachment_file' => 'nullable|file|mimes:pdf,docx,doc|max:10240',
        ]);

        $validated['is_pinned'] = $request->boolean('is_pinned');
        $validated['is_published'] = $request->boolean('is_published');
        $validated['updated_by'] = Auth::id();

        if ($request->hasFile('banner_image')) {
            if ($announcement->banner_image && Storage::disk('public')->exists($announcement->banner_image)) {
                Storage::disk('public')->delete($announcement->banner_image);
            }
            $validated['banner_image'] = $request->file('banner_image')->store('announcements/banners', 'public');
        }

        if ($request->hasFile('attachment_file')) {
            if ($announcement->attachment_file && Storage::disk('public')->exists($announcement->attachment_file)) {
                Storage::disk('public')->delete($announcement->attachment_file);
            }
            $validated['attachment_file'] = $request->file('attachment_file')->store('announcements/docs', 'public');
        }

        $announcement->update($validated);

        return redirect()->route('announcements.index')
            ->with('success', 'Pengumuman / Call for Papers berhasil diperbarui.');
    }

    /**
     * Remove the specified announcement from storage.
     */
    public function destroy($id)
    {
        $announcement = JournalAnnouncement::findOrFail($id);
        $announcement->delete();

        return redirect()->route('announcements.index')
            ->with('success', 'Pengumuman / Call for Papers berhasil dihapus.');
    }

    /**
     * Toggle published status of announcement.
     */
    public function toggleStatus($id)
    {
        $announcement = JournalAnnouncement::findOrFail($id);
        $announcement->update([
            'is_published' => !$announcement->is_published,
            'published_at' => !$announcement->is_published ? now() : $announcement->published_at,
        ]);

        return redirect()->route('announcements.index')
            ->with('success', 'Status publikasi pengumuman berhasil diubah.');
    }
}
