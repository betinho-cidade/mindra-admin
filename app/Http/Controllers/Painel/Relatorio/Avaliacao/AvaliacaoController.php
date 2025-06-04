<?php

namespace App\Http\Controllers\Painel\Relatorio\Avaliacao;

use App\Http\Controllers\Controller;
use App\Models\CampanhaFuncionario;
use App\Models\CampanhaResposta;
use App\Models\FormularioPergunta;
use App\Models\RespostaIndicador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class AvaliacaoController extends Controller
{

    public function __construct(Request $request)
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if(Gate::denies('view_avaliacao')){
            abort('403', 'Página não disponível');
            //return redirect()->back();
        }

        $user = Auth()->User();

        $campanha_funcionarios = CampanhaFuncionario::join('empresa_funcionarios', 'campanha_funcionarios.empresa_funcionario_id', '=', 'empresa_funcionarios.id')
                                                    ->whereIn('empresa_funcionarios.status', ['A'])
                                                    ->join('campanhas', 'campanha_funcionarios.campanha_id', '=', 'campanhas.id')
                                                    ->join('funcionarios', 'empresa_funcionarios.funcionario_id', '=', 'funcionarios.id')
                                                    ->join('users', 'funcionarios.user_id', '=', 'users.id')
                                                    ->where('users.id', $user->id)
                                                    ->select('campanha_funcionarios.*')
                                                    ->orderBy('campanhas.data_inicio')
                                                    ->get();

        return view('painel.relatorio.avaliacao.index', compact('user', 'campanha_funcionarios'));
    }

    public function start(CampanhaFuncionario $campanha_funcionario, Request $request)
    {
        if(Gate::denies('populate_avaliacao')){
            abort('403', 'Página não disponível');
            //return redirect()->back();
        }

        $user = Auth()->User();

        $message = '';

        if($campanha_funcionario->empresa_funcionario->funcionario->user->id != $user->id){
            $request->session()->flash('message.level', 'danger');
            $request->session()->flash('message.content', 'Inconsistência identificada. Não foi possível iniciar a Avaliação!');
            return redirect()->route('avaliacao.index');
        }

        if($campanha_funcionario->campanha->data_finalizada ||
           $campanha_funcionario->campanha->data_fim < Carbon::now()){
            $request->session()->flash('message.level', 'danger');
            $request->session()->flash('message.content', 'A Avaliação dessa campanha já foi realizada!');
            return redirect()->route('avaliacao.index');
        }

        try {
            DB::beginTransaction();

            if(!$campanha_funcionario->data_iniciado){
                $campanha_funcionario->data_iniciado = Carbon::now();
                $campanha_funcionario->save();
            }

            DB::commit();

        } catch (Exception $ex){
            DB::rollBack();
            $message = "Erro desconhecido, por gentileza, entre em contato com o administrador. ".$ex->getMessage();
        }

        if ($message && $message !='') {
            $request->session()->flash('message.level', 'danger');
            $request->session()->flash('message.content', $message);
            return redirect()->route('avaliacao.index');
        }

        return view('painel.relatorio.avaliacao.formulario', compact('user', 'campanha_funcionario'));
    }

    public function store(CampanhaFuncionario $campanha_funcionario, Request $request)
    {
        if(Gate::denies('populate_avaliacao')){
            abort('403', 'Página não disponível');
            //return redirect()->back();
        }

        $user = Auth()->User();

        $message = '';

        if($campanha_funcionario->empresa_funcionario->funcionario->user->id != $user->id){
            $request->session()->flash('message.level', 'danger');
            $request->session()->flash('message.content', 'Inconsistência identificada. Não foi possível concluir a Avaliação!');
            return redirect()->route('avaliacao.index');
        }

        if($campanha_funcionario->campanha->data_finalizada ||
           $campanha_funcionario->campanha->data_fim < Carbon::now()){
            $request->session()->flash('message.level', 'danger');
            $request->session()->flash('message.content', 'A Avaliação dessa campanha já foi realizada!');
            return redirect()->route('avaliacao.index');
        }

        $prefix = 'pergunta_';
        $data = $request->all();

        $filteredData = array_filter($data, function ($key) use ($prefix) {
            return str_starts_with($key, $prefix);
        }, ARRAY_FILTER_USE_KEY);

        $cleanedData = [];
        foreach ($filteredData as $key => $value) {
            $newKey = str_replace($prefix, '', $key); // Remove o prefixo da chave
            $cleanedData[$newKey] = $value;
        }

        $keys_request = array_keys($cleanedData);

        $keys_pergunta = FormularioPergunta::join('formulario_etapas', 'formulario_perguntas.formulario_etapa_id', '=', 'formulario_etapas.id')
                                            ->join('formularios', 'formulario_etapas.formulario_id', '=', 'formularios.id')
                                            ->whereIn('formularios.status', ['A'])
                                            ->where('formularios.id', $campanha_funcionario->campanha->formulario->id)
                                            ->select('formulario_perguntas.id')
                                            ->pluck('formulario_perguntas.id')
                                            ->toArray();

        $differences = array_diff($keys_pergunta, $keys_request);

        if(!empty($differences) && (count($differences)!=0)){
            $request->session()->flash('message.level', 'danger');
            $request->session()->flash('message.content', 'Necessário que todas as perguntas sejam respondidas!');
            return redirect()->route('avaliacao.index');
        }


        try {
            DB::beginTransaction();

            //gravar forulario e atualizar dta finalizado em campanha_funcionario

            $campanha_funcionario->data_realizado = Carbon::now();
            $campanha_funcionario->observacao = $request->observacao;
            $campanha_funcionario->save();

            foreach($keys_request as $pergunta){
                $resposta = 'pergunta_' . $pergunta;

                $newFormularioPergunta = FormularioPergunta::where('id', $pergunta)->first();
                $newRespostaIndicador = RespostaIndicador::where('id', $request->input($resposta))->first();

                if($newFormularioPergunta && $newRespostaIndicador){
                    $newCampanhaResposta = new CampanhaResposta();
                    $newCampanhaResposta->campanha_funcionario_id = $campanha_funcionario->id;
                    $newCampanhaResposta->formulario_pergunta_id = $newFormularioPergunta->id;
                    $newCampanhaResposta->resposta_indicador_id = $newRespostaIndicador->id;
                    $newCampanhaResposta->save();
                }
            }

            $keys_resposta = $campanha_funcionario->campanha_respostas->pluck('formulario_pergunta_id')->toArray();

            $newDifferences = array_diff($keys_pergunta, $keys_resposta);

            if(!empty($newDifferences)){
                DB::rollBack();
                $request->session()->flash('message.level', 'danger');
                $request->session()->flash('message.content', 'Necessário que todas as perguntas sejam respondidas!');
                return redirect()->route('avaliacao.index');
            }

            DB::commit();

        } catch (Exception $ex){
            DB::rollBack();
            $message = "Erro desconhecido, por gentileza, entre em contato com o administrador. ".$ex->getMessage();
        }

        if ($message && $message !='') {
            $request->session()->flash('message.level', 'danger');
            $request->session()->flash('message.content', $message);
        }
        else {
            $request->session()->flash('message.level', 'success');
            $request->session()->flash('message.content', 'Campanha finalizada com suscesso!');
        }

        return redirect()->route('avaliacao.index');
    }

}
