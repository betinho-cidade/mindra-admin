<?php

namespace App\Http\Requests\Cadastro\Home\Empresa;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;


class CreateRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'cnpj' => Str::of($this->cnpj)->replaceMatches('/[^z0-9]++/', '')->__toString(),
            'telefone' => Str::of($this->telefone)->replaceMatches('/[^z0-9]++/', '')->__toString(),
            'responsavel_telefone' => Str::of($this->responsavel_telefone)->replaceMatches('/[^z0-9]++/', '')->__toString(),
            'end_cep' => Str::of($this->end_cep)->replaceMatches('/[^z0-9]++/', '')->__toString(),
        ]);
    }

    public function rules()
    {
        return [
            'nome' => 'required|max:255',
            'cnpj' => 'required|max:14|unique:empresas,cnpj',
            'email' => 'nullable|email|max:255',
            'responsavel_nome' => 'max:255',
            'responsavel_telefone' => 'max:20',
            'telefone' => 'max:20',
            'num_contrato' => 'max:20',
            'inscricao_estadual' => 'max:50',
            'atividade_principal' => 'max:300',
            'site' => 'nullable|url|max:200',
            'data_abertura' => 'date',
            'situacao' => 'required',
            'end_cep' => 'max:8',
            'end_cidade' => 'max:60',
            'end_uf' => 'max:2',
            'end_logradouro' => 'max:80',
            'end_numero' => 'max:20',
            'end_bairro' => 'max:60',
            'end_complemento' => 'max:100',
        ];
    }

    public function messages()
    {
        return [
            'nome.required' => 'O nome é requerido',
            'nome.max' => 'O tamanho permitido para o nome é de 255 caracteres',
            'cnpj.required' => 'O CNPJ é requerido',
            'cnpj.max' => 'O CNPJ permite apenas 14 dígitos',
            'cnpj.unique' => 'Já existe o CNPJ informado. Por gentileza, informe outro',
            'email.email' => 'O E-mail informado não é válido.',
            'email.max' => 'O tamanho permitido para o E-mail é de 255 caracteres',
            'responsavel_nome.max' => 'O tamanho permitido para o Nome do Responsável é de 255 caracteres',
            'responsavel_telefone.max' => 'O tamanho permitido para o Telefone do Responsável é de 20 caracteres',
            'telefone.max' => 'O tamanho permitido para o Telefone da Empresa é de 20 caracteres',
            'num_contrato.max' => 'O tamanho permitido para o Número do Contrato é de 20 caracteres',
            'inscricao_estadual.max' => 'O tamanho permitido para a Inscrição Estadual é de 50 caracteres',
            'atividade_principal.max' => 'O tamanho permitido para a Atividade Principal é de 300 caracteres',
            'site.url' => 'A URL do Site é inválida',
            'site.max' => 'O tamanho permitido para a URL do Site é de 200 caracteres',
            'data_abertura.date' => 'A data de abertura está inválida',
            'situacao.required' => 'A situação é requerida',
            'end_cep.max' => 'O tamanho permitido para o cep é de 8 caracteres',
            'end_cidade.max' => 'O tamanho permitido para a cidade é de 60 caracteres',
            'end_uf.max' => 'O tamanho permitido para a UF é de 2 caracteres',
            'end_logradouro.max' => 'O tamanho permitido para o logradouro é de 80 caracteres',
            'end_numero.max' => 'O tamanho permitido para o número é de 20 caracteres',
            'end_bairro.max' => 'O tamanho permitido para o bairro é de 60 caracteres',
            'end_complemento.max' => 'O tamanho permitido para o complemento é de 100 caracteres',
       ];
    }
}
