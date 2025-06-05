<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Funcionario;

class SendResetPasswordMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $token;
    public $funcionario;

    public function __construct($funcionario, $token)
    {
        $this->funcionario = $funcionario;
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $view = 'emails.reset_password.verify_link';

        return $this->subject('Mindra - Link para troca de Senha')
                    ->from('naoresponda@mindra.com.br')
                    ->view($view);
    }
}
