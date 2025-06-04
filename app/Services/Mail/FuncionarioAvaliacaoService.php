<?php

namespace App\Services\Mail;

use App\Mail\FuncionarioAvaliacaoMail;
use App\Models\Campanha;
use App\Models\EmpresaFuncionario;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Helpers\Formatters;

class FuncionarioAvaliacaoService
{

    protected $empresa_funcionarios;
    protected $campanha;

    /**
     * Construtor que injeta o modelo Empresa.
     *
     * @param Empresa $empresa
     */
    public function __construct(Array $empresa_funcionarios, Campanha $campanha)
    {
        $this->empresa_funcionarios = $empresa_funcionarios;
        $this->campanha = $campanha;
    }

    /**
     * Envia e-mails com senhas randômicas para um grupo de funcionários.
     *
     * @param array $funcionarioIds IDs dos funcionários
     * @return array Resultado do envio (sucessos e falhas)
     */
    public function sendInvites(): array
    {

        $results = ['success' => [], 'failed' => [], 'log_file' => null];
        $logEntries = [];
        $logFileName = 'avaliacao-log-' . now()->format('Y-m-d_H-i-s') . '.log';

        if(count($this->empresa_funcionarios) == 0) {
            throw new \Exception('Nenhum registro encontrado');
        }

        foreach($this->empresa_funcionarios as $emp_func) {


            $empresa_funcionario = EmpresaFuncionario::where('id', $emp_func['id'])->first();

            if($empresa_funcionario){

                try {
                    // Enfileira o e-mail
                    Mail::to($empresa_funcionario->funcionario->user->email)->queue(new FuncionarioAvaliacaoMail($empresa_funcionario->funcionario, $this->campanha));

                    $results['success'][] = [
                        'id' => $empresa_funcionario->funcionario->user->id,
                        'email' => $empresa_funcionario->funcionario->user->email,
                        'cpf' => Formatters::formataCpf($empresa_funcionario->funcionario->user->cpf),
                    ];

                    // Adiciona ao log
                    $logEntries[] = sprintf(
                        "[%s] SUCESSO: Funcionário ID=%d, E-mail=%s, CPF=%s",
                        now()->toDateTimeString(),
                        $empresa_funcionario->funcionario->user->id,
                        $empresa_funcionario->funcionario->user->email,
                        Formatters::formataCpf($empresa_funcionario->funcionario->user->cpf)
                    );

                } catch (\Exception $e) {

                    $results['failed'][] = [
                        'id' => $empresa_funcionario->funcionario->id,
                        'email' => $empresa_funcionario->funcionario->email,
                        'cpf' => Formatters::formataCpf($empresa_funcionario->funcionario->user->cpf),
                        'error' => $e->getMessage(),
                    ];

                    // Adiciona ao log
                    $logEntries[] = sprintf(
                        "[%s] FALHA: Funcionário ID=%d, E-mail=%s, CPF=%s, Erro=%s",
                        now()->toDateTimeString(),
                        $empresa_funcionario->funcionario->user->id,
                        $empresa_funcionario->funcionario->user->email,
                        Formatters::formataCpf($empresa_funcionario->funcionario->user->cpf),
                        $e->getMessage()
                    );
                }
            }

        }

        // Salva o log no diretório storage/logs
        if (!empty($logEntries)) {
            $logContent = implode("\n", $logEntries);
            Storage::put('logs/invite/' . $this->campanha->empresa->id . '/campanha/' . $this->campanha->id . '/'  . $logFileName, $logContent);
            $results['log_file'] = $logFileName;
        }

        return $results;
    }

    /**
     * Gera um link para download do arquivo de log.
     *
     * @param string $logFileName
     * @return string|null
     */
    public function getLogAvaliacaoDownloadLink(string $logFileName): ?string
    {
        if (Storage::exists('logs/invite/' . $this->campanha->empresa->id  . '/campanha/' . $this->campanha->id . '/' . $logFileName)) {
            return route('campanha_empresa.logAvaliacao', ['campanha' => $this->campanha, 'filename' => $logFileName]);
        }
        return null;
    }
}
