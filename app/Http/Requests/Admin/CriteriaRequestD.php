<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CriteriaRequestD extends FormRequest
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
                        'number' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'means_of_verification' => 'nullable|string',
            'criteria_function' => 'nullable|in:criteria,header,sub-header',
            'designated_offices' => 'nullable|array',

            // Nested validation for requirements
            'dRequirements' => 'nullable|array',
            'dRequirements.*.requirement_description' => 'nullable|string',
            'dRequirements.*.point_value' => 'nullable|numeric',
        ];
    }
}
