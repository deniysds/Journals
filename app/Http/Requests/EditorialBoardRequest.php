<?php

namespace Modules\Journals\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditorialBoardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'journal_id'  => ['required', 'exists:journals,id'],
            'user_id'     => ['nullable', 'exists:users,id'],
            'name'        => ['required_without:user_id', 'nullable', 'string', 'max:255'],
            'email'       => ['nullable', 'email', 'max:255'],
            'affiliation' => ['nullable', 'string', 'max:255'],
            'role'        => ['required', 'string', 'max:100'],
            'order_no'    => ['nullable', 'integer', 'min:0'],
            'is_active'   => ['nullable', 'boolean'],
        ];
    }
}
