@extends('layouts.main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('journals.publishing-hub') }}
@endsection

@section('content')
<div class="space-y-6 w-full">

    <!-- Hero / Header Banner -->
    <div class="kt-card bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white rounded-2xl shadow-xl overflow-hidden border border-slate-800">
        <div class="kt-card-body p-6 md:p-8 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="flex items-center gap-2.5 flex-wrap">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-primary/20 text-primary-300 border border-primary/30">
                        <i class="ki-filled ki-rocket text-sm"></i> Alur Penerbitan Jurnal Terpadu
                    </span>
                    @if($selectedJournal)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $selectedJournal->is_active ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-rose-500/20 text-rose-300 border border-rose-500/30' }}">
                            {{ $selectedJournal->is_active ? 'Jurnal Aktif' : 'Jurnal Nonaktif' }}
                        </span>
                    @endif
                </div>
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-white">
                    Pusat Penerbitan & Alur Kerja Jurnal
                </h1>
                <p class="text-slate-300 text-sm max-w-2xl leading-relaxed">
                    Satu antarmuka terpadu untuk memandu seluruh siklus penerbitan jurnal: mulai dari konfigurasi profil jurnal, susunan dewan redaksi, pembuatan edisi terbitan, evaluasi naskah hingga publikasi resmi ke portal publik.
                </p>
            </div>

            <!-- Journal Switcher & Primary Action -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 shrink-0">
                @if($journals->isNotEmpty())
                    <form method="GET" action="{{ route('journals.publishing-hub') }}" class="flex items-center">
                        <div class="relative w-full sm:w-auto">
                            <select name="journal_id" onchange="this.form.submit()" class="kt-select bg-slate-800/90 text-white border-slate-700 hover:border-slate-600 rounded-xl text-xs py-2.5 px-3.5 pr-8 focus:ring-primary focus:border-primary">
                                @foreach($journals as $j)
                                    <option value="{{ $j->id }}" {{ $selectedJournal && $selectedJournal->id === $j->id ? 'selected' : '' }}>
                                        {{ $j->name }} ({{ $j->short_name ?? $j->slug }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                @endif

                @can('journals.create')
                    <a href="{{ route('journals.create') }}" class="kt-btn kt-btn-sm kt-btn-primary text-white shadow-md hover:shadow-primary/30 transition">
                        <i class="ki-filled ki-plus text-white"></i> Buat Jurnal Baru
                    </a>
                @endcan
            </div>
        </div>
    </div>

    @if(!$selectedJournal)
        <!-- Empty State jika belum ada jurnal -->
        <div class="kt-card text-center p-12 space-y-4">
            <div class="inline-flex items-center justify-center size-20 rounded-full bg-primary/10 text-primary mx-auto">
                <i class="ki-filled ki-book text-4xl"></i>
            </div>
            <h2 class="text-xl font-bold text-gray-900">Belum Ada Jurnal Ilmiah yang Terdaftar</h2>
            <p class="text-gray-500 text-sm max-w-md mx-auto">
                Untuk memulai alur penerbitan, silakan buat data master jurnal ilmiah terlebih dahulu dengan mengisi judul, ISSN, dan dewan redaksi.
            </p>
            <div>
                <a href="{{ route('journals.create') }}" class="kt-btn kt-btn-primary text-white">
                    <i class="ki-filled ki-plus text-white"></i> Inisiasi Jurnal Pertama Anda
                </a>
            </div>
        </div>
    @else
        <!-- Indikator Kemajuan / Publishing Readiness Progress Card -->
        <div class="kt-card p-6 border border-gray-150 shadow-sm space-y-5">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-lg font-bold text-gray-900">Indikator Kesiapan Penerbitan</h2>
                        <span class="kt-badge kt-badge-sm {{ $stats['progress_percentage'] === 100 ? 'kt-badge-light-success' : 'kt-badge-light-primary' }} font-bold">
                            {{ $stats['progress_percentage'] }}% Lengkap
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        Menampilkan kelengkapan 5 pilar syarat penerbitan untuk <strong>{{ $selectedJournal->name }}</strong>.
                    </p>
                </div>

                <!-- KPI Quick Counters -->
                <div class="flex items-center gap-4 text-xs">
                    <div class="text-center px-3 py-1.5 rounded-lg bg-gray-50 border border-gray-200">
                        <span class="text-gray-500 block">Tim Redaksi</span>
                        <span class="font-bold text-gray-800">{{ $stats['editorial_count'] }} Orang</span>
                    </div>
                    <div class="text-center px-3 py-1.5 rounded-lg bg-gray-50 border border-gray-200">
                        <span class="text-gray-500 block">Total Edisi</span>
                        <span class="font-bold text-gray-800">{{ $stats['issues_count'] }} Issue</span>
                    </div>
                    <div class="text-center px-3 py-1.5 rounded-lg bg-gray-50 border border-gray-200">
                        <span class="text-gray-500 block">Naskah Diterima</span>
                        <span class="font-bold text-emerald-600">{{ $stats['accepted_count'] }} Naskah</span>
                    </div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                <div class="bg-gradient-to-r from-primary to-emerald-500 h-2.5 rounded-full transition-all duration-500" style="width: {{ $stats['progress_percentage'] }}%"></div>
            </div>

            <!-- 5 Steps Stepper Header -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 pt-2">
                <!-- Step 1 -->
                <a href="#step-1" class="p-3 rounded-xl border {{ $stats['step_completed'][1] ? 'border-emerald-200 bg-emerald-50/50 hover:bg-emerald-50' : 'border-gray-200 bg-gray-50/50 hover:bg-gray-100' }} transition flex items-center gap-3">
                    <span class="size-8 rounded-full flex items-center justify-center font-bold text-xs shrink-0 {{ $stats['step_completed'][1] ? 'bg-emerald-500 text-white' : 'bg-gray-200 text-gray-600' }}">
                        @if($stats['step_completed'][1]) <i class="ki-filled ki-check text-white text-xs"></i> @else 1 @endif
                    </span>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-gray-800 truncate">1. Profil Jurnal</p>
                        <span class="text-[10px] {{ $stats['step_completed'][1] ? 'text-emerald-700 font-medium' : 'text-gray-500' }}">
                            {{ $stats['step_completed'][1] ? 'Sudah Aktif' : 'Perlu Dilengkapi' }}
                        </span>
                    </div>
                </a>

                <!-- Step 2 -->
                <a href="#step-2" class="p-3 rounded-xl border {{ $stats['step_completed'][2] ? 'border-emerald-200 bg-emerald-50/50 hover:bg-emerald-50' : 'border-amber-200 bg-amber-50/50 hover:bg-amber-50' }} transition flex items-center gap-3">
                    <span class="size-8 rounded-full flex items-center justify-center font-bold text-xs shrink-0 {{ $stats['step_completed'][2] ? 'bg-emerald-500 text-white' : 'bg-amber-500 text-white' }}">
                        @if($stats['step_completed'][2]) <i class="ki-filled ki-check text-white text-xs"></i> @else 2 @endif
                    </span>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-gray-800 truncate">2. Dewan Redaksi</p>
                        <span class="text-[10px] {{ $stats['step_completed'][2] ? 'text-emerald-700 font-medium' : 'text-amber-700 font-medium' }}">
                            {{ $stats['editorial_count'] }} Anggota
                        </span>
                    </div>
                </a>

                <!-- Step 3 -->
                <a href="#step-3" class="p-3 rounded-xl border {{ $stats['step_completed'][3] ? 'border-emerald-200 bg-emerald-50/50 hover:bg-emerald-50' : 'border-amber-200 bg-amber-50/50 hover:bg-amber-50' }} transition flex items-center gap-3">
                    <span class="size-8 rounded-full flex items-center justify-center font-bold text-xs shrink-0 {{ $stats['step_completed'][3] ? 'bg-emerald-500 text-white' : 'bg-amber-500 text-white' }}">
                        @if($stats['step_completed'][3]) <i class="ki-filled ki-check text-white text-xs"></i> @else 3 @endif
                    </span>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-gray-800 truncate">3. Edisi / Issue</p>
                        <span class="text-[10px] {{ $stats['step_completed'][3] ? 'text-emerald-700 font-medium' : 'text-amber-700 font-medium' }}">
                            {{ $targetIssue ? 'Vol. ' . $targetIssue->volume . ' No. ' . $targetIssue->number : 'Belum Ada' }}
                        </span>
                    </div>
                </a>

                <!-- Step 4 -->
                <a href="#step-4" class="p-3 rounded-xl border {{ $stats['step_completed'][4] ? 'border-emerald-200 bg-emerald-50/50 hover:bg-emerald-50' : 'border-amber-200 bg-amber-50/50 hover:bg-amber-50' }} transition flex items-center gap-3">
                    <span class="size-8 rounded-full flex items-center justify-center font-bold text-xs shrink-0 {{ $stats['step_completed'][4] ? 'bg-emerald-500 text-white' : 'bg-amber-500 text-white' }}">
                        @if($stats['step_completed'][4]) <i class="ki-filled ki-check text-white text-xs"></i> @else 4 @endif
                    </span>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-gray-800 truncate">4. Review Naskah</p>
                        <span class="text-[10px] {{ $stats['step_completed'][4] ? 'text-emerald-700 font-medium' : 'text-amber-700 font-medium' }}">
                            {{ $stats['accepted_count'] }} Accepted
                        </span>
                    </div>
                </a>

                <!-- Step 5 -->
                <a href="#step-5" class="p-3 rounded-xl border {{ $stats['step_completed'][5] ? 'border-emerald-200 bg-emerald-50/50 hover:bg-emerald-50' : 'border-gray-200 bg-gray-50/50 hover:bg-gray-100' }} transition flex items-center gap-3">
                    <span class="size-8 rounded-full flex items-center justify-center font-bold text-xs shrink-0 {{ $stats['step_completed'][5] ? 'bg-emerald-500 text-white' : 'bg-gray-300 text-gray-700' }}">
                        @if($stats['step_completed'][5]) <i class="ki-filled ki-check text-white text-xs"></i> @else 5 @endif
                    </span>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-gray-800 truncate">5. Terbit Publik</p>
                        <span class="text-[10px] {{ $stats['step_completed'][5] ? 'text-emerald-700 font-medium' : 'text-gray-500' }}">
                            {{ $targetIssue && $targetIssue->is_published ? 'Terbit' : 'Belum Rilis' }}
                        </span>
                    </div>
                </a>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAHAP 1: Profil & Identitas Jurnal -->
        <!-- ========================================== -->
        <div id="step-1" class="kt-card border border-gray-150 shadow-sm scroll-mt-20">
            <div class="kt-card-header min-h-16 py-4 flex-wrap gap-3">
                <div class="flex items-center gap-3">
                    <span class="size-9 rounded-xl bg-primary/10 text-primary flex items-center justify-center font-bold text-sm shrink-0">
                        <i class="ki-filled ki-book text-base"></i>
                    </span>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-base font-bold text-gray-900">Langkah 1: Profil & Identitas Jurnal</h3>
                            @if($selectedJournal->is_active)
                                <span class="kt-badge kt-badge-sm kt-badge-light-success">Aktif</span>
                            @else
                                <span class="kt-badge kt-badge-sm kt-badge-light-danger">Nonaktif</span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500">Konfigurasi metadata utama jurnal ilmiah, nomor ISSN resmi, dan panduan kepenulisan.</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('website.journals.show', $selectedJournal->slug) }}" target="_blank" class="kt-btn kt-btn-xs kt-btn-outline">
                        <i class="ki-filled ki-external-link"></i> Portal Publik
                    </a>
                    @can('journals.update')
                        <a href="{{ route('journals.edit', $selectedJournal->id) }}" class="kt-btn kt-btn-xs kt-btn-primary text-white">
                            <i class="ki-filled ki-pencil text-white"></i> Ubah Data Jurnal
                        </a>
                    @endcan
                </div>
            </div>
            <div class="kt-card-body p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-xs bg-gray-50/70 p-4 rounded-xl border border-gray-200">
                    <div>
                        <span class="text-gray-400 uppercase font-semibold block mb-0.5">Nama Resmi</span>
                        <span class="font-bold text-gray-900 text-sm">{{ $selectedJournal->name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 uppercase font-semibold block mb-0.5">Nama Singkat / Slug</span>
                        <span class="font-mono text-gray-800">{{ $selectedJournal->short_name ?: '-' }} (<code>{{ $selectedJournal->slug }}</code>)</span>
                    </div>
                    <div>
                        <span class="text-gray-400 uppercase font-semibold block mb-0.5">P-ISSN (Cetak)</span>
                        <span class="font-mono text-gray-800">{{ $selectedJournal->issn_p ?: '-' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 uppercase font-semibold block mb-0.5">E-ISSN (Online)</span>
                        <span class="font-mono text-gray-800">{{ $selectedJournal->issn_e ?: '-' }}</span>
                    </div>
                </div>

                @if($selectedJournal->description)
                    <div class="mt-4 text-xs text-gray-600 line-clamp-2">
                        <strong class="text-gray-700">Deskripsi:</strong> {{ $selectedJournal->description }}
                    </div>
                @endif
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAHAP 2: Dewan Redaksi & Tim Penelaah -->
        <!-- ========================================== -->
        <div id="step-2" class="kt-card border border-gray-150 shadow-sm scroll-mt-20">
            <div class="kt-card-header min-h-16 py-4 flex-wrap gap-3">
                <div class="flex items-center gap-3">
                    <span class="size-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm shrink-0">
                        <i class="ki-filled ki-people text-base"></i>
                    </span>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-base font-bold text-gray-900">Langkah 2: Susunan Dewan Redaksi & Penelaah</h3>
                            <span class="kt-badge kt-badge-sm {{ $stats['editorial_count'] > 0 ? 'kt-badge-light-success' : 'kt-badge-light-warning' }}">
                                {{ $stats['editorial_count'] }} Anggota
                            </span>
                        </div>
                        <p class="text-xs text-gray-500">Struktur pengelola ilmiah: Editor-in-Chief, Managing Editor, Section Editor, dan Reviewer.</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('editorial-boards.index') }}" class="kt-btn kt-btn-xs kt-btn-outline">
                        <i class="ki-filled ki-setting-4"></i> Kelola Lengkap
                    </a>
                    @can('journals.update')
                        <button type="button" onclick="openAddMemberModal()" class="kt-btn kt-btn-xs kt-btn-success text-white">
                            <i class="ki-filled ki-plus text-white"></i> + Anggota Redaksi
                        </button>
                    @endcan
                </div>
            </div>
            <div class="kt-card-body p-0">
                @if($editorialBoards->isEmpty())
                    <div class="p-6 text-center text-xs text-gray-500">
                        <i class="ki-filled ki-information-2 text-2xl text-amber-500 mb-2 block"></i>
                        Belum ada susunan dewan redaksi untuk jurnal ini. Klik <strong>+ Anggota Redaksi</strong> untuk menambahkan Editor-in-Chief atau Section Editor.
                    </div>
                @else
                    <div class="kt-table-wrapper kt-scrollable">
                        <table class="kt-table text-xs">
                            <thead>
                                <tr>
                                    <th class="w-12 text-center">#</th>
                                    <th>Nama Anggota</th>
                                    <th>Peran Redaksi</th>
                                    <th>Afiliasi / Institusi</th>
                                    <th>Email</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($editorialBoards as $index => $board)
                                    <tr>
                                        <td class="text-center font-medium text-gray-500">{{ $index + 1 }}</td>
                                        <td class="font-bold text-gray-900">
                                            {{ $board->user?->name ?? $board->name }}
                                        </td>
                                        <td>
                                            @if($board->role === 'Editor-in-Chief')
                                                <span class="kt-badge kt-badge-sm kt-badge-light-primary font-bold">Editor-in-Chief</span>
                                            @elseif(str_contains(strtolower($board->role), 'managing'))
                                                <span class="kt-badge kt-badge-sm kt-badge-light-info">Managing Editor</span>
                                            @elseif(str_contains(strtolower($board->role), 'section'))
                                                <span class="kt-badge kt-badge-sm kt-badge-light-warning">Section Editor</span>
                                            @else
                                                <span class="kt-badge kt-badge-sm kt-badge-light-secondary">{{ $board->role }}</span>
                                            @endif
                                        </td>
                                        <td class="text-gray-600">{{ $board->affiliation ?: '-' }}</td>
                                        <td class="text-gray-600 font-mono">{{ $board->email ?? $board->user?->email ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAHAP 3: Pembuatan Edisi / Issue Terbitan -->
        <!-- ========================================== -->
        <div id="step-3" class="kt-card border border-gray-150 shadow-sm scroll-mt-20">
            <div class="kt-card-header min-h-16 py-4 flex-wrap gap-3">
                <div class="flex items-center gap-3">
                    <span class="size-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-sm shrink-0">
                        <i class="ki-filled ki-copy text-base"></i>
                    </span>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-base font-bold text-gray-900">Langkah 3: Pembuatan Edisi / Issue Terbitan</h3>
                            @if($targetIssue)
                                <span class="kt-badge kt-badge-sm {{ $targetIssue->is_published ? 'kt-badge-light-success' : 'kt-badge-light-warning' }}">
                                    {{ $targetIssue->is_published ? 'Terbit (Published)' : 'Draf Persiapan' }}
                                </span>
                            @else
                                <span class="kt-badge kt-badge-sm kt-badge-light-danger">Belum Dibuat</span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500">Siapkan wadah nomor publikasi (Volume, Nomor, Tahun) yang akan dirilis.</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    @if($issues->count() > 1)
                        <form method="GET" action="{{ route('journals.publishing-hub') }}" class="flex items-center gap-1.5">
                            <input type="hidden" name="journal_id" value="{{ $selectedJournal->id }}" />
                            <select name="issue_id" onchange="this.form.submit()" class="kt-select text-xs py-1 px-2.5 rounded-lg border-gray-300">
                                @foreach($issues as $iss)
                                    <option value="{{ $iss->id }}" {{ $targetIssue && $targetIssue->id === $iss->id ? 'selected' : '' }}>
                                        Vol. {{ $iss->volume }} No. {{ $iss->number }} ({{ $iss->publication_year }}) - {{ $iss->is_published ? 'Published' : 'Draft' }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    @endif

                    @can('issues.create')
                        <a href="{{ route('issues.create', ['journal_id' => $selectedJournal->id]) }}" class="kt-btn kt-btn-xs kt-btn-primary text-white">
                            <i class="ki-filled ki-plus text-white"></i> + Buat Issue Baru
                        </a>
                    @endcan
                </div>
            </div>
            <div class="kt-card-body p-6">
                @if(!$targetIssue)
                    <div class="text-center p-6 bg-amber-50/50 rounded-xl border border-amber-200">
                        <i class="ki-filled ki-copy text-2xl text-amber-500 mb-2 block"></i>
                        <p class="font-bold text-gray-800 text-sm">Belum Ada Edisi / Issue untuk Jurnal Ini</p>
                        <p class="text-gray-500 text-xs mt-1 mb-4">Buat nomor issue target terbitan (misalnya: Volume 1 Nomor 1 Tahun {{ date('Y') }}) untuk menampung artikel yang akan diterbitkan.</p>
                        <a href="{{ route('issues.create', ['journal_id' => $selectedJournal->id]) }}" class="kt-btn kt-btn-xs kt-btn-primary text-white">
                            <i class="ki-filled ki-plus text-white"></i> Buat Issue Terbitan Pertama
                        </a>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 p-4 rounded-xl bg-gray-50/70 border border-gray-200 text-xs mb-3">
                        <div>
                            <span class="text-gray-400 uppercase font-semibold block mb-0.5">Judul Issue Terbitan</span>
                            <span class="font-bold text-gray-900 text-sm">{{ $targetIssue->title }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 uppercase font-semibold block mb-0.5">Penomoran Berkala</span>
                            <span class="font-bold text-gray-800">Volume {{ $targetIssue->volume }}, Nomor {{ $targetIssue->number }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 uppercase font-semibold block mb-0.5">Tahun Terbitan</span>
                            <span class="font-bold text-gray-800">{{ $targetIssue->publication_year }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 uppercase font-semibold block mb-0.5">Jumlah Artikel Terplot</span>
                            <span class="font-bold text-primary">{{ $issueArticles->count() }} Artikel</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-xs pt-1">
                        <span class="text-gray-500">
                            Status Edisi: <strong>{{ $targetIssue->is_published ? 'Telah Dipublikasikan (' . ($targetIssue->published_at ? $targetIssue->published_at->format('d M Y') : '') . ')' : 'Masih dalam Persiapan (Draf)' }}</strong>
                        </span>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('issues.show', $targetIssue->id) }}" class="kt-btn kt-btn-xs kt-btn-outline">
                                <i class="ki-filled ki-eye"></i> Detail Lengkap Issue
                            </a>
                            <a href="{{ route('issues.edit', $targetIssue->id) }}" class="kt-btn kt-btn-xs kt-btn-outline">
                                <i class="ki-filled ki-pencil"></i> Ubah Issue
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAHAP 4: Naskah Masuk & Penelaahan Redaksi -->
        <!-- ========================================== -->
        <div id="step-4" class="kt-card border border-gray-150 shadow-sm scroll-mt-20">
            <div class="kt-card-header min-h-16 py-4 flex-wrap gap-3">
                <div class="flex items-center gap-3">
                    <span class="size-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm shrink-0">
                        <i class="ki-filled ki-cloud-change text-base"></i>
                    </span>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-base font-bold text-gray-900">Langkah 4: Naskah Masuk & Penelaahan (Review Workflow)</h3>
                            <span class="kt-badge kt-badge-sm kt-badge-light-info">
                                {{ $stats['submissions_count'] }} Naskah Masuk
                            </span>
                        </div>
                        <p class="text-xs text-gray-500">Evaluasi naskah oleh editor & mitra bestari hingga naskah berstatus Diterima (Accepted) dan siap dijadwalkan.</p>
                    </div>
                </div>
                <div>
                    <a href="{{ route('editor.submissions.index') }}" class="kt-btn kt-btn-xs kt-btn-primary text-white">
                        <i class="ki-filled ki-verify text-white"></i> Ruang Kerja Redaksi (Editor Workspace)
                    </a>
                </div>
            </div>
            <div class="kt-card-body p-6 space-y-4">
                <!-- Status Barometer -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                    <div class="p-3 rounded-xl bg-blue-50 border border-blue-150 text-blue-900">
                        <span class="text-blue-600 block text-[11px] font-medium">Naskah Masuk (Pending)</span>
                        <span class="text-xl font-extrabold">{{ $submissions->where('status', 'submitted')->count() }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-amber-50 border border-amber-150 text-amber-900">
                        <span class="text-amber-600 block text-[11px] font-medium">Sedang Ditelaah / Revisi</span>
                        <span class="text-xl font-extrabold">{{ $stats['in_review_count'] }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-150 text-emerald-900">
                        <span class="text-emerald-600 block text-[11px] font-medium">Diterima (Accepted - Siap Terbit)</span>
                        <span class="text-xl font-extrabold">{{ $stats['accepted_count'] }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-purple-50 border border-purple-150 text-purple-900">
                        <span class="text-purple-600 block text-[11px] font-medium">Sudah Terbit Publik</span>
                        <span class="text-xl font-extrabold">{{ $stats['published_articles_count'] }}</span>
                    </div>
                </div>

                <!-- Naskah Accepted yang Siap Dimasukkan ke Issue -->
                <div class="pt-2">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-bold text-xs uppercase text-gray-700 tracking-wider flex items-center gap-1.5">
                            <i class="ki-filled ki-check-circle text-emerald-500"></i> Naskah Berstatus Accepted (Siap Dijadwalkan ke Issue)
                        </h4>
                        @if($readyToPublishSubmissions->isNotEmpty() && $targetIssue)
                            <span class="text-xs text-gray-500">Target: <strong>{{ $targetIssue->title }}</strong></span>
                        @endif
                    </div>

                    @if($readyToPublishSubmissions->isEmpty())
                        <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 text-xs text-gray-500 text-center">
                            @if($stats['accepted_count'] > 0 && $issueArticles->count() > 0)
                                Seluruh naskah yang berstatus Accepted telah dimasukkan ke dalam daftar terbitan pada Langkah 5 di bawah.
                            @else
                                Belum ada naskah yang berstatus <strong>Accepted</strong>. Silakan evaluasi naskah yang masuk di <a href="{{ route('editor.submissions.index') }}" class="text-primary underline font-medium">Ruang Kerja Redaksi</a>.
                            @endif
                        </div>
                    @else
                        <div class="border border-gray-200 rounded-xl overflow-hidden">
                            <table class="kt-table text-xs">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th>Judul Naskah</th>
                                        <th>Penulis Utama</th>
                                        <th>Tanggal Diterima</th>
                                        <th class="w-36 text-center">Aksi Cepat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($readyToPublishSubmissions as $sub)
                                        <tr>
                                            <td class="font-bold text-gray-900 leading-snug">
                                                {{ $sub->title }}
                                            </td>
                                            <td class="text-gray-600">
                                                {{ $sub->author?->name ?? '-' }}
                                            </td>
                                            <td class="text-gray-500 font-mono">
                                                {{ $sub->decision_at ? $sub->decision_at->format('d M Y') : '-' }}
                                            </td>
                                            <td class="text-center">
                                                @if($targetIssue)
                                                    <button type="button"
                                                        onclick="openAssignToIssueModal({{ $sub->id }}, '{{ addslashes($sub->title) }}')"
                                                        class="kt-btn kt-btn-xs kt-btn-success text-white shadow-xs font-semibold">
                                                        <i class="ki-filled ki-plus text-white"></i> Masukkan ke Issue
                                                    </button>
                                                @else
                                                    <span class="text-[10px] text-gray-400">Buat issue dahulu</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAHAP 5: Penataan Galley & Publikasi Akhir -->
        <!-- ========================================== -->
        <div id="step-5" class="kt-card border border-gray-150 shadow-sm scroll-mt-20">
            <div class="kt-card-header min-h-16 py-4 flex-wrap gap-3">
                <div class="flex items-center gap-3">
                    <span class="size-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-sm shrink-0">
                        <i class="ki-filled ki-rocket text-base"></i>
                    </span>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-base font-bold text-gray-900">Langkah 5: Penataan Akhir & Peluncuran Terbitan (Publish)</h3>
                            @if($targetIssue && $targetIssue->is_published)
                                <span class="kt-badge kt-badge-sm kt-badge-light-success font-bold">Resmi Terbit</span>
                            @else
                                <span class="kt-badge kt-badge-sm kt-badge-light-warning">Siap Rilis</span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500">Periksa nomor halaman naskah, registrasi DOI Crossref, dan luncurkan edisi ke publik secara terbuka.</p>
                    </div>
                </div>
                @if($targetIssue)
                    <div class="flex items-center gap-2">
                        <a href="{{ route('issues.export_crossref_xml', $targetIssue->id) }}" class="kt-btn kt-btn-xs kt-btn-outline" title="Unduh Crossref XML Schema 5.3 untuk pendaftaran DOI massal">
                            <i class="ki-filled ki-file-down"></i> Crossref XML
                        </a>
                        @if($targetIssue->is_published)
                            <a href="{{ route('website.issues.show', $targetIssue->id) }}" target="_blank" class="kt-btn kt-btn-xs kt-btn-primary text-white">
                                <i class="ki-filled ki-external-link text-white"></i> Buka Terbitan Publik
                            </a>
                        @endif
                    </div>
                @endif
            </div>
            <div class="kt-card-body p-6 space-y-5">
                @if(!$targetIssue)
                    <div class="p-4 bg-gray-50 rounded-xl text-center text-xs text-gray-500">
                        Silakan buat edisi / issue pada Langkah 3 terlebih dahulu untuk mengatur artikel dan menerbitkan edisi.
                    </div>
                @else
                    <!-- Daftar Artikel dalam Issue Ini -->
                    <div>
                        <h4 class="font-bold text-xs uppercase text-gray-700 tracking-wider mb-2 flex items-center gap-1.5">
                            <i class="ki-filled ki-document text-primary"></i> Daftar Artikel dalam {{ $targetIssue->title }} ({{ $issueArticles->count() }} Artikel)
                        </h4>

                        @if($issueArticles->isEmpty())
                            <div class="p-6 bg-amber-50/50 rounded-xl border border-amber-200 text-center text-xs text-amber-800">
                                <i class="ki-filled ki-information-2 text-xl text-amber-500 mb-1 block"></i>
                                Belum ada artikel yang dimasukkan ke dalam issue ini. Klik tombol <strong>+ Masukkan ke Issue</strong> pada Langkah 4 di atas untuk menambahkan naskah.
                            </div>
                        @else
                            <div class="border border-gray-200 rounded-xl overflow-hidden mb-4">
                                <table class="kt-table text-xs">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="w-12 text-center">#</th>
                                            <th>Judul Artikel & Penulis</th>
                                            <th class="w-28">Halaman</th>
                                            <th>Nomor DOI</th>
                                            <th class="w-24 text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($issueArticles as $index => $art)
                                            <tr>
                                                <td class="text-center font-bold text-gray-500">{{ $index + 1 }}</td>
                                                <td>
                                                    <p class="font-bold text-gray-900 leading-snug">{{ $art->title }}</p>
                                                    <span class="text-[11px] text-gray-500">
                                                        {{ $art->submission?->authors?->pluck('name')->implode(', ') ?: ($art->submission?->author?->name ?? '-') }}
                                                    </span>
                                                </td>
                                                <td class="font-mono text-gray-700">{{ $art->pages ?: '1-10' }}</td>
                                                <td class="font-mono text-gray-600 text-[11px]">{{ $art->doi ?: '-' }}</td>
                                                <td class="text-center">
                                                    <div class="flex items-center justify-center gap-1">
                                                        <button type="button"
                                                            onclick="openEditArticleMetaModal({{ $art->id }}, '{{ addslashes($art->title) }}', '{{ $art->pages }}', '{{ $art->doi }}')"
                                                            class="kt-btn kt-btn-xs kt-btn-icon kt-btn-ghost text-primary"
                                                            title="Ubah Halaman & DOI">
                                                            <i class="ki-filled ki-pencil"></i>
                                                        </button>
                                                        <form method="POST" action="{{ route('issues.articles.destroy', [$targetIssue->id, $art->id]) }}" onsubmit="return confirm('Apakah Anda yakin ingin melepas artikel ini dari terbitan? Status naskah akan kembali ke Accepted.')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <input type="hidden" name="redirect_to" value="{{ url()->full() }}" />
                                                            <button type="submit" class="kt-btn kt-btn-xs kt-btn-icon kt-btn-ghost text-danger" title="Lepas dari Edisi">
                                                                <i class="ki-filled ki-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    <!-- Kotak Eksekusi Peluncuran Publikasi (Publish Trigger) -->
                    <div class="p-6 rounded-2xl {{ $targetIssue->is_published ? 'bg-emerald-50/80 border border-emerald-200' : 'bg-slate-900 text-white border border-slate-800' }} shadow-md flex flex-col md:flex-row items-center justify-between gap-6">
                        <div class="space-y-1 text-center md:text-left">
                            @if($targetIssue->is_published)
                                <div class="flex items-center gap-2 justify-center md:justify-start">
                                    <span class="size-6 rounded-full bg-emerald-500 text-white flex items-center justify-center text-xs">
                                        <i class="ki-filled ki-check text-xs"></i>
                                    </span>
                                    <h4 class="font-bold text-emerald-950 text-base">Edisi Ini Sudah Resmi Dipublikasikan ke Publik!</h4>
                                </div>
                                <p class="text-emerald-800 text-xs">
                                    Artikel dapat diakses, dibaca, dan diunduh oleh publik. Tanggal rilis: <strong>{{ $targetIssue->published_at ? $targetIssue->published_at->format('d F Y') : '-' }}</strong>.
                                </p>
                            @else
                                <h4 class="font-bold text-white text-base flex items-center gap-2 justify-center md:justify-start">
                                    <i class="ki-filled ki-rocket text-primary-400"></i> Siap Meluncurkan Terbitan Ini ke Portal Publik?
                                </h4>
                                <p class="text-slate-300 text-xs max-w-xl">
                                    Dengan mempublikasikan edisi ini, seluruh artikel di dalamnya akan otomatis tayang di arsip publik, DOI diaktifkan, dan metadata sitasi dapat diakses pembaca global.
                                </p>
                            @endif
                        </div>

                        <div class="flex items-center gap-3 shrink-0">
                            @if(!$targetIssue->is_published)
                                <form method="POST" action="{{ route('issues.toggle_publish', $targetIssue->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin mempublikasikan edisi ini sekarang? Edisi akan langsung dapat diakses oleh publik.')">
                                    @csrf
                                    <input type="hidden" name="redirect_to" value="{{ url()->full() }}" />
                                    <button type="submit"
                                        class="kt-btn kt-btn-success text-white font-bold px-6 py-3 text-xs md:text-sm shadow-lg hover:shadow-emerald-500/25 transition flex items-center gap-2 {{ $issueArticles->isEmpty() ? 'opacity-50 cursor-not-allowed' : '' }}"
                                        {{ $issueArticles->isEmpty() ? 'disabled' : '' }}>
                                        <i class="ki-filled ki-rocket text-white"></i> Publikasikan Edisi Ini Sekarang!
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('issues.toggle_publish', $targetIssue->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menarik kembali edisi ini ke status draf?')">
                                    @csrf
                                    <input type="hidden" name="redirect_to" value="{{ url()->full() }}" />
                                    <button type="submit" class="kt-btn kt-btn-xs kt-btn-outline text-amber-700 border-amber-300 hover:bg-amber-100">
                                        <i class="ki-filled ki-arrows-circle"></i> Tarik Kembali ke Draf
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

</div>

<!-- Modal 1: Tambah Anggota Dewan Redaksi Cepat -->
@if($selectedJournal)
<div id="addEditorialMemberModal" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl overflow-hidden border border-gray-150 animate-in fade-in">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gray-50">
            <h3 class="font-bold text-gray-900 text-sm">Tambah Anggota Dewan Redaksi</h3>
            <button type="button" onclick="closeAddMemberModal()" class="text-gray-400 hover:text-gray-700">
                <i class="ki-filled ki-cross text-base"></i>
            </button>
        </div>

        <form action="{{ route('editorial-boards.store') }}" method="POST" class="p-6 space-y-4 text-xs">
            @csrf
            <input type="hidden" name="journal_id" value="{{ $selectedJournal->id }}" />
            <input type="hidden" name="redirect_to" value="{{ url()->full() }}" />

            <div class="space-y-1">
                <label for="modal_user_id" class="kt-label text-xs font-semibold text-gray-700">Pilih User Terdaftar (Opsional)</label>
                <select name="user_id" id="modal_user_id" class="kt-select w-full text-xs">
                    <option value="">-- Bukan User Terdaftar / Input Manual --</option>
                    @foreach($availableUsers as $u)
                        <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1">
                <label for="modal_name" class="kt-label text-xs font-semibold text-gray-700">Nama Lengkap & Gelar <span class="text-danger">*</span></label>
                <input type="text" name="name" id="modal_name" placeholder="Contoh: Prof. Dr. Budi Santoso, Sp.A(K)" class="kt-input w-full text-xs" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label for="modal_role" class="kt-label text-xs font-semibold text-gray-700">Peran Redaksi <span class="text-danger">*</span></label>
                    <select name="role" id="modal_role" class="kt-select w-full text-xs" required>
                        <option value="Editor-in-Chief">Editor-in-Chief</option>
                        <option value="Managing Editor">Managing Editor</option>
                        <option value="Section Editor">Section Editor</option>
                        <option value="Editorial Board Member" selected>Anggota Tim Redaksi</option>
                        <option value="Reviewer">Mitra Bestari (Reviewer)</option>
                    </select>
                </div>

                <div class="space-y-1">
                    <label for="modal_order_no" class="kt-label text-xs font-semibold text-gray-700">Urutan Tampilan</label>
                    <input type="number" name="order_no" id="modal_order_no" value="1" min="1" class="kt-input w-full text-xs" />
                </div>
            </div>

            <div class="space-y-1">
                <label for="modal_affiliation" class="kt-label text-xs font-semibold text-gray-700">Afiliasi / Universitas</label>
                <input type="text" name="affiliation" id="modal_affiliation" placeholder="Contoh: Universitas Indonesia / YSDS" class="kt-input w-full text-xs" />
            </div>

            <div class="space-y-1">
                <label for="modal_email" class="kt-label text-xs font-semibold text-gray-700">Alamat Email</label>
                <input type="email" name="email" id="modal_email" placeholder="budi@institution.ac.id" class="kt-input w-full text-xs" />
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                <button type="button" onclick="closeAddMemberModal()" class="kt-btn kt-btn-xs kt-btn-outline">
                    Batal
                </button>
                <button type="submit" class="kt-btn kt-btn-xs kt-btn-primary text-white font-semibold px-4 py-2">
                    Simpan Anggota Redaksi
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<!-- Modal 2: Plot Masukkan Naskah Accepted ke Issue -->
@if($targetIssue)
<div id="assignToIssueModal" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl overflow-hidden border border-gray-150 animate-in fade-in">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gray-50">
            <h3 class="font-bold text-gray-900 text-sm">Plot Naskah ke Edisi Terbitan</h3>
            <button type="button" onclick="closeAssignToIssueModal()" class="text-gray-400 hover:text-gray-700">
                <i class="ki-filled ki-cross text-base"></i>
            </button>
        </div>

        <form id="assignToIssueForm" method="POST" class="p-6 space-y-4 text-xs">
            @csrf
            <input type="hidden" name="redirect_to" value="{{ url()->full() }}" />
            <input type="hidden" name="issue_id" value="{{ $targetIssue->id }}" />

            <div>
                <span class="text-[10px] uppercase font-semibold text-gray-500 block mb-1">Target Issue:</span>
                <p class="font-bold text-gray-800">{{ $targetIssue->title }} (Vol. {{ $targetIssue->volume }} No. {{ $targetIssue->number }})</p>
            </div>

            <div>
                <span class="text-[10px] uppercase font-semibold text-gray-500 block mb-1">Judul Naskah:</span>
                <p id="assignModalSubmissionTitle" class="font-bold text-gray-800 leading-snug line-clamp-2"></p>
            </div>

            <div class="space-y-1">
                <label for="assignModalPages" class="kt-label text-xs font-semibold text-gray-700">Rentang Halaman (Pages)</label>
                <input type="text" name="pages" id="assignModalPages" value="1-10" placeholder="Contoh: 1-12" class="kt-input w-full text-xs font-mono" />
            </div>

            <div class="space-y-1">
                <label for="assignModalDoi" class="kt-label text-xs font-semibold text-gray-700">Nomor DOI (Opsional / Otomatis)</label>
                <input type="text" name="doi" id="assignModalDoi" placeholder="Dibiarkan kosong untuk auto-generate" class="kt-input w-full text-xs font-mono" />
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                <button type="button" onclick="closeAssignToIssueModal()" class="kt-btn kt-btn-xs kt-btn-outline">
                    Batal
                </button>
                <button type="submit" class="kt-btn kt-btn-xs kt-btn-success text-white font-semibold px-4 py-2">
                    Plot ke Issue Sekarang
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 3: Edit Metadata Artikel di Issue (Pages & DOI) -->
<div id="editArticleMetaModal" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl overflow-hidden border border-gray-150 animate-in fade-in">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gray-50">
            <h3 class="font-bold text-gray-900 text-sm">Ubah Nomor Halaman & DOI Artikel</h3>
            <button type="button" onclick="closeEditArticleMetaModal()" class="text-gray-400 hover:text-gray-700">
                <i class="ki-filled ki-cross text-base"></i>
            </button>
        </div>

        <form id="editArticleMetaForm" method="POST" class="p-6 space-y-4 text-xs">
            @csrf
            @method('PUT')
            <input type="hidden" name="redirect_to" value="{{ url()->full() }}" />

            <div>
                <span class="text-[10px] uppercase font-semibold text-gray-500 block mb-1">Judul Artikel:</span>
                <p id="editArticleMetaTitle" class="font-bold text-gray-800 leading-snug line-clamp-2"></p>
            </div>

            <div class="space-y-1">
                <label for="editArticleMetaPages" class="kt-label text-xs font-semibold text-gray-700">Rentang Halaman (Pages)</label>
                <input type="text" name="pages" id="editArticleMetaPages" placeholder="Contoh: 1-15" class="kt-input w-full text-xs font-mono" />
            </div>

            <div class="space-y-1">
                <label for="editArticleMetaDoi" class="kt-label text-xs font-semibold text-gray-700">Nomor DOI</label>
                <input type="text" name="doi" id="editArticleMetaDoi" placeholder="Contoh: 10.1234/journal.2026.1.1.001" class="kt-input w-full text-xs font-mono" />
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                <button type="button" onclick="closeEditArticleMetaModal()" class="kt-btn kt-btn-xs kt-btn-outline">
                    Batal
                </button>
                <button type="submit" class="kt-btn kt-btn-xs kt-btn-primary text-white font-semibold px-4 py-2">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
    function openAddMemberModal() {
        const modal = document.getElementById('addEditorialMemberModal');
        if (modal) modal.classList.remove('hidden');
    }

    function closeAddMemberModal() {
        const modal = document.getElementById('addEditorialMemberModal');
        if (modal) modal.classList.add('hidden');
    }

    function openAssignToIssueModal(submissionId, title) {
        const form = document.getElementById('assignToIssueForm');
        if (form) {
            form.action = "{{ url('editor/submissions') }}/" + submissionId + "/publish-to-issue";
            document.getElementById('assignModalSubmissionTitle').textContent = title;
            document.getElementById('assignToIssueModal').classList.remove('hidden');
        }
    }

    function closeAssignToIssueModal() {
        const modal = document.getElementById('assignToIssueModal');
        if (modal) modal.classList.add('hidden');
    }

    function openEditArticleMetaModal(articleId, title, pages, doi) {
        const form = document.getElementById('editArticleMetaForm');
        if (form && @json($targetIssue ? $targetIssue->id : null)) {
            const targetIssueId = @json($targetIssue ? $targetIssue->id : null);
            form.action = "{{ url('issues') }}/" + targetIssueId + "/articles/" + articleId;
            document.getElementById('editArticleMetaTitle').textContent = title;
            document.getElementById('editArticleMetaPages').value = pages || '';
            document.getElementById('editArticleMetaDoi').value = doi || '';
            document.getElementById('editArticleMetaModal').classList.remove('hidden');
        }
    }

    function closeEditArticleMetaModal() {
        const modal = document.getElementById('editArticleMetaModal');
        if (modal) modal.classList.add('hidden');
    }
</script>
@endsection
