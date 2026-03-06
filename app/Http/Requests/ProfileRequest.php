<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:20'],
            'username' => ['required', 'string', 'min:3', 'max:20', 'regex:/^[a-zA-Z0-9_]+$/', 'unique:'.User::class],
            'bio' => ['nullable', 'string', 'max:200'],
            'gender' => ['required', 'in:male,female'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a valid string.',
            'name.max' => 'The name may not be greater than 20 characters.',
            'username.required' => 'The username field is required.',
            'username.string' => 'The username must be a valid string.',
            'username.min' => 'The username must be at least 3 characters.',
            'username.max' => 'The username may not be greater than 20 characters.',
            'username.regex' => 'The username may only contain letters, numbers, and underscores.',
            'username.unique' => 'This username has already been taken.',
            'bio.string' => 'The bio must be a valid string.',
            'bio.max' => 'The bio may not be greater than 200 characters.',
            'gender.required' => 'The gender field is required.',
            'gender.in' => 'The selected gender is invalid. Please choose male or female.',
        ];
    }
}
