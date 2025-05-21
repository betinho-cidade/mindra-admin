<?php

namespace App\Http\Requests\Cadastro\UsuarioLogado;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;


class UpdateRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'cpf' => Str::of($this->cpf)->replaceMatches('/[^z0-9]++/', '')->__toString(),
            'telefone' => Str::of($this->telefone)->replaceMatches('/[^z0-9]++/', '')->__toString(),
            'end_cep' => Str::of($this->end_cep)->replaceMatches('/[^z0-9]++/', '')->__toString(),
        ]);
    }

    public function rules()
    {
        return [
            'nome' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$this->usuario_logado->id,
            'cpf' => 'required|cpf|max:11|unique:users,cpf,'.$this->usuario_logado->id,
            'rg' => 'max:11',
            'sexo' => 'required',
            'data_nascimento' => 'required|date',
            'telefone' => 'required',
            'end_cep' => 'max:8',
            'end_cidade' => 'max:60',
            'end_uf' => 'max:2',
            'end_logradouro' => 'max:80',
            'end_numero' => 'max:20',
            'end_bairro' => 'max:60',
            'end_complemento' => 'max:40',
            'password' => 'nullable|min:8',
            'password_confirm' => 'nullable|required_with:password|min:8|same:password',
        ];
    }

    public function messages()
    {
        return [
            'nome.required' => 'O nome é requerido',
            'nome.max' => 'O tamanho permitido para o nome é de 255 caracteres',
            'email.required' => 'O E-mail é requerido',
            'email.email' => 'O E-mail informado não é válido.',
            'email.max' => 'O tamanho permitido para o E-mail é de 300 caracteres',
            'email.unique' => 'Já existe o E-mail informado. Por gentileza, informe outro',
            'cpf.required' => 'O CPF é requerido',
            'cpf.max' => 'O CPF permite apenas 11 dígitos',
            'cpf.unique' => 'Já existe o CPF informado. Por gentileza, informe outro',
            'cpf.cpf' => 'O CPF informado não é válido',
            'rg.max' => 'O tamanho permitido para o RG é de 11 caracteres',
            'sexo.required' => 'O sexo é requerido',
            'data_nascimento.required' => 'A data de nascimento é requerida',
            'data_nascimento.date' => 'A data de nascimento está inválida',
            'telefone.required' => 'O telefone é requerido',
            'end_cep.max' => 'O tamanho permitido para o cep é de 8 caracteres',
            'end_cidade.max' => 'O tamanho permitido para a cidade é de 60 caracteres',
            'end_uf.max' => 'O tamanho permitido para a UF é de 2 caracteres',
            'end_logradouro.max' => 'O tamanho permitido para o logradouro é de 80 caracteres',
            'end_numero.max' => 'O tamanho permitido para o número é de 20 caracteres',
            'end_bairro.max' => 'O tamanho permitido para o bairro é de 60 caracteres',
            'end_complemento.max' => 'O tamanho permitido para o complemento é de 40 caracteres',
            'password.min' => 'A Nova Senha deve conter no mínimo 8 caracteres',
            'password_confirm.min' => 'A Senha de Confirmação deve ter no mínimo 8 caracteres',
            'password_confirm.same' => 'A Senha de Confirmação deve ser igual a Nova Senha',
            'password_confirm.required_with' => 'A Senha de Confirmação deve ser igual a Nova Senha',
        ];
    }

}
