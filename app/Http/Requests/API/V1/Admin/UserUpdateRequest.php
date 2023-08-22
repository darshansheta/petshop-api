<?php

namespace App\Http\Requests\API\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|max:255|email|unique:users,email,' . $this->user->id,
            'password' => 'required|max:255|confirmed',
            'password_confirmation' => 'required|max:255',
            'avatar' => 'exists:files,uuid',
            'address' => 'required|max:255',
            'phone_number' => 'required|max:255',
            'is_marketing' => 'boolean',
        ];
    }
}
