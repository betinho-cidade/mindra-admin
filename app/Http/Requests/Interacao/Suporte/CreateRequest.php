<?php

namespace App\Http\Requests\Interacao\Suporte;

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
            'new_assinante' => 'required',
            'new_post' => 'required|max:500',
        ];
    }

    public function messages()
    {
        return [
            'new_assinante.required' => 'O assinante é requerido',
            'new_post.max' => 'O tamanho permitido para a mensagem é de 500 caracteres',
            'new_post.required' => 'A mensagem é requerida',
        ];
    }
}
