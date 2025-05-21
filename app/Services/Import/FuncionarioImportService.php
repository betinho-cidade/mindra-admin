<?php

namespace App\Services\Import;

use App\Models\User;
use App\Models\Funcionario;
use App\Models\Role;
use App\Models\Empresa;
use App\Models\EmpresaFuncionario;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Helpers\Formatters;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class FuncionarioImportService
{

    protected $empresa, $user;

    public function __construct(Empresa $empresa, User $user)
    {
        $this->empresa = $empresa;
        $this->user = $user;
    }
    public function import($filePath): array
    {

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new \Exception('Erro ao abrir o arquivo.');
        }

        $header = fgetcsv($handle, 0, ','); // Assume delimitador como vírgula
        $expectedHeader = [
            'nome', 'email', 'cpf', 'rg', 'data_nascimento', 'telefone', 'sexo', 'end_cep',
            'end_cidade', 'end_uf', 'end_logradouro', 'end_numero', 'end_bairro', 'end_complemento', 'matricula',
            'cargo', 'departamento', 'data_admissao'
        ];

        if ($header !== $expectedHeader) {
            fclose($handle);
            throw ValidationException::withMessages(['file' => 'Cabeçalho do arquivo inválido.']);
        }

        $results = ['success' => [], 'failed' => [], 'log_file' => null];
        $logEntries = [];
        $logFileName = 'import-log-' . now()->format('Y-m-d_H-i-s') . '.log';

        $errors = [];
        $successCount = 0;
        $lineNumber = 2; // Começa na linha 2 (após o cabeçalho)

        while (($data = fgetcsv($handle, 0, ',')) !== false) {
            $row = array_combine($expectedHeader, array_pad($data, count($expectedHeader), null));

            // Preparar os dados antes da validação
            $row = $this->prepareRow($row);

            $customMessages = [
                'nome.required' => 'Nome requerido',
                'nome.max' => 'Nome 255 caracteres',
                'email.required' => 'E-mail requerido',
                'email.email' => 'E-mail inválido.',
                'email.max' => 'E-mail 255 caracteres',
                'email.unique' => 'E-mail já existe',
                'cpf.required' => 'CPF requerido',
                'cpf.max' => 'CPF 11 dígitos',
                'cpf.unique' => 'CPF já existe',
                'cpf.cpf' => 'CPF inválido',
                'rg.max' => 'RG 11 caracteres',
                'sexo.required' => 'Sexo requerido',
                'data_nascimento.required' => 'Data Nascimento requerida',
                'data_nascimento.date' => 'Data Nascimento inválida',
                'data_nascimento.date_format' => 'Data Nascimento AAAA-MM-DD.',
                'telefone.required' => 'Telefone requerido',
                'telefone.max' => 'Telefone 20 caracteres',
                'end_cep.max' => 'Cep 8 caracteres',
                'end_cidade.max' => 'Cidade 60 caracteres',
                'end_uf.max' => 'UF 2 caracteres',
                'end_logradouro.max' => 'Logradouro 80 caracteres',
                'end_numero.max' => 'Número 20 caracteres',
                'end_bairro.max' => 'Bairro 60 caracteres',
                'end_complemento.max' => 'Complemento 100 caracteres',
                'matricula.max' => 'Matrícula 20 caracteres',
                'cargo.required' => 'Cargo requerido',
                'cargo.max' => 'Cargo 50 caracteres',
                'departamento.max' => 'Departamento 50 caracteres',
                'data_admissao.date' => 'Data Admissão inválida',
                'data_admissao.date_format' => 'Data Admissão AAAA-MM-DD.',
            ];

            $validator = Validator::make($row, [
                'nome' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email',
                'cpf' => 'required|cpf|max:11|unique:users,cpf',
                'rg' => 'nullable|string|max:11',
                'sexo' => 'required|string|in:M,F',
                'data_nascimento' => 'required|date|date_format:Y-m-d',
                'telefone' => 'required|string|max:20',
                'end_cep' => 'nullable|string|max:8',
                'end_cidade' => 'nullable|string|max:60',
                'end_uf' => 'nullable|string|size:2',
                'end_logradouro' => 'nullable|string|max:80',
                'end_numero' => 'nullable|string|max:20',
                'end_bairro' => 'nullable|string|max:60',
                'end_complemento' => 'nullable|string|max:100',
                'matricula' => 'nullable|string|max:20',
                'cargo' => 'required|string|max:50',
                'departamento' => 'nullable|string|max:50',
                'data_admissao' => 'nullable|date|date_format:Y-m-d',
             ], $customMessages);


            if ($validator->fails()) {
                $errors[] = "Linha $lineNumber: " . implode(', ', $validator->errors()->all());

                $results['failed'][] = [
                    'linha' => $lineNumber,
                    'error' => implode(', ', $validator->errors()->all()),
                ];

                $logEntries[] = sprintf(
                    "[%s] FALHA: Linha =%d, Erro=%s",
                    now()->toDateTimeString(),
                    $lineNumber,
                    implode(', ', $validator->errors()->all())
                );
            } else {
                try {
                    $usuario = new User();
                    $usuario->nome = $row['nome'];
                    $usuario->email = $row['email'];
                    $usuario->password = bcrypt(Str::random(8));
                    $usuario->cpf = $row['cpf'];
                    $usuario->rg = $row['rg'];
                    $usuario->data_nascimento = $row['data_nascimento'];
                    $usuario->telefone = $row['telefone'];
                    $usuario->sexo = $row['sexo'];
                    $usuario->end_cep = $row['end_cep'];
                    $usuario->end_cidade = $row['end_cidade'];
                    $usuario->end_uf = $row['end_uf'];
                    $usuario->end_logradouro = $row['end_logradouro'];
                    $usuario->end_numero = $row['end_numero'];
                    $usuario->end_bairro = $row['end_bairro'];
                    $usuario->end_complemento = $row['end_complemento'];
                    $usuario->save();

                    $usuario->rolesAll()->attach(Role::where('name', 'Funcionario')->first()->id);
                    $status = $usuario->rolesAll()
                                    ->withPivot(['status'])
                                    ->first()
                                    ->pivot;
                    $status['status'] = 'I';
                    $status->save();

                    $funcionario = new Funcionario();
                    $funcionario->user_id = $usuario->id;
                    $funcionario->save();

                    $empresa_funcionario = new EmpresaFuncionario();
                    $empresa_funcionario->empresa_id = $this->empresa->id;
                    $empresa_funcionario->funcionario_id = $funcionario->id;
                    $empresa_funcionario->matricula = $row['matricula'];
                    $empresa_funcionario->cargo = $row['cargo'];
                    $empresa_funcionario->departamento = $row['departamento'];
                    $empresa_funcionario->data_admissao = $row['data_admissao'];
                    $empresa_funcionario->status = 'I';
                    $empresa_funcionario->empresa_funcionario_created = $this->user->id;
                    $empresa_funcionario->empresa_funcionario_updated = $this->user->id;
                    $empresa_funcionario->save();

                    $results['success'][] = [
                        'id' => $usuario->id,
                        'email' => $usuario->email,
                        'cpf' => Formatters::formataCpf($usuario->cpf),
                    ];

                    $logEntries[] = sprintf(
                        "[%s] SUCESSO: Funcionário ID=%d, E-mail=%s, CPF=%s",
                        now()->toDateTimeString(),
                        $usuario->id,
                        $usuario->email,
                        Formatters::formataCpf($usuario->cpf)
                    );

                    $successCount++;
                } catch (\Exception $e) {
                    $errors[] = "Linha $lineNumber: Erro ao salvar: " . $e->getMessage();

                    $results['failed'][] = [
                        'linha' => $lineNumber,
                        'error' => $e->getMessage(),
                    ];

                    $logEntries[] = sprintf(
                        "[%s] FALHA: Linha =%d, Erro=%s",
                        now()->toDateTimeString(),
                        $lineNumber,
                        $e->getMessage()
                    );

                }
            }

            $lineNumber++;
        }

        if ($lineNumber == 2) {
            fclose($handle);
            throw new \Exception('Nenhum registro encontrado no arquivo.');
        }

        fclose($handle);

        // Salva o log no diretório storage/logs
        if (!empty($logEntries)) {
            $logContent = implode("\n", $logEntries);
            Storage::put('logs/import/' . $this->empresa->id . '/' . $logFileName, $logContent);
            $results['log_file'] = $logFileName;
        }

        return $results;
    }


 protected function prepareRow(array $row): array
    {
        if (!empty($row['cpf'])) {
            $row['cpf'] = preg_replace('/[^0-9]/', '', $row['cpf']);
        }

        if (!empty($row['end_cep'])) {
            $row['end_cep'] = preg_replace('/[^0-9]/', '', $row['end_cep']);
        }

        if (!empty($row['telefone'])) {
            $row['telefone'] = preg_replace('/[^0-9]/', '', $row['telefone']);
        }

        // // Padronizar sexo
        // if (!empty($row['sexo'])) {
        //     $sexo = strtolower(trim($row['sexo']));
        //     $row['sexo'] = match ($sexo) {
        //         'masculino', 'm' => 'M',
        //         'feminino', 'f' => 'F',
        //         'outro', 'o' => 'O',
        //         default => null, // Ou manter o valor original se preferir
        //     };
        // }

        // // Definir departamento padrão se vazio
        // if (empty($row['departamento'])) {
        //     $row['departamento'] = 'Geral';
        // }

        // // Converter data_nascimento e data_admissao para Y-m-d (se no formato d/m/Y)
        // foreach (['data_nascimento', 'data_admissao'] as $dateField) {
        //     if (!empty($row[$dateField])) {
        //         try {
        //             $date = Carbon::createFromFormat('d/m/Y', $row[$dateField]);
        //             if ($date) {
        //                 $row[$dateField] = $date->format('Y-m-d');
        //             }
        //         } catch (\Exception $e) {
        //             // Se não for d/m/Y, manter o valor original para validação
        //         }
        //     }
        // }

        // // Exemplo: Converter email para minúsculas
        // if (!empty($row['email'])) {
        //     $row['email'] = strtolower($row['email']);
        // }

        return $row;
    }

    public function getLogImportDownloadLink(string $logFileName): ?string
    {
        if (Storage::exists('logs/import/' . $this->empresa->id  . '/' . $logFileName)) {
            return route('empresa_funcionario.logImport', ['empresa' => $this->empresa, 'filename' => $logFileName]);
        }
        return null;
    }

}
