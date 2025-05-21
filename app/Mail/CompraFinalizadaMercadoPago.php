<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\MepaTransacao;


class CompraFinalizadaMercadoPago extends Mailable // implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $mepa_transacao = null;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(MepaTransacao $mepa_transacao)
    {
        $this->mepa_transacao = $mepa_transacao;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        return $this->from('naoresponda@mindra.com.br')
                    ->subject('Compra finalizada com sucesso - Mindra')
                    ->markdown('emails.compras_finalizada_mercado_pago')
                    ->with('mepa_transacao', $this->mepa_transacao);
    }
}
