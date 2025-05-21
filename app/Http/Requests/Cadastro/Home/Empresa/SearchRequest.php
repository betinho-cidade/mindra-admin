<?php

namespace App\Http\Requests\Cadastro\Home\Empresa;

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
            'cnpj' => Str::of($this->cnpj)->replaceMatches('/[^z0-9]++/', '')->__toString(),
        ]);
    }

    public function rules()
    {
        return [
            'nome' => 'nullable|min:3',
            'email' => 'nullable|min:3',
            'cnpj' => 'nullable|min:3',
        ];
    }

    public function messages()
    {
        return [
            'nome.min' => 'Necessário pelo menos 3 caracteres para consultar pelo Nome',
            'email.min' => 'Necessário pelo menos 3 caracteres para consultar pelo E-mail',
            'cnpj.min' => 'Necessário pelo menos 3 dígitos para consultar pelo CNPJ',
        ];
    }
}
