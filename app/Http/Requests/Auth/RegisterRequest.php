<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;


class RegisterRequest extends FormRequest
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
            'alpha_dash', // só letras, números, hífen e underscore
            'unique:users,username',
            Rule::notIn([
                    'admin', 'api', 'login', 'register', 'dashboard',
                    'settings', 'auth', 'logout', 'password', 'profile',
                ]),
            ],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
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
            'email.required' => 'O campo de email é obrigatório.',
            'email.string' => 'O campo de email deve ser uma string.',
            'email.email' => 'O campo de email deve ser um endereço de email válido.',
            'email.max' => 'O campo de email não pode exceder 255 caracteres.',
            'email.unique' => 'O email já está em uso.',
            'password.required' => 'O campo de senha é obrigatório.',
            'password.string' => 'O campo de senha deve ser uma string.',
            'password.min' => 'O campo de senha deve ter pelo menos 8 caracteres.',
            'password.confirmed' => 'A confirmação de senha não corresponde.',
        ];
    }

    protected function failedValidation(Validator $validator)

    {
        throw new HttpResponseException(
            response()->json([
                'status' => false,
                'message' => 'Erro de validação',
                'errors' => $validator->errors()
            ], 422)
        );
    }
}
