<?php

namespace Modules\Journals\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class JournalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $journalId = $this->route('journal');
        if (is_object($journalId)) {
            $journalId = $journalId->id;
        }

        return [
            'name'               => ['required', 'string', 'max:255'],
            'slug'               => ['nullable', 'string', 'max:255', Rule::unique('journals', 'slug')->ignore($journalId)],
            'short_name'         => ['nullable', 'string', 'max:50'],
            'issn_p'             => ['nullable', 'string', 'max:20'],
            'issn_e'             => ['nullable', 'string', 'max:20'],
            'description'        => ['nullable', 'string'],
            'scope'              => ['nullable', 'string'],
            'guidelines'         => ['nullable', 'string'],
            'publication_ethics' => ['nullable', 'string'],
            'is_active'          => ['nullable', 'boolean'],
        ];
    }
}
