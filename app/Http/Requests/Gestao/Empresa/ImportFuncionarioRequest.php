<?php

namespace App\Http\Requests\Gestao\Empresa;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ImportFuncionarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => 'required|file|extensions:csv,txt|max:1024', // Aceita apenas .csv e .txt, máx 10MB
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'É necessário selecionar um arquivo.',
            'file.file' => 'O upload deve ser um arquivo válido.',
            'file.mimes' => 'O arquivo deve ser do tipo CSV ou TXT.',
            'file.extensions' => 'O arquivo deve ser do tipo CSV ou TXT.',
            'file.max' => 'O arquivo não pode ser maior que 1MB.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->hasFile('file')) {
            $file = $this->file('file');
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $this->merge([
                'file_original_name' => strtolower($originalName),
            ]);
        }
    }
}
