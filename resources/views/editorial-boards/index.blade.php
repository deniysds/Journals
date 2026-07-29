@extends('layouts.main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('editorial-boards') }}
@endsection

@section('content')
    <div class="grid w-full space-y-5">
        <div class="kt-card">
            <div class="kt-card-header min-h-16 flex-wrap py-5">
                <div class="flex items-center gap-2.5">
                    <h3 class="kt-card-title">{{ __('journals::app.editorial_board') }}</h3>
                </div>
                <div class="flex items-center gap-2.5">
                    <input
                        type="text"
                        placeholder="{{ __('journals::app.search') }}"
                        class="kt-input sm:w-48"
                        id="search_members"
                        data-kt-datatable-search="#kt_datatable_editorial_boards"
                    />
                    <button type="button" class="kt-btn kt-btn-sm kt-btn-danger hidden" id="bulk_delete_btn" onclick="bulkDelete()">
                        <i class="ki-filled ki-trash text-white"></i> {{ __('journals::app.delete_selected') }} (<span id="selected_count">0</span>)
                    </button>
                    <a class="kt-btn kt-btn-sm kt-btn-outline" id="export_btn" href="{{ route('editorial-boards.export') }}" target="_blank">
                        <i class="ki-filled ki-file-down"></i> {{ __('journals::app.export') }}
                    </a>
                    @can('journals.update')
                        <button type="button" onclick="openAddMemberModal()" class="kt-btn kt-btn-sm kt-btn-primary text-white">
                            <i class="ki-filled ki-plus text-white"></i> {{ __('journals::app.add_editorial_member') }}
                        </button>
                    @endcan
                </div>
            </div>
            <div
                id="kt_datatable_editorial_boards"
                class="kt-card-table"
                data-kt-datatable-page-size="10"
                data-kt-datatable-state-save="true"
            >
                <div class="kt-table-wrapper kt-scrollable">
                    <table class="kt-table" data-kt-datatable-table="true">
                        <thead>
                            <tr>
                                <th scope="col" class="w-14 text-center" data-kt-datatable-column="select">
                                    <input class="kt-checkbox kt-checkbox-sm" id="check_all" data-kt-datatable-check="true" type="checkbox" />
                                </th>
                                <th scope="col" class="w-48" data-kt-datatable-column="name">
                                    <span class="kt-table-col"><span class="kt-table-col-label">{{ __('journals::app.member_name') }}</span><span class="kt-table-col-sort"></span></span>
                                </th>
                                <th scope="col" class="w-48" data-kt-datatable-column="journal">
                                    <span class="kt-table-col"><span class="kt-table-col-label">{{ __('journals::app.journal') }}</span><span class="kt-table-col-sort"></span></span>
                                </th>
                                <th scope="col" class="w-36" data-kt-datatable-column="role">
                                    <span class="kt-table-col"><span class="kt-table-col-label">{{ __('journals::app.role') }}</span><span class="kt-table-col-sort"></span></span>
                                </th>
                                <th scope="col" class="w-48" data-kt-datatable-column="affiliation">
                                    <span class="kt-table-col"><span class="kt-table-col-label">{{ __('journals::app.affiliation') }}</span><span class="kt-table-col-sort"></span></span>
                                </th>
                                <th scope="col" class="w-40" data-kt-datatable-column="email">
                                    <span class="kt-table-col"><span class="kt-table-col-label">{{ __('journals::app.email') }}</span><span class="kt-table-col-sort"></span></span>
                                </th>
                                <th scope="col" class="w-24 text-center" data-kt-datatable-column="actions">
                                    <span class="kt-table-col"><span class="kt-table-col-label">{{ __('journals::app.actions') }}</span></span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <template><!--begin:pagination--></template>
                <div class="kt-datatable-toolbar">
                    <div class="kt-datatable-length">
                        Show<select class="kt-select kt-select-sm w-16" name="perpage" data-kt-datatable-size="true"></select>per page
                    </div>
                    <div class="kt-datatable-info">
                        <span data-kt-datatable-info="true"></span>
                        <div class="kt-datatable-pagination" data-kt-datatable-pagination="true"></div>
                    </div>
                </div>
                <template><!--end:pagination--></template>
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

                <div class="space-y-1.5">
                    <label class="kt-label font-medium text-gray-700">{{ __('journals::app.journal') }} <span class="text-danger-600">*</span></label>
                    <select name="journal_id" class="kt-select w-full" required>
                        <option value="">-- {{ __('journals::app.journal') }} --</option>
                        @foreach($journals as $j)
                            <option value="{{ $j->id }}">{{ $j->name }}</option>
                        @endforeach
                    </select>
                </div>

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
    <script type="text/javascript">
        function openAddMemberModal() {
            document.getElementById('add_member_modal').classList.remove('hidden');
        }

        function closeAddMemberModal() {
            document.getElementById('add_member_modal').classList.add('hidden');
        }
        'use strict';

        const lang = {
            areYouSure: "{{ __('journals::app.are_you_sure') }}",
            deleteConfirmText: "{{ __('journals::app.delete_confirm_text') }}",
            yesDelete: "{{ __('journals::app.yes_delete') }}",
            yesDeleteSelected: "{{ __('journals::app.yes_delete_selected') }}",
            notice: "{{ __('journals::app.notice') }}",
            noEligibleItems: "{{ __('journals::app.no_eligible_items') }}",
            deleted: "{{ __('journals::app.deleted') }}",
            error: "{{ __('journals::app.error') }}"
        };

        var KTDatatableEditorialBoards = (function () {
            var isInitialized = false;
            var instance = null;

            var resolveDataTableClass = function () {
                if (typeof window === 'undefined') return null;
                if (window.KTDataTable) return window.KTDataTable;
                if (window.KTUI && window.KTUI.KTDataTable) return window.KTUI.KTDataTable;
                return null;
            };

            var init = function () {
                var KTDataTable = resolveDataTableClass();
                if (!KTDataTable) {
                    setTimeout(init, 100);
                    return null;
                }

                if (isInitialized && instance) return instance;

                var datatableEl = document.getElementById('kt_datatable_editorial_boards');
                if (!datatableEl) return null;

                var datatable = new KTDataTable(datatableEl, {
                    apiEndpoint: "{{ route('editorial-boards.datatables') }}",
                    requestMethod: 'GET',
                    requestHeaders: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    mapResponse: function (response) {
                        if (response && response.data) {
                            return {
                                data: response.data,
                                totalCount: response.totalCount || response.recordsTotal || response.data.length,
                                page: response.page || 1,
                                pageSize: response.pageSize || 10,
                                totalPages: response.pageCount || Math.ceil((response.totalCount || response.data.length) / (response.pageSize || 10)),
                            };
                        }
                        return { data: [], totalCount: 0, page: 1, pageSize: 10, totalPages: 1 };
                    },
                    columns: {
                        select: {
                            render: function (value, row) {
                                var rowId = row ? row.id : '';
                                return '<input class="kt-checkbox kt-checkbox-sm row-checkbox" type="checkbox" value="' + rowId + '" onchange="updateBulkDeleteState()">';
                            }
                        },
                        name: {
                            render: function (value, row) {
                                if (!row) return value || '-';
                                return '<div class="flex flex-col"><span class="font-semibold text-gray-900">' + (row.display_name || '-') + '</span></div>';
                            }
                        },
                        journal: {
                            render: function (value, row) {
                                if (!row) return value || '-';
                                return '<span class="text-gray-700 font-medium">' + (row.journal_name || '-') + '</span>';
                            }
                        },
                        role: {
                            render: function (value, row) {
                                var roleStr = row ? row.role : value;
                                return '<span class="kt-badge kt-badge-sm kt-badge-light-primary">' + (roleStr || '-') + '</span>';
                            }
                        },
                        affiliation: {
                            render: function (value, row) {
                                return (row ? row.affiliation : value) || '-';
                            }
                        },
                        email: {
                            render: function (value, row) {
                                return (row ? row.display_email : value) || '-';
                            }
                        },
                        actions: {
                            render: function (value, row) {
                                if (!row) return '';
                                return '<div class="flex items-center justify-center gap-1.5"><button type="button" onclick="deleteMember(' + row.id + ')" class="kt-btn kt-btn-sm kt-btn-icon kt-btn-ghost text-gray-600 hover:text-danger-600" title="Delete"><i class="ki-filled ki-trash"></i></button></div>';
                            }
                        }
                    },
                    callbacks: {
                        afterDraw: function () {
                            updateBulkDeleteState();
                        }
                    }
                });

                instance = datatable;
                isInitialized = true;
                return datatable;
            };

            return {
                init: function () {
                    if (document.readyState === 'loading') {
                        document.addEventListener('DOMContentLoaded', init);
                    } else {
                        init();
                    }
                }
            };
        })();

        KTDatatableEditorialBoards.init();

        document.getElementById('check_all')?.addEventListener('change', function () {
            var checkboxes = document.querySelectorAll('.row-checkbox:not([disabled])');
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateBulkDeleteState();
        });

        function updateBulkDeleteState() {
            var selected = document.querySelectorAll('.row-checkbox:checked');
            var btn = document.getElementById('bulk_delete_btn');
            var countEl = document.getElementById('selected_count');

            if (selected.length > 0) {
                btn?.classList.remove('hidden');
                if (countEl) countEl.innerText = selected.length;
            } else {
                btn?.classList.add('hidden');
                if (countEl) countEl.innerText = '0';
            }
        }

        function deleteMember(id) {
            Swal.fire({
                title: lang.areYouSure,
                text: lang.deleteConfirmText,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#6b7280',
                confirmButtonText: lang.yesDelete
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
                            Swal.fire(lang.deleted, data.message, 'success').then(() => location.reload());
                        } else {
                            Swal.fire(lang.error, data.message, 'error');
                        }
                    }).catch(() => Swal.fire(lang.error, 'Network error', 'error'));
                }
            });
        }

        function bulkDelete() {
            var selected = Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value);

            if (selected.length === 0) {
                Swal.fire(lang.notice, lang.noEligibleItems, 'info');
                return;
            }

            Swal.fire({
                title: lang.areYouSure,
                text: lang.deleteConfirmText,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#6b7280',
                confirmButtonText: lang.yesDeleteSelected
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch("{{ route('editorial-boards.bulk-destroy') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ ids: selected })
                    }).then(res => res.json()).then(data => {
                        if (data.success) {
                            Swal.fire(lang.deleted, data.message, 'success').then(() => location.reload());
                        } else {
                            Swal.fire(lang.error, data.message, 'error');
                        }
                    }).catch(() => Swal.fire(lang.error, 'Network error', 'error'));
                }
            });
        }
    </script>
@endpush
