<?php

namespace App\Http\Requests;

use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateProjectRequest extends ProjectRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, list<string|Rule>>
     */
    public function rules(): array
    {
        return collect($this->projectRules())
            ->map(fn (array $rules) => ['sometimes', ...$rules])
            ->all();
    }

    /**
     * Configure the validator instance.
     *
     * @return array<int, callable(Validator): void>
     */
    public function after(): array
    {
        return [function (Validator $validator): void {
            if (! $this->has('due_date') || $validator->errors()->has('due_date') || $validator->errors()->has('start_date')) {
                return;
            }

            $project = $this->route('project');
            if (! $project instanceof Project) {
                return;
            }

            $startDate = $this->date('start_date') ?? $project->start_date;
            $dueDate = $this->date('due_date');

            if ($startDate && $dueDate && $dueDate->lt(Carbon::parse($startDate))) {
                $validator->errors()->add('due_date', 'The due date must be on or after the start date.');
            }
        }];
    }
}
