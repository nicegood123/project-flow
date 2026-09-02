<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, list<string|Rule>>
     */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::in(['Planning', 'In Progress', 'On Hold', 'Completed'])],
            'priority' => ['nullable', Rule::in(['Low', 'Medium', 'High'])],
            'sort' => ['nullable', Rule::in(['project_name', 'client_name', 'start_date', 'due_date', 'priority', 'status'])],
            'direction' => ['nullable', Rule::in(['asc', 'desc'])],
        ];
    }
}
