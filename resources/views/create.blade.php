@extends('layouts.main')

@section('breadcrumbs')
    <ul class="flex items-center gap-2 text-sm text-gray-600">
        <li><a href="/" class="hover:text-primary">Dashboard</a></li>
        <li><span>/</span></li>
        <li><a href="{{ route('journals.index') }}" class="hover:text-primary">{{ __('journals::app.journals') }}</a></li>
        <li><span>/</span></li>
        <li class="font-semibold text-gray-800">{{ isset($journal) ? __('journals::app.edit_journal') : __('journals::app.add_journal') }}</li>
    </ul>
@endsection

@section('content')
    <div class="grid w-full space-y-5">
        <div class="kt-card">
            <div class="kt-card-header min-h-16 py-5">
                <h3 class="kt-card-title">{{ isset($journal) ? __('journals::app.edit_journal') : __('journals::app.add_new_journal') }}</h3>
                <div>
                    <a href="{{ route('journals.index') }}" class="kt-btn kt-btn-sm kt-btn-outline">
                        <i class="ki-filled ki-arrow-left"></i> {{ __('journals::app.back') }}
                    </a>
                </div>
            </div>
            <div class="kt-card-body p-6">
                <form action="{{ isset($journal) ? route('journals.update', $journal->id) : route('journals.store') }}" method="POST">
                    @csrf
                    @if(isset($journal))
                        @method('PUT')
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Journal Name -->
                        <div class="space-y-1.5 md:col-span-2">
                            <label for="name" class="kt-label font-medium text-gray-700">{{ __('journals::app.name') }} <span class="text-danger-600">*</span></label>
                            <input
                                type="text"
                                name="name"
                                id="name"
                                class="kt-input w-full @error('name') kt-input-danger @enderror"
                                value="{{ old('name', $journal->name ?? '') }}"
                                placeholder="{{ __('journals::app.enter_name') }}"
                                required
                            />
                            @error('name')
                                <span class="text-xs text-danger-600">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Short Name -->
                        <div class="space-y-1.5">
                            <label for="short_name" class="kt-label font-medium text-gray-700">{{ __('journals::app.short_name') }}</label>
                            <input
                                type="text"
                                name="short_name"
                                id="short_name"
                                class="kt-input w-full @error('short_name') kt-input-danger @enderror"
                                value="{{ old('short_name', $journal->short_name ?? '') }}"
                                placeholder="{{ __('journals::app.enter_short_name') }}"
                            />
                        </div>

                        <!-- Slug -->
                        <div class="space-y-1.5">
                            <label for="slug" class="kt-label font-medium text-gray-700">{{ __('journals::app.slug') }}</label>
                            <input
                                type="text"
                                name="slug"
                                id="slug"
                                class="kt-input w-full @error('slug') kt-input-danger @enderror"
                                value="{{ old('slug', $journal->slug ?? '') }}"
                                placeholder="auto-generated-slug"
                            />
                        </div>

                        <!-- P-ISSN -->
                        <div class="space-y-1.5">
                            <label for="issn_p" class="kt-label font-medium text-gray-700">{{ __('journals::app.issn_p') }}</label>
                            <input
                                type="text"
                                name="issn_p"
                                id="issn_p"
                                class="kt-input w-full @error('issn_p') kt-input-danger @enderror"
                                value="{{ old('issn_p', $journal->issn_p ?? '') }}"
                                placeholder="{{ __('journals::app.enter_issn_p') }}"
                            />
                        </div>

                        <!-- E-ISSN -->
                        <div class="space-y-1.5">
                            <label for="issn_e" class="kt-label font-medium text-gray-700">{{ __('journals::app.issn_e') }}</label>
                            <input
                                type="text"
                                name="issn_e"
                                id="issn_e"
                                class="kt-input w-full @error('issn_e') kt-input-danger @enderror"
                                value="{{ old('issn_e', $journal->issn_e ?? '') }}"
                                placeholder="{{ __('journals::app.enter_issn_e') }}"
                            />
                        </div>

                        <!-- Description -->
                        <div class="space-y-1.5 md:col-span-2">
                            <label for="description" class="kt-label font-medium text-gray-700">{{ __('journals::app.description') }}</label>
                            <textarea
                                name="description"
                                id="description"
                                rows="3"
                                class="kt-input w-full p-3"
                            >{{ old('description', $journal->description ?? '') }}</textarea>
                        </div>

                        <!-- Scope -->
                        <div class="space-y-1.5 md:col-span-2">
                            <label for="scope" class="kt-label font-medium text-gray-700">{{ __('journals::app.scope') }}</label>
                            <textarea
                                name="scope"
                                id="scope"
                                rows="4"
                                class="kt-input w-full p-3"
                            >{{ old('scope', $journal->scope ?? '') }}</textarea>
                        </div>

                        <!-- Guidelines -->
                        <div class="space-y-1.5 md:col-span-2">
                            <label for="guidelines" class="kt-label font-medium text-gray-700">{{ __('journals::app.guidelines') }}</label>
                            <textarea
                                name="guidelines"
                                id="guidelines"
                                rows="4"
                                class="kt-input w-full p-3"
                            >{{ old('guidelines', $journal->guidelines ?? '') }}</textarea>
                        </div>

                        <!-- Status Checkbox -->
                        <div class="space-y-1.5 md:col-span-2 flex items-center gap-2 pt-2">
                            <input
                                type="checkbox"
                                name="is_active"
                                id="is_active"
                                value="1"
                                class="kt-checkbox"
                                {{ old('is_active', $journal->is_active ?? true) ? 'checked' : '' }}
                            />
                            <label for="is_active" class="font-medium text-gray-700">{{ __('journals::app.active') }}</label>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                        <a href="{{ route('journals.index') }}" class="kt-btn kt-btn-outline">
                            {{ __('journals::app.cancel') }}
                        </a>
                        <button type="submit" class="kt-btn kt-btn-primary text-white">
                            <i class="ki-filled ki-check text-white"></i> {{ isset($journal) ? __('journals::app.update') : __('journals::app.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
