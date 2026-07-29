@extends('layouts.main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('journals.show', $journal) }}
@endsection

@section('content')
    <div class="grid w-full space-y-6">
        <!-- Header & Journal Info -->
        <div class="kt-card">
            <div class="kt-card-header min-h-16 flex-wrap py-5">
                <div class="flex items-center gap-3">
                    <h3 class="kt-card-title text-xl font-bold">{{ $journal->name }}</h3>
                    @if($journal->is_active)
                        <span class="kt-badge kt-badge-sm kt-badge-light-success">{{ __('journals::app.active') }}</span>
                    @else
                        <span class="kt-badge kt-badge-sm kt-badge-light-danger">{{ __('journals::app.inactive') }}</span>
                    @endif
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('journals.index') }}" class="kt-btn kt-btn-sm kt-btn-outline">
                        <i class="ki-filled ki-arrow-left"></i> {{ __('journals::app.back') }}
                    </a>
                    @can('journals.update')
                        <a href="{{ route('journals.edit', $journal->id) }}" class="kt-btn kt-btn-sm kt-btn-primary text-white">
                            <i class="ki-filled ki-pencil text-white"></i> {{ __('journals::app.edit_journal') }}
                        </a>
                    @endcan
                </div>
            </div>
            <div class="kt-card-body p-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm mb-6 pb-6 border-b border-gray-200">
                    <div>
                        <span class="text-xs text-gray-500 uppercase block font-semibold mb-1">{{ __('journals::app.short_name') }}</span>
                        <span class="font-medium text-gray-800">{{ $journal->short_name ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 uppercase block font-semibold mb-1">{{ __('journals::app.slug') }}</span>
                        <span class="font-mono text-gray-800">{{ $journal->slug }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 uppercase block font-semibold mb-1">{{ __('journals::app.issn_p') }}</span>
                        <span class="font-medium text-gray-800">{{ $journal->issn_p ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 uppercase block font-semibold mb-1">{{ __('journals::app.issn_e') }}</span>
                        <span class="font-medium text-gray-800">{{ $journal->issn_e ?? '-' }}</span>
                    </div>
                </div>

                @if($journal->description)
                    <div class="mb-4">
                        <h4 class="text-sm font-semibold text-gray-700 mb-1">{{ __('journals::app.description') }}</h4>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $journal->description }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Editorial Board Management Section -->
        <div class="kt-card">
            <div class="kt-card-header min-h-16 flex-wrap py-5">
                <div class="flex items-center gap-2.5">
                    <h3 class="kt-card-title">{{ __('journals::app.editorial_board') }}</h3>
                </div>
                <div>
                    @can('journals.update')
                        <button type="button" onclick="openAddMemberModal()" class="kt-btn kt-btn-sm kt-btn-success text-white">
                            <i class="ki-filled ki-plus text-white"></i> {{ __('journals::app.add_editorial_member') }}
                        </button>
                    @endcan
                </div>
            </div>
            <div class="kt-card-body p-0">
                <div class="kt-table-wrapper kt-scrollable">
                    <table class="kt-table">
                        <thead>
                            <tr>
                                <th scope="col" class="w-12 text-center">#</th>
                                <th scope="col" class="w-48">{{ __('journals::app.member_name') }}</th>
                                <th scope="col" class="w-36">{{ __('journals::app.role') }}</th>
                                <th scope="col" class="w-48">{{ __('journals::app.affiliation') }}</th>
                                <th scope="col" class="w-48">{{ __('journals::app.email') }}</th>
                                <th scope="col" class="w-20 text-center">{{ __('journals::app.order_no') }}</th>
                                <th scope="col" class="w-24 text-center">{{ __('journals::app.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($journal->editorialBoards as $index => $member)
                                <tr class="hover:bg-gray-50">
                                    <td class="text-center font-medium text-gray-500">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="flex flex-col">
                                            <span class="font-semibold text-gray-900">{{ $member->display_name }}</span>
                                            @if($member->user)
                                                <span class="text-xs text-primary-600">(Registered User)</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="kt-badge kt-badge-sm kt-badge-light-primary">{{ $member->role }}</span>
                                    </td>
                                    <td class="text-gray-700">{{ $member->affiliation ?? '-' }}</td>
                                    <td class="text-gray-700">{{ $member->display_email }}</td>
                                    <td class="text-center font-mono text-xs">{{ $member->order_no }}</td>
                                    <td class="text-center">
                                        @can('journals.update')
                                            <button type="button" onclick="deleteMember({{ $member->id }})" class="kt-btn kt-btn-sm kt-btn-icon kt-btn-ghost text-danger-600 hover:bg-danger-50" title="Delete">
                                                <i class="ki-filled ki-trash"></i>
                                            </button>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-6 text-gray-500">
                                        Belum ada anggota tim redaksi terdaftar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Form Tambah Member -->
    <div id="add_member_modal" class="fixed inset-0 z-50 hidden bg-black/40 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 class="font-bold text-lg text-gray-800">{{ __('journals::app.add_editorial_member') }}</h3>
                <button type="button" onclick="closeAddMemberModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="ki-filled ki-cross text-xl"></i>
                </button>
            </div>
            <form action="{{ route('editorial-boards.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="journal_id" value="{{ $journal->id }}">

                <div class="space-y-1.5">
                    <label class="kt-label font-medium text-gray-700">{{ __('journals::app.role') }} <span class="text-danger-600">*</span></label>
                    <select name="role" class="kt-select w-full" required>
                        <option value="Editor-in-Chief">{{ __('journals::app.editor_in_chief') }}</option>
                        <option value="Managing Editor">{{ __('journals::app.managing_editor') }}</option>
                        <option value="Section Editor">{{ __('journals::app.section_editor') }}</option>
                        <option value="Editorial Board Member" selected>{{ __('journals::app.editorial_board_member') }}</option>
                        <option value="International Advisory Board">{{ __('journals::app.advisory_board') }}</option>
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="kt-label font-medium text-gray-700">{{ __('journals::app.member_name') }} <span class="text-danger-600">*</span></label>
                    <input type="text" name="name" class="kt-input w-full" placeholder="Nama lengkap & gelar" required />
                </div>

                <div class="space-y-1.5">
                    <label class="kt-label font-medium text-gray-700">{{ __('journals::app.email') }}</label>
                    <input type="email" name="email" class="kt-input w-full" placeholder="email@domain.com" />
                </div>

                <div class="space-y-1.5">
                    <label class="kt-label font-medium text-gray-700">{{ __('journals::app.affiliation') }}</label>
                    <input type="text" name="affiliation" class="kt-input w-full" placeholder="misal: Universitas / Lembaga Riset" />
                </div>

                <div class="space-y-1.5">
                    <label class="kt-label font-medium text-gray-700">{{ __('journals::app.order_no') }}</label>
                    <input type="number" name="order_no" class="kt-input w-full" value="0" min="0" />
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                    <button type="button" onclick="closeAddMemberModal()" class="kt-btn kt-btn-outline">
                        {{ __('journals::app.cancel') }}
                    </button>
                    <button type="submit" class="kt-btn kt-btn-primary text-white">
                        <i class="ki-filled ki-check text-white"></i> {{ __('journals::app.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function openAddMemberModal() {
            document.getElementById('add_member_modal').classList.remove('hidden');
        }

        function closeAddMemberModal() {
            document.getElementById('add_member_modal').classList.add('hidden');
        }

        function deleteMember(id) {
            Swal.fire({
                title: "{{ __('journals::app.are_you_sure') }}",
                text: "{{ __('journals::app.delete_confirm_text') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                confirmButtonText: "{{ __('journals::app.yes_delete') }}"
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch("{{ route('editorial-boards.destroy', ':id') }}".replace(':id', id), {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        }
                    }).then(res => res.json()).then(data => {
                        if (data.success) {
                            Swal.fire("{{ __('journals::app.deleted') }}", data.message, 'success').then(() => location.reload());
                        } else {
                            Swal.fire("{{ __('journals::app.error') }}", data.message, 'error');
                        }
                    });
                }
            });
        }
    </script>
@endpush
