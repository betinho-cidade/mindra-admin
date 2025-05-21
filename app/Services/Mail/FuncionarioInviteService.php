<?php

namespace App\Services\Mail;

use App\Mail\FuncionarioInviteMail;
use App\Models\Funcionario;
use App\Models\Empresa;
use App\Models\EmpresaFuncionario;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Helpers\Formatters;

class FuncionarioInviteService
{

    protected $empresa;

    /**
     * Construtor que injeta o modelo Empresa.
     *
     * @param Empresa $empresa
     */
    public function __construct(Empresa $empresa)
    {
        $this->empresa = $empresa;
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
        $logFileName = 'invite-log-' . now()->format('Y-m-d_H-i-s') . '.log';

        $funcionarios = Funcionario::join('empresa_funcionarios', 'empresa_funcionarios.funcionario_id', '=', 'funcionarios.id')
                                   ->join('empresas', 'empresa_funcionarios.empresa_id', '=', 'empresas.id')
                                   ->whereIn('empresa_funcionarios.status', ['I'])
                                   ->whereIn('empresas.status', ['A'])
                                   ->where('empresas.id', $this->empresa->id)
                                   ->select('funcionarios.*')
                                   ->get();

        if(count($funcionarios) == 0) {
            throw new \Exception('Nenhum registro inativo encontrado');
        }

        foreach ($funcionarios as $funcionario) {
            try {
                // Gera senha randômica
                $randomPassword = Str::random(8);

                // Atualiza a senha do funcionário
                //$funcionario->user->password = ($funcionario->id == 23) ? 'asdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdf' : 'asdf';
                $funcionario->user->password = bcrypt($randomPassword);
                $funcionario->user->save();

                EmpresaFuncionario::where('funcionario_id', $funcionario->id)
                                 ->where('empresa_id', $this->empresa->id)
                                 ->update(['status' => 'A']);

                // Libera o acesso do funcionário ao sistema
                $status = $funcionario->user->rolesAll()
                                ->withPivot(['status'])
                                ->first()
                                ->pivot;
                $status['status'] = 'A';
                $status->save();

                // Enfileira o e-mail
                Mail::to($funcionario->user->email)->queue(new FuncionarioInviteMail($funcionario, $randomPassword));

                $results['success'][] = [
                    'id' => $funcionario->user->id,
                    'email' => $funcionario->user->email,
                    'cpf' => Formatters::formataCpf($funcionario->user->cpf),
                ];

                // Adiciona ao log
                $logEntries[] = sprintf(
                    "[%s] SUCESSO: Funcionário ID=%d, E-mail=%s, CPF=%s",
                    now()->toDateTimeString(),
                    $funcionario->user->id,
                    $funcionario->user->email,
                    Formatters::formataCpf($funcionario->user->cpf)
                );


            } catch (\Exception $e) {
                $results['failed'][] = [
                    'id' => $funcionario->id,
                    'email' => $funcionario->email,
                    'cpf' => Formatters::formataCpf($funcionario->user->cpf),
                    'error' => $e->getMessage(),
                ];

                // Adiciona ao log
                $logEntries[] = sprintf(
                    "[%s] FALHA: Funcionário ID=%d, E-mail=%s, CPF=%s, Erro=%s",
                    now()->toDateTimeString(),
                    $funcionario->user->id,
                    $funcionario->user->email,
                    Formatters::formataCpf($funcionario->user->cpf),
                    $e->getMessage()
                );
            }
        }

        // Salva o log no diretório storage/logs
        if (!empty($logEntries)) {
            $logContent = implode("\n", $logEntries);
            Storage::put('logs/invite/' . $this->empresa->id . '/' . $logFileName, $logContent);
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
    public function getLogInviteDownloadLink(string $logFileName): ?string
    {
        if (Storage::exists('logs/invite/' . $this->empresa->id  . '/' . $logFileName)) {
            return route('empresa_funcionario.logInvite', ['empresa' => $this->empresa, 'filename' => $logFileName]);
        }
        return null;
    }
}
