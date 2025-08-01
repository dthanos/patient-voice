<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'sort' => 'nullable|string',
            'sortDesc' => 'nullable|string',
            'filters' => 'nullable|array',
            'search' => 'nullable|string',
            'page' => 'nullable|integer',
            'itemsPerPage' => 'nullable|integer',
        ];
    }

    public function messages(): array
    {
        return [
        ];
    }
}
