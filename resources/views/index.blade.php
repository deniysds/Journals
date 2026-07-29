@extends('layouts.main')

@section('breadcrumbs')
    {{ Breadcrumbs::render('journals') }}
@endsection

@section('content')
    <div class="grid w-full space-y-5">
        <div class="kt-card">
            <div class="kt-card-header min-h-16 flex-wrap py-5">
                <div class="flex items-center gap-2.5">
                    <h3 class="kt-card-title">{{ __('journals::app.journals_list') }}</h3>
                </div>
                <div class="flex items-center gap-2.5">
                    <input type="text" placeholder="{{ __('journals::app.search') }}" class="kt-input sm:w-48"
                        id="search_journals" data-kt-datatable-search="#kt_datatable_journals" />
                    <button type="button" class="kt-btn kt-btn-sm kt-btn-danger hidden" id="bulk_delete_btn"
                        onclick="bulkDelete()">
                        <i class="ki-filled ki-trash text-white"></i> {{ __('journals::app.delete_selected') }} (<span
                            id="selected_count">0</span>)
                    </button>
                    <a class="kt-btn kt-btn-sm kt-btn-outline" id="export_btn" href="{{ route('journals.export') }}"
                        target="_blank">
                        <i class="ki-filled ki-file-down"></i> {{ __('journals::app.export') }}
                    </a>
                    @can('journals.create')
                        <a class="kt-btn kt-btn-sm kt-btn-primary text-white" href="{{ route('journals.create') }}">
                            <i class="ki-filled ki-plus text-white"></i> {{ __('journals::app.add_journal') }}
                        </a>
                    @endcan
                </div>
            </div>
            <div id="kt_datatable_journals" class="kt-card-table" data-kt-datatable-page-size="10"
                data-kt-datatable-state-save="true">
                <div class="kt-table-wrapper kt-scrollable">
                    <table class="kt-table" data-kt-datatable-table="true">
                        <thead>
                            <tr>
                                <th scope="col" class="w-14" data-kt-datatable-column="select">
                                    <input class="kt-checkbox kt-checkbox-sm" id="check_all" data-kt-datatable-check="true"
                                        type="checkbox" />
                                </th>
                                <th scope="col" class="w-48" data-kt-datatable-column="name">
                                    <span class="kt-table-col"><span
                                            class="kt-table-col-label">{{ __('journals::app.name') }}</span><span
                                            class="kt-table-col-sort"></span></span>
                                </th>
                                <th scope="col" class="w-32" data-kt-datatable-column="short_name">
                                    <span class="kt-table-col"><span
                                            class="kt-table-col-label">{{ __('journals::app.short_name') }}</span><span
                                            class="kt-table-col-sort"></span></span>
                                </th>
                                <th scope="col" class="w-36" data-kt-datatable-column="issn_p">
                                    <span class="kt-table-col"><span
                                            class="kt-table-col-label">{{ __('journals::app.issn_p') }}</span><span
                                            class="kt-table-col-sort"></span></span>
                                </th>
                                <th scope="col" class="w-36" data-kt-datatable-column="issn_e">
                                    <span class="kt-table-col"><span
                                            class="kt-table-col-label">{{ __('journals::app.issn_e') }}</span><span
                                            class="kt-table-col-sort"></span></span>
                                </th>
                                <th scope="col" class="w-28 text-center" data-kt-datatable-column="is_active">
                                    <span class="kt-table-col"><span
                                            class="kt-table-col-label">{{ __('journals::app.status') }}</span><span
                                            class="kt-table-col-sort"></span></span>
                                </th>
                                <th scope="col" class="w-24 text-center" data-kt-datatable-column="actions">
                                    <span class="kt-table-col"><span
                                            class="kt-table-col-label">{{ __('journals::app.actions') }}</span></span>
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
                        Show<select class="kt-select kt-select-sm w-16" name="perpage"
                            data-kt-datatable-size="true"></select>per page
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
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript">
        'use strict';

        const lang = {
            edit: "{{ __('journals::app.edit_journal') }}",
            delete: "{{ __('journals::app.actions') }}",
            cannotDeleteHasRelations: "{{ __('journals::app.cannot_delete_has_relations') }}",
            areYouSure: "{{ __('journals::app.are_you_sure') }}",
            deleteConfirmText: "{{ __('journals::app.delete_confirm_text') }}",
            yesDelete: "{{ __('journals::app.yes_delete') }}",
            yesDeleteSelected: "{{ __('journals::app.yes_delete_selected') }}",
            notice: "{{ __('journals::app.notice') }}",
            noEligibleItems: "{{ __('journals::app.no_eligible_items') }}",
            deleted: "{{ __('journals::app.deleted') }}",
            error: "{{ __('journals::app.error') }}"
        };

        var KTDatatableJournals = (function () {
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

                var datatableEl = document.getElementById('kt_datatable_journals');
                if (!datatableEl) return null;

                var datatable = new KTDataTable(datatableEl, {
                    apiEndpoint: "{{ route('journals.datatables') }}",
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
                                var disabledAttr = (row && !row.can_delete) ? 'disabled' : '';
                                var rowId = row ? row.id : '';
                                return '<input class="kt-checkbox kt-checkbox-sm row-checkbox" type="checkbox" value="' + rowId + '" ' + disabledAttr + ' onchange="updateBulkDeleteState()">';
                            }
                        },
                        name: {
                            render: function (value, row) {
                                if (!row) return value || '-';
                                var showUrl = "{{ route('journals.show', ':id') }}".replace(':id', row.id);
                                return '<div class="flex flex-col"><a href="' + showUrl + '" class="font-semibold text-gray-900 hover:text-primary-600">' + (row.name || '-') + '</a><span class="text-xs text-gray-500">' + (row.slug || '') + '</span></div>';
                            }
                        },
                        short_name: {
                            render: function (value, row) {
                                return (row ? row.short_name : value) || '-';
                            }
                        },
                        issn_p: {
                            render: function (value, row) {
                                return (row ? row.issn_p : value) || '-';
                            }
                        },
                        issn_e: {
                            render: function (value, row) {
                                return (row ? row.issn_e : value) || '-';
                            }
                        },
                        is_active: {
                            render: function (value, row) {
                                var isActive = row ? row.is_active : value;
                                if (isActive) {
                                    return '<span class="kt-badge kt-badge-sm kt-badge-light-success">' + "{{ __('journals::app.active') }}" + '</span>';
                                }
                                return '<span class="kt-badge kt-badge-sm kt-badge-light-danger">' + "{{ __('journals::app.inactive') }}" + '</span>';
                            }
                        },
                        actions: {
                            render: function (value, row) {
                                if (!row) return '';
                                var editUrl = "{{ route('journals.edit', ':id') }}".replace(':id', row.id);
                                var html = '<div class="flex items-center justify-center gap-1.5">';

                                html += '<a href="' + editUrl + '" class="kt-btn kt-btn-xs kt-btn-icon kt-btn-ghost text-warning" title="' + lang.edit + '"><i class="ki-filled ki-pencil text-warning text-base"></i></a>';

                                if (row.can_delete) {
                                    html += '<button type="button" onclick="deleteJournal(' + row.id + ')" class="kt-btn kt-btn-xs kt-btn-icon kt-btn-ghost text-danger" title="Delete"><i class="ki-filled ki-trash text-danger text-base"></i></button>';
                                } else {
                                    html += '<button type="button" class="kt-btn kt-btn-xs kt-btn-icon kt-btn-ghost opacity-30 cursor-not-allowed text-gray-400" disabled title="' + lang.cannotDeleteHasRelations + '"><i class="ki-filled ki-trash text-gray-400 text-base"></i></button>';
                                }

                                html += '</div>';
                                return html;
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

        KTDatatableJournals.init();

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

        function deleteJournal(id) {
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
                    fetch("{{ route('journals.destroy', ':id') }}".replace(':id', id), {
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
                    fetch("{{ route('journals.bulk-destroy') }}", {
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