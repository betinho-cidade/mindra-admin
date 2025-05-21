<?php

namespace App\Http\Requests\Cadastro\Usuario;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;


class SearchRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'cpf' => Str::of($this->cpf)->replaceMatches('/[^z0-9]++/', '')->__toString(),
        ]);
    }

    public function rules()
    {
        return [
            'nome' => 'nullable|min:3',
            'email' => 'nullable|min:3',
            'cpf' => 'nullable|min:3',
        ];
    }

    public function messages()
    {
        return [
            'nome.min' => 'Necessário pelo menos 3 caracteres para consultar pelo Nome',
            'email.min' => 'Necessário pelo menos 3 caracteres para consultar pelo E-mail',
            'cpf.min' => 'Necessário pelo menos 3 dígitos para consultar pelo CPF',
        ];
    }
}
