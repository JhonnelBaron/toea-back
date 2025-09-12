<?php

namespace App\Http\Requests\Admin\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
       $userId = $this->route('id'); // assuming route is /users/{id}

        $rules = [
            'user_type' => 'sometimes|required|in:admin,secretariat,external validator,executive office focal,nominee,regional office',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'office' => 'nullable|string|max:255',
            'email' => "sometimes|required|email|unique:users,email,{$userId}",
            'password' => 'nullable|min:8',
        ];

        if ($this->user_type === 'nominee') {
            $rules = array_merge($rules, [
                'nominee_type' => 'sometimes|required|in:BRO,GP,BTI',
                'nominee_category' => 'sometimes|required|in:small,medium,large,ptc-dtc,rtc-stc,tas',
                'region' => 'sometimes|required|string|max:255',
                'province' => 'sometimes|required|string|max:255',
                'nominee_name' => 'sometimes|required|string|max:255',
            ]);
        }

        return $rules;
    }
}
