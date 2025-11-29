<?php

namespace App\Http\Controllers\Painel\Relatorio\Checklist;

use App\Http\Controllers\Controller;
use App\Models\ChecklistConsultor;
use App\Models\ChecklistResposta;
use App\Models\ChecklistPergunta;
use App\Models\RespostaIndicador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class ChecklistController extends Controller
{

    public function __construct(Request $request)
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if(Gate::denies('view_checklist')){
            abort('403', 'Página não disponível');
            //return redirect()->back();
        }

        $user = Auth()->User();

        $checklist_consultors = ChecklistConsultor::join('consultor_empresas', 'checklist_consultors.consultor_empresa_id', '=', 'consultor_empresas.id')
                                                    ->whereIn('consultor_empresas.status', ['A'])
                                                    ->join('campanhas', 'checklist_consultors.campanha_id', '=', 'campanhas.id')
                                                    ->join('consultors', 'consultor_empresas.consultor_id', '=', 'consultors.id')
                                                    ->join('users', 'consultors.user_id', '=', 'users.id')
                                                    ->where('users.id', $user->id)
                                                    ->select('checklist_consultors.*')
                                                    ->orderBy('campanhas.data_inicio')
                                                    ->get();

        return view('painel.relatorio.checklist.index', compact('user', 'checklist_consultors'));
    }

    public function start(ChecklistConsultor $checklist_consultor, Request $request)
    {
        if(Gate::denies('populate_checklist')){
            abort('403', 'Página não disponível');
            //return redirect()->back();
        }

        $user = Auth()->User();

        $message = '';

        if($checklist_consultor->consultor_empresa->consultor->user->id != $user->id){
            $request->session()->flash('message.level', 'danger');
            $request->session()->flash('message.content', 'Inconsistência identificada. Não foi possível iniciar o Checklist!');
            return redirect()->route('checklist.index');
        }

        if($checklist_consultor->campanha->data_finalizada ||
           $checklist_consultor->campanha->data_fim < Carbon::now()){
            $request->session()->flash('message.level', 'danger');
            $request->session()->flash('message.content', 'O Checklist dessa campanha já foi realizado!');
            return redirect()->route('checklist.index');
        }

        try {
            DB::beginTransaction();

            if(!$checklist_consultor->data_iniciado){
                $checklist_consultor->data_iniciado = Carbon::now();
                $checklist_consultor->save();
            }

            DB::commit();

        } catch (Exception $ex){
            DB::rollBack();
            $message = "Erro desconhecido, por gentileza, entre em contato com o administrador. ".$ex->getMessage();
        }

        if ($message && $message !='') {
            $request->session()->flash('message.level', 'danger');
            $request->session()->flash('message.content', $message);
            return redirect()->route('checklist.index');
        }

        return view('painel.relatorio.checklist.formulario', compact('user', 'checklist_consultor'));
    }

    public function store(ChecklistConsultor $checklist_consultor, Request $request)
    {
        if(Gate::denies('populate_checklist')){
            abort('403', 'Página não disponível');
            //return redirect()->back();
        }

        $user = Auth()->User();

        $message = '';

        if($checklist_consultor->consultor_empresa->consultor->user->id != $user->id){
            $request->session()->flash('message.level', 'danger');
            $request->session()->flash('message.content', 'Inconsistência identificada. Não foi possível concluir o Checklist!');
            return redirect()->route('avaliacao.index');
        }

        if($checklist_consultor->campanha->data_finalizada ||
           $checklist_consultor->campanha->data_fim < Carbon::now()){
            $request->session()->flash('message.level', 'danger');
            $request->session()->flash('message.content', 'O Checklist dessa campanha já foi realizado!');
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

        $keys_pergunta = ChecklistPergunta::join('checklist_etapas', 'checklist_perguntas.checklist_etapa_id', '=', 'checklist_etapas.id')
                                            ->join('checklists', 'checklist_etapas.checklist_id', '=', 'checklists.id')
                                            ->whereIn('checklists.status', ['A'])
                                            ->where('checklists.id', $checklist_consultor->campanha->checklist->id)
                                            ->select('checklist_perguntas.id')
                                            ->pluck('checklist_perguntas.id')
                                            ->toArray();

        $differences = array_diff($keys_pergunta, $keys_request);

        if(!empty($differences) && (count($differences)!=0)){
            $request->session()->flash('message.level', 'danger');
            $request->session()->flash('message.content', 'Necessário que todas as perguntas sejam respondidas!');
            return redirect()->route('avaliacao.index');
        }


        try {
            DB::beginTransaction();

            //gravar forulario e atualizar dta finalizado em checklist_consultor

            $checklist_consultor->data_realizado = Carbon::now();
            $checklist_consultor->observacao = $request->observacao;
            $checklist_consultor->save();

            foreach($keys_request as $pergunta){
                $resposta = 'pergunta_' . $pergunta;

                $newChecklistPergunta = ChecklistPergunta::where('id', $pergunta)->first();
                $newRespostaIndicador = RespostaIndicador::where('id', $request->input($resposta))->first();

                if($newChecklistPergunta && $newRespostaIndicador){
                    $newChecklistResposta = new ChecklistResposta();
                    $newChecklistResposta->checklist_consultor_id = $checklist_consultor->id;
                    $newChecklistResposta->checklist_pergunta_id = $newChecklistPergunta->id;
                    $newChecklistResposta->resposta_indicador_id = $newRespostaIndicador->id;
                    $newChecklistResposta->save();
                }
            }

            $keys_resposta = $checklist_consultor->checklist_respostas->pluck('checklist_pergunta_id')->toArray();

            $newDifferences = array_diff($keys_pergunta, $keys_resposta);

            if(!empty($newDifferences)){
                DB::rollBack();
                $request->session()->flash('message.level', 'danger');
                $request->session()->flash('message.content', 'Necessário que todas as perguntas sejam respondidas!');
                return redirect()->route('checklist.index');
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
            $request->session()->flash('message.content', 'Checklist da Campanha finalizado com suscesso!');
        }

        return redirect()->route('checklist.index');
    }

}
