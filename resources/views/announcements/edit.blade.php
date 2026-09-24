@extends('layouts.main')

@section('breadcrumbs')
    <div class="flex items-center gap-2 text-xs text-gray-500 mb-4">
        <a href="{{ route('dashboard') }}" class="hover:text-gray-800 transition">Beranda</a>
        <span>/</span>
        <a href="{{ route('announcements.index') }}" class="hover:text-gray-800 transition">Pengumuman &amp; CFP</a>
        <span>/</span>
        <span class="font-semibold text-gray-800">Edit Pengumuman</span>
    </div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="kt-card p-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs space-y-6">
        <div class="border-b border-slate-100 pb-4">
            <h1 class="text-xl font-bold text-slate-900">Perbarui Pengumuman / Call for Papers</h1>
            <p class="text-xs text-slate-500 mt-1">Ubah rincian pengumuman, perpanjang tenggat waktu, atau kelola berkas lampiran.</p>
        </div>

        @if($errors->any())
            <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-xs text-red-700">
                <strong class="font-bold block mb-1">Perhatian:</strong>
                <ul class="list-disc pl-5 space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('announcements.update', $announcement->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            <!-- Judul -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Judul Pengumuman / Call for Papers <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $announcement->title) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-red-500 outline-hidden" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Kategori -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Kategori / Tipe <span class="text-red-500">*</span></label>
                    <select name="type" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-red-500 outline-hidden bg-white">
                        <option value="call_for_papers" {{ old('type', $announcement->type) === 'call_for_papers' ? 'selected' : '' }}>Call for Papers (CFP)</option>
                        <option value="announcement" {{ old('type', $announcement->type) === 'announcement' ? 'selected' : '' }}>Pengumuman Redaksi / Publikasi</option>
                        <option value="event" {{ old('type', $announcement->type) === 'event' ? 'selected' : '' }}>Agenda / Seminar / Workshop</option>
                    </select>
                </div>

                <!-- Jurnal Sasaran -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Jurnal Terkait (Opsional)</label>
                    <select name="journal_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-red-500 outline-hidden bg-white">
                        <option value="">-- Semua Jurnal (Pengumuman Global) --</option>
                        @foreach($journals as $j)
                            <option value="{{ $j->id }}" {{ old('journal_id', $announcement->journal_id) == $j->id ? 'selected' : '' }}>
                                {{ $j->short_name ?: $j->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Deadline -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Batas Waktu Pengiriman / Deadline (Khusus CFP)</label>
                    <input type="date" name="deadline" value="{{ old('deadline', $announcement->deadline ? $announcement->deadline->format('Y-m-d') : '') }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-red-500 outline-hidden bg-white" />
                </div>
                <div class="flex items-center gap-6 pt-6">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_pinned" value="1" {{ old('is_pinned', $announcement->is_pinned) ? 'checked' : '' }} class="rounded border-slate-300 text-red-600 focus:ring-red-500 size-4" />
                        <span class="text-xs font-bold text-slate-700">Sematkan ke Atas (Pin to Top)</span>
                    </label>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_published" value="1" {{ old('is_published', $announcement->is_published) ? 'checked' : '' }} class="rounded border-slate-300 text-green-600 focus:ring-green-500 size-4" />
                        <span class="text-xs font-bold text-slate-700">Status Tayang (Published)</span>
                    </label>
                </div>
            </div>

            <!-- Ringkasan Singkat -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Ringkasan Singkat (Lead Summary)</label>
                <textarea name="summary" rows="2" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-red-500 outline-hidden">{{ old('summary', $announcement->summary) }}</textarea>
            </div>

            <!-- Isi Lengkap -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Isi Lengkap Pengumuman <span class="text-red-500">*</span></label>
                <textarea name="content" rows="8" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-red-500 outline-hidden leading-relaxed">{{ old('content', $announcement->content) }}</textarea>
            </div>

            <!-- Banner & Attachment -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-100">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Banner Poster</label>
                    @if($announcement->banner_image)
                        <div class="mb-2">
                            <span class="text-[11px] text-slate-400">Berkas saat ini: {{ $announcement->banner_image }}</span>
                        </div>
                    @endif
                    <input type="file" name="banner_image" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200" />
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Lampiran Panduan</label>
                    @if($announcement->attachment_file)
                        <div class="mb-2">
                            <span class="text-[11px] text-slate-400">Berkas saat ini: {{ $announcement->attachment_file }}</span>
                        </div>
                    @endif
                    <input type="file" name="attachment_file" accept=".pdf,.docx,.doc" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200" />
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('announcements.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 font-bold text-xs transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs shadow-md transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
