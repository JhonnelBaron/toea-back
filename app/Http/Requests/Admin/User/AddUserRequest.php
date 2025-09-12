<?php

namespace App\Http\Requests\Admin\User;

use Illuminate\Foundation\Http\FormRequest;

class AddUserRequest extends FormRequest
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
        $rules = [
                    'user_type' => 'required|in:admin,secretariat,external validator,executive office focal,nominee,regional office',
                    'first_name' => 'nullable|string|max:255',
                    'last_name' => 'nullable|string|max:255',
                    'designation' => 'nullable|string|max:255',
                    'office' => 'nullable|string|max:255',
                    'email' => 'required|email|unique:users,email',
                    'password' => 'required|min:8',
                ];

                // Extra rules if nominee
                if ($this->user_type === 'nominee') {
                    $rules = array_merge($rules, [
                        'nominee_type' => 'required|in:BRO,GP,BTI',
                        'nominee_category' => 'required|in:small,medium,large,ptc-dtc,rtc-stc,tas',
                        'region' => 'required|string|max:255',
                        'province' => 'required|string|max:255',
                        'nominee_name' => 'required|string|max:255',
                    ]);
                }

                return $rules;
            
    }
}
