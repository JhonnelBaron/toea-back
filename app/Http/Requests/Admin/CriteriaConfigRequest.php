<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CriteriaRequest extends FormRequest
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
            'bro_small' => 'boolean',
            'bro_medium' => 'boolean',
            'bro_large' => 'boolean',
            'gp_small' => 'boolean',
            'gp_medium' => 'boolean',
            'gp_large' => 'boolean',
            'bti_rtcstc' => 'boolean',
            'bti_ptcdtc' => 'boolean',
            'bti_tas' => 'boolean'
        ];
    }
}
