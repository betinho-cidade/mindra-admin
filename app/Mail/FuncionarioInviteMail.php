<?php

namespace App\Mail;

use App\Models\Funcionario;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FuncionarioInviteMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $funcionario;
    public $password;

    /**
     * Create a new message instance.
     *
     * @param Funcionario $funcionario
     * @param string $password
     */
    public function __construct(Funcionario $funcionario, string $password)
    {
        $this->funcionario = $funcionario;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Bem-vindo ao Nosso Sistema!')
                    ->view('emails.funcionario_invite');
    }
}
