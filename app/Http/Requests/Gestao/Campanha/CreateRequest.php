<?php

namespace App\Http\Requests\Gestao\Campanha;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;


class CreateRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'formulario' => 'required',
            'titulo' => 'required|max:255',
            'data_inicio' => 'required|date|before:data_fim',
            'data_fim' => 'required|date|after:data_inicio',
            'situacao' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'formulario.required' => 'O formulário é requerido',
            'titulo.required' => 'O título é requerido',
            'titulo.max' => 'O tamanho permitido para o título é de 255 caracteres',
            'data_inicio.required' => 'A data de início é requerida',
            'data_inicio.date' => 'A data de início está inválida',
            'data_inicio.before' => 'A data de início deve ser anterior a data de finalização',
            'data_fim.required' => 'A data de finalização é requerida',
            'data_fim.date' => 'A data de finalização está inválida',
            'data_fim.after' => 'A data de finalização deve ser posterior a data de início',
            'situacao.required' => 'A situação é requerida',
       ];
    }
}
