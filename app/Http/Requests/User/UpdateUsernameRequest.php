<?php

namespace App\Http\Requests\User;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUsernameRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => [
                'required',
                'string',
                'min:3',
                'max:30',
                'alpha_dash',
                Rule::unique('users', 'username')->ignore($this->user()->id),
                Rule::notIn(User::RESERVED_USERNAMES),
            ],
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
            'username.required' => 'O campo de nome de usuário é obrigatório.',
            'username.string' => 'O campo de nome de usuário deve ser uma string.',
            'username.min' => 'O campo de nome de usuário deve ter pelo menos 3 caracteres.',
            'username.max' => 'O campo de nome de usuário não pode exceder 30 caracteres.',
            'username.alpha_dash' => 'O campo de nome de usuário deve conter apenas letras, números, hífen e underscore.',
            'username.unique' => 'O nome de usuário já está em uso.',
            'username.not_in' => 'Este nome de usuário não está disponível.',
        ];
    }
}
