<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


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
            'name' => ['required', 'string', 'max:255'],
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
    {        return [
            'name.required' => 'O campo de nome é obrigatório.',
            'name.string' => 'O campo de nome deve ser uma string.',
            'name.max' => 'O campo de nome não pode exceder 255 caracteres.',
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
