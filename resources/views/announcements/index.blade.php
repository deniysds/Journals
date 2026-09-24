@extends('layouts.main')

@section('breadcrumbs')
    <div class="flex items-center gap-2 text-xs text-gray-500 mb-4">
        <a href="{{ route('dashboard') }}" class="hover:text-gray-800 transition">Beranda</a>
        <span>/</span>
        <a href="{{ route('journals.index') }}" class="hover:text-gray-800 transition">Jurnal</a>
        <span>/</span>
        <span class="font-semibold text-gray-800">Pengumuman &amp; Call for Papers</span>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Top Action Banner -->
    <div class="kt-card p-6 bg-gradient-to-r from-slate-900 to-red-950 text-white shadow-md rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-red-500/20 text-red-300 text-xs font-bold uppercase tracking-wider mb-2">
                <i class="ki-filled ki-megaphone"></i> Publikasi &amp; Agenda Ilmiah
            </div>
            <h1 class="text-2xl font-black text-white">Manajemen Pengumuman &amp; Call for Papers</h1>
            <p class="text-slate-300 text-xs sm:text-sm mt-1">
                Kelola warta resmi, edisi khusus riset, dan ajakan penyerahan naskah ilmiah (*Call for Papers*) per jurnal.
            </p>
        </div>
        <a href="{{ route('announcements.create') }}" class="px-5 py-3 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs sm:text-sm shadow-md transition flex items-center justify-center gap-2 shrink-0">
            <i class="ki-filled ki-plus text-base"></i> Tambah Pengumuman / CFP
        </a>
    </div>

    <!-- Search & Filter Bar -->
    <div class="kt-card p-4 sm:p-5 bg-white rounded-2xl border border-slate-200/80 shadow-xs">
        <form action="{{ route('announcements.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div class="sm:col-span-2 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul atau ringkasan pengumuman..." class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-red-500 outline-hidden" />
                <i class="ki-filled ki-magnifier absolute left-3 top-3 text-slate-400"></i>
            </div>
            <div>
                <select name="type" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-red-500 outline-hidden bg-white">
                    <option value="">-- Semua Kategori --</option>
                    <option value="call_for_papers" {{ request('type') === 'call_for_papers' ? 'selected' : '' }}>Call for Papers (CFP)</option>
                    <option value="announcement" {{ request('type') === 'announcement' ? 'selected' : '' }}>Pengumuman Redaksi</option>
                    <option value="event" {{ request('type') === 'event' ? 'selected' : '' }}>Agenda / Seminar</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-black text-white font-bold text-xs transition">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'type', 'journal_id']))
                    <a href="{{ route('announcements.index') }}" class="py-2.5 px-3 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs transition flex items-center justify-center" title="Reset">
                        <i class="ki-filled ki-cross-circle"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Announcements Table -->
    <div class="kt-card bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 font-bold uppercase tracking-wider text-[11px] border-b border-slate-100">
                    <tr>
                        <th class="py-3.5 px-5">Judul Pengumuman</th>
                        <th class="py-3.5 px-4">Kategori</th>
                        <th class="py-3.5 px-4">Jurnal Sasaran</th>
                        <th class="py-3.5 px-4">Tenggat Waktu</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($announcements as $announcement)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-4 px-5">
                                <div class="space-y-1 max-w-md">
                                    <div class="flex items-center gap-2">
                                        @if($announcement->is_pinned)
                                            <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-800 text-[10px] font-bold inline-flex items-center gap-1">
                                                <i class="ki-filled ki-pin text-[10px]"></i> Disematkan
                                            </span>
                                        @endif
                                        <h4 class="font-bold text-slate-900 text-sm hover:text-red-600 transition">
                                            {{ $announcement->title }}
                                        </h4>
                                    </div>
                                    @if($announcement->summary)
                                        <p class="text-slate-500 text-[11px] line-clamp-1">
                                            {{ $announcement->summary }}
                                        </p>
                                    @endif
                                    <span class="text-[10px] text-slate-400 font-mono">
                                        Dibuat: {{ $announcement->created_at->format('d M Y') }}
                                    </span>
                                </div>
                            </td>
                            <td class="py-4 px-4 whitespace-nowrap">
                                @if($announcement->type === 'call_for_papers')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-purple-100 text-purple-700 border border-purple-200">
                                        Call for Papers
                                    </span>
                                @elseif($announcement->type === 'event')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700 border border-blue-200">
                                        Agenda / Event
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                        Pengumuman
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-4 whitespace-nowrap font-medium text-slate-800">
                                {{ $announcement->journal?->short_name ?: ($announcement->journal?->name ?? 'Semua Jurnal (Global)') }}
                            </td>
                            <td class="py-4 px-4 whitespace-nowrap">
                                @if($announcement->deadline)
                                    <span class="font-mono text-xs {{ $announcement->isDeadlinePassed() ? 'text-red-600 font-bold' : 'text-slate-700' }}">
                                        {{ $announcement->deadline->format('d M Y') }}
                                    </span>
                                    @if($announcement->isDeadlinePassed())
                                        <span class="block text-[9px] text-red-500 font-bold uppercase">Berakhir</span>
                                    @endif
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-center whitespace-nowrap">
                                <form action="{{ route('announcements.toggle', $announcement->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-2.5 py-1 rounded-full text-[10px] font-bold transition cursor-pointer {{ $announcement->is_published ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}" title="Klik untuk ubah status publikasi">
                                        <i class="ki-filled {{ $announcement->is_published ? 'ki-check-circle text-green-600' : 'ki-cross-circle text-slate-400' }}"></i>
                                        {{ $announcement->is_published ? 'Tayang' : 'Draf' }}
                                    </button>
                                </form>
                            </td>
                            <td class="py-4 px-5 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('announcements.edit', $announcement->id) }}" class="p-1.5 rounded-lg hover:bg-slate-100 text-slate-600 hover:text-slate-900 transition" title="Edit">
                                        <i class="ki-filled ki-pencil text-sm"></i>
                                    </a>
                                    <form action="{{ route('announcements.destroy', $announcement->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengumuman ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg hover:bg-red-50 text-slate-400 hover:text-red-600 transition cursor-pointer" title="Hapus">
                                            <i class="ki-filled ki-trash text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <i class="ki-filled ki-document text-4xl mb-2 text-slate-300 block"></i>
                                <p class="text-xs font-semibold text-slate-600">Belum ada pengumuman atau Call for Papers</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">Klik tombol Tambah Pengumuman di atas untuk membuat warta baru.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($announcements->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $announcements->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
