<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\MepaConfig;
use App\Models\MepaTransacao;
use App\Models\MepaTransacaoHistorico;
use App\Models\MepaSituacao;
use App\Models\CursoRealizado;

use App\Mail\ComprasMercadoPago;
use App\Mail\CompraFinalizadaMercadoPago;
use Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;

class validaCompraMercadoPagoDaily extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily:compra_mercado_pago';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Diariamente é validado as compras que estão pendentes de todos os assinantes, e verificado se já foi dado baixo para atualização e liberação do acesso aos cursos.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $message = '';
        $lista_compras = [];
        $compras = [];

        try {
            DB::beginTransaction();

            $mepa_config = MepaConfig::where('ambiente', env('AMBIENTE_MEPA'))->first();

            $mepa_transacaos = MepaTransacao::join('mepa_situacaos', 'mepa_transacaos.mepa_situacao_id', '=', 'mepa_situacaos.id')
                                            ->whereIn('mepa_situacaos.status', ['in_process', 'pending'])
                                            ->select('mepa_transacaos.*')
                                            ->get();

            $total_processadas = count($mepa_transacaos);

            foreach($mepa_transacaos as $mepa_transacao){
                $this->autalizar_status_mepa($mepa_config->access_token, $mepa_transacao);
            }

            $mepa_transacaos = MepaTransacao::join('mepa_situacaos', 'mepa_transacaos.mepa_situacao_id', '=', 'mepa_situacaos.id')
                                                ->whereIn('mepa_situacaos.status', ['in_process', 'pending'])
                                                ->select('mepa_transacaos.*')
                                                ->get();

            $total_restantes = count($mepa_transacaos);

            $cont = 0;
            foreach($mepa_transacaos as $mepa_transacao){
                $lista_compras[$cont]['payment_code']       = $mepa_transacao->payment_code;
                $lista_compras[$cont]['Assinante']          = $mepa_transacao->user->name;
                $lista_compras[$cont]['Email']              = $mepa_transacao->user->email;
                $lista_compras[$cont]['tipo_compra']        = $mepa_transacao->tipo_compra;
                $lista_compras[$cont]['curso_pacote']       = ($mepa_transacao->tipo_compra == 'curso') ? $mepa_transacao->curso->nome : $mepa_transacao->pacote->nome;
                $lista_compras[$cont]['mepa_valor']         = 'R$ ' . number_format($mepa_transacao->amount, 2, ',', '.');
                $lista_compras[$cont]['mepa_qtd_parcela']   = $mepa_transacao->installments;
                $lista_compras[$cont]['mepa_valor_parcela'] = 'R$ ' . number_format($mepa_transacao->valor_parcela, 2, ',', '.');
                $lista_compras[$cont]['mepa_valor_final']   = 'R$ ' . number_format($mepa_transacao->valor_final, 2, ',', '.');
                $lista_compras[$cont]['data_compra']        = Carbon::parse($mepa_transacao->created_at)->format('d-m-Y H:i:s');
                $lista_compras[$cont]['mepa_status']        = $mepa_transacao->mepa_situacao->status;
                $lista_compras[$cont]['mepa_status_detail'] = $mepa_transacao->mepa_situacao->status_detail;
                $lista_compras[$cont]['mepa_descricao']     = $mepa_transacao->mepa_situacao->descricao;
                $lista_compras[$cont]['mepa_observacao']    = $mepa_transacao->observacao;
                $cont++;
            }

            $compras = [
                'total_processadas' => $total_processadas,
                'total_restantes' => $total_restantes,
            ];

            DB::commit();


            if($total_restantes > 0){
                Mail::to('naoresponda@mindra.com.br')->send(new ComprasMercadoPago($compras));
            }

            $message = 'Realizado a validação das compras pendentes realizadas no Mercado Pago.';

            $message = $message . '<TOTAL>' . json_encode($compras) . '<LISTA>' . json_encode($lista_compras);

        } catch (Exception $ex){

            DB::rollBack();

            $message = "Erro desconhecido, por gentileza, entre em contato com o administrador. " . $ex->getMessage();
        }

        $this->info($message);

        $header_assinatura = 'Compras Processadas: '. $compras['total_processadas'] . ' Compras Pendentes: ' . $compras['total_restantes'];

        // Create a new Table instance.
        $table = new Table($this->output);

        // Set the table headers.
        $table->setHeaders([
            'PaymentCode', 'Usuário', 'E-mail', 'Tipo Compra', 'Nome', 'Valor', 'Qtd. Parcela', 'Valor Parcela', 'Valor Final', 'Data Compra','Status', 'Status Detail', 'Descrição', 'Observação'
        ]);

        // Create a new TableSeparator instance.
        $separator = new TableSeparator;

        $table->setRow(0, [new TableCell($header_assinatura, ['colspan' => 10])]);
        $table->addRow($separator);
        $table->setRow(1, [new TableCell('', ['colspan' => 10])]);
        $table->addRow($separator);
        $table->addRow($separator);

        $cont=3;
        foreach($lista_compras as $compra){
            $table->setRow($cont, [
				$compra['payment_code'],
                $compra['Assinante'],
                $compra['Email'],
                $compra['tipo_compra'],
                $compra['curso_pacote'],
                $compra['mepa_valor'],
                $compra['mepa_qtd_parcela'],
                $compra['mepa_valor_parcela'],
                $compra['mepa_valor_final'],
                $compra['data_compra'],
                $compra['mepa_status'],
                $compra['mepa_status_detail'],
                $compra['mepa_descricao'],
                $compra['mepa_observacao']
            ]);
            $cont++;
        }
        // Render the table to the output.
        $table->render();

        return 0;
    }

    private function autalizar_status_mepa(String $access_token,  MepaTransacao $mepa_transacao){

        $payment_id = $mepa_transacao->payment_code;

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.mercadopago.com/v1/payments/".$payment_id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "accept: */*",
                "accept-language: en-US,en;q=0.8",
                "content-type: application/json",
                "Authorization: Bearer " . $access_token,
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if($err) {
            //dd("cURL Error #: ", $err);
        } else {
            $novo_status = (json_decode($response));

            if($novo_status->status == $mepa_transacao->mepa_situacao->status){
                if($novo_status->status_detail == $mepa_transacao->mepa_situacao->status_detail){
                    try {
                        DB::beginTransaction();
                        $mepa_transacao->updated_at = Carbon::now();
                        $mepa_transacao->save();
                        DB::commit();
                    } catch (Exception $ex){
                        DB::rollBack();
                    }
                } else {
                    try {
                        DB::beginTransaction();
                        $mepa_situacao = MepaSituacao::where('status_detail', $novo_status->status_detail)->first();

                        $mepa_transacao_historico = new MepaTransacaoHistorico();
                        $mepa_transacao_historico->mepa_transacao_id = $mepa_transacao->id;
                        if($mepa_situacao){
                            $mepa_transacao->mepa_situacao_id = $mepa_situacao->id;
                            $mepa_transacao_historico->mepa_situacao_id = $mepa_situacao->id;
                            $mepa_transacao_historico->observacao = $mepa_situacao->descricao;
                        } else{
                            $mepa_transacao_historico->mepa_situacao_id = $mepa_transacao->mepa_situacao_id;
                            $mepa_transacao_historico->observacao = 'Status MERCADO PAGO alterado: ' . $novo_status->status_detail;
                        }

                        $mepa_transacao_historico->save();

                        $mepa_transacao->updated_at = Carbon::now();
                        $mepa_transacao->save();
                        DB::commit();
                    } catch (Exception $ex){
                        DB::rollBack();
                    }
                }
            } else {
                if($novo_status->status == 'approved'){
                    try {
                        DB::beginTransaction();
                        $mepa_situacao_alternativo = MepaSituacao::where('status', 'approved')->first();
                        $mepa_situacao = MepaSituacao::where('status_detail', $novo_status->status_detail)->first();

                        $mepa_transacao_historico = new MepaTransacaoHistorico();
                        $mepa_transacao_historico->mepa_transacao_id = $mepa_transacao->id;

                        if($novo_status->status_detail == 'accredited'){
                            $mepa_transacao->mepa_situacao_id = $mepa_situacao->id;
                            $mepa_transacao_historico->mepa_situacao_id = $mepa_situacao->id;
                            $mepa_transacao_historico->observacao = $mepa_situacao->descricao;
                        } else {
                            $mepa_transacao->mepa_situacao_id = $mepa_situacao_alternativo->id;
                            $mepa_transacao_historico->mepa_situacao_id = $mepa_situacao_alternativo->id;
                            $mepa_transacao_historico->observacao = $mepa_situacao_alternativo->descricao . ' - Status MERCADO PAGO alterado: ' . $novo_status->status_detail;
                        }

                        $mepa_transacao_historico->save();

                        $mepa_transacao->updated_at = Carbon::now();
                        $mepa_transacao->save();

                        if($mepa_transacao->tipo_compra == 'curso'){
                            $curso_realizado = CursoRealizado::whereIn('situacao', ['P'])
                                                        ->where('curso_id', $mepa_transacao->curso_id)
                                                        ->where('mepa_transacao_id', $mepa_transacao->id)
                                                        ->first();

                            $curso_realizado->situacao = ($curso_realizado->situacao_assinatura == 'P') ? 'L' : $curso_realizado->situacao_assinatura;
                            $curso_realizado->situacao_assinatura = $curso_realizado->situacao;
                            $curso_realizado->save();
                        }else{
                            $curso_realizados = CursoRealizado::whereIn('situacao', ['P'])
                                                                ->where('mepa_transacao_id', $mepa_transacao->id)
                                                                ->get();

                            foreach($curso_realizados as $curso_realizado){
                                    $curso_realizado->situacao = ($curso_realizado->situacao_assinatura == 'P') ? 'L' : $curso_realizado->situacao_assinatura;
                                    $curso_realizado->situacao_assinatura = $curso_realizado->situacao;
                                    $curso_realizado->save();
                            }
                        }

                        try{
                            Mail::to($mepa_transacao->user->email)->send(new CompraFinalizadaMercadoPago($mepa_transacao));
                        } catch (Exception $ex){}

                        DB::commit();
                    } catch (Exception $ex){
                        DB::rollBack();
                        //dd($ex->getMessage());
                    }
                } else{
                    try {
                        DB::beginTransaction();
                        $mepa_situacao = MepaSituacao::where('status_detail', $novo_status->status_detail)->first();

                        $mepa_transacao_historico = new MepaTransacaoHistorico();
                        $mepa_transacao_historico->mepa_transacao_id = $mepa_transacao->id;

                        if($mepa_situacao){
                            $mepa_transacao->mepa_situacao_id = $mepa_situacao->id;
                            $mepa_transacao_historico->mepa_situacao_id = $mepa_situacao->id;
                            $mepa_transacao_historico->observacao = $mepa_situacao->descricao;
                        } else{
                            $mepa_transacao_historico->mepa_situacao_id = $mepa_transacao->mepa_situacao_id;
                            $mepa_transacao_historico->observacao = 'Status MERCADO PAGO alterado: ' . $novo_status->status_detail;
                        }

                        $mepa_transacao_historico->save();

                        $mepa_transacao->updated_at = Carbon::now();
                        $mepa_transacao->save();

                        if($novo_status->status == 'rejected'){
                            CursoRealizado::whereIn('situacao', ['P'])
                                            ->where('mepa_transacao_id', $mepa_transacao->id)
                                            ->delete();
                        }

                        DB::commit();
                    } catch (Exception $ex){
                        DB::rollBack();
                    }
                }
            }
        }
    }
}
