<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StoreProjectRequest extends ProjectRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, list<string|Rule>>
     */
    public function rules(): array
    {
        return $this->projectRules();
    }
}
