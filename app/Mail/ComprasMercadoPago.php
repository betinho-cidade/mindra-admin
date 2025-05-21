<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ComprasMercadoPago extends Mailable // implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $compras = [];

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Array $compras)
    {
        $this->compras = $compras;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        return $this->from('naoresponda@mindra.com.br')
                    ->subject('Validação das Compras realizadas no Mercado Pago')
                    ->markdown('emails.compras_mercado_pago')
                    ->with('compras', $this->compras);
    }
}
