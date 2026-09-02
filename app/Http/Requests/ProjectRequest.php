<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

abstract class ProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules shared by project requests.
     *
     * @return array<string, list<string|Rule>>
     */
    protected function projectRules(): array
    {
        return [
            'client_name' => ['required', 'string', 'max:255'],
            'project_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['Planning', 'In Progress', 'On Hold', 'Completed'])],
            'priority' => ['required', Rule::in(['Low', 'Medium', 'High'])],
            'start_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ];
    }
}
