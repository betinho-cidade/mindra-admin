<?php

namespace App\Http\Requests\Gestao\Campanha;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class UpdateRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'titulo' => 'required|max:255',
            'data_inicio' => 'required|date',
            'data_fim' => 'required|date',
            'situacao' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'titulo.required' => 'O título é requerido',
            'titulo.max' => 'O tamanho permitido para o título é de 255 caracteres',
            'data_inicio.required' => 'A data de início é requerida',
            'data_inicio.date' => 'A data de início está inválida',
            'data_fim.required' => 'A data de finalização é requerida',
            'data_fim.date' => 'A data de finalização está inválida',
            'situacao.required' => 'A situação é requerida',
       ];
    }
}

