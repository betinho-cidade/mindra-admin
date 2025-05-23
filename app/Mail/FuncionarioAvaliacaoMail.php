<?php

namespace App\Mail;

use App\Models\Funcionario;
use App\Models\CampanhaEmpresa;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FuncionarioAvaliacaoMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $funcionario;
    public $campanha_empresa;

    /**
     * Create a new message instance.
     *
     * @param Funcionario $funcionario
     * @param string $password
     */
    public function __construct(Funcionario $funcionario, CampanhaEmpresa $campanha_empresa)
    {
        $this->funcionario = $funcionario;
        $this->campanha_empresa = $campanha_empresa;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Autoraização para início de Avaliação!')
                    ->view('emails.funcionario_avaliacao');
    }
}
