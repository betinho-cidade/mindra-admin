<?php

namespace App\Http\Requests\Interacao\Suporte;

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
            'post' => 'required|max:500',
        ];
    }

    public function messages()
    {
        return [
            'post.max' => 'O tamanho permitido para a mensagem é de 500 caracteres',
            'post.required' => 'A mensagem é requerida',
        ];
    }
}
