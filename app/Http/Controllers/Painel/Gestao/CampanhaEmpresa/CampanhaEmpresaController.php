<?php

namespace App\Http\Controllers\Painel\Gestao\CampanhaEmpresa;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Campanha;
use App\Models\Empresa;
use App\Models\ConsultorEmpresa;
use App\Models\Formulario;
use App\Models\FormularioEtapa;
use App\Models\FormularioPergunta;
use App\Models\EmpresaFuncionario;
use App\Models\CampanhaFuncionario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\Mail\FuncionarioAvaliacaoService;
use Illuminate\Support\Facades\Storage;



class CampanhaEmpresaController extends Controller
{

    public function __construct(Request $request)
    {
        $this->middleware('auth');
    }

    public function libera_funcionario(Campanha $campanha, Request $request)
    {
        if(Gate::denies('release_campanha_funcionario')){
            abort('403', 'Página não disponível');
        }

        $user = Auth()->User();

        $message = '';
        $aba = 'Campanhas';
        $empresa = $campanha->empresa;
        $resultado_invite = [];

        if(!$this->valida_consultor($campanha->empresa)){
            abort('403', 'Página não disponível');
        }

            $empresa_funcionarios = EmpresaFuncionario::join('empresas', 'empresa_funcionarios.empresa_id', '=', 'empresas.id')
                                                    ->whereIn('empresa_funcionarios.status', ['A'])
                                                    ->whereIn('empresas.status', ['A'])
                                                    ->where('empresas.id', $campanha->empresa->id)
                                                    ->join('campanhas', 'campanhas.empresa_id', '=', 'empresas.id')
                                                    ->where('campanhas.id', $campanha->id)
                                                    ->whereNotExists(function($query)
                                                                        {
                                                                            $query->select(DB::raw(1))
                                                                                ->from('campanha_funcionarios')
                                                                                ->whereRaw('campanha_funcionarios.empresa_funcionario_id = empresa_funcionarios.id')
                                                                                ->whereColumn('campanha_funcionarios.campanha_id','=','campanhas.id');
                                                                            })

                                                    ->select('empresa_funcionarios.*')
                                                    ->get();

            if(count($empresa_funcionarios) == 0){
                $request->session()->flash('message.level', 'danger');
                $request->session()->flash('message.content', 'Nenhum funcionário disponível para liberação');
                return redirect()->route('empresa_funcionario.show', compact('empresa', 'aba'));
            }

            try {

                DB::beginTransaction();

                foreach($empresa_funcionarios as $empresa_funcionario){
                    $newCampanhaFuncionario = new CampanhaFuncionario();
                    $newCampanhaFuncionario->campanha_id = $campanha->id;
                    $newCampanhaFuncionario->empresa_funcionario_id  = $empresa_funcionario->id;
                    $newCampanhaFuncionario->data_liberado = Carbon::now();
                    $newCampanhaFuncionario->save();
                }

                DB::commit();

            } catch (Exception $ex){

                DB::rollBack();

                if(strpos($ex->getMessage(), 'campanha_funcionario_uk') !== false){
                    $message = "Não é possível incluir o funcionário duas vezes na mesma campanha.";
                } else{
                    $message = "Erro desconhecido, por gentileza, entre em contato com o administrador. ".$ex->getMessage();
                }
            }


            try {
                $service = new FuncionarioAvaliacaoService($empresa_funcionarios->toArray(), $campanha);
                $results = $service->sendInvites();

                $resultado_invite = [
                    'success_count' => count($results['success']),
                    'errors_count' => count($results['failed']),
                    'log_file' => ($results['log_file']) ? $service->getLogAvaliacaoDownloadLink($results['log_file']) : ''
                ];

            } catch (\Exception $e) {

                if($e->getMessage()){
                    $request->session()->flash('message.level', 'danger');
                    $request->session()->flash('message.content', $e->getMessage());
                }

                $resultado_invite = [
                    'success_count' => 0,
                    'errors_count' => 0,
                    'log_file' => ''
                ];
            }


        if ($message && $message !='') {
            $request->session()->flash('message.level', 'danger');
            $request->session()->flash('message.content', $message);
        } else {
            $request->session()->flash('message.level', 'success');
            $request->session()->flash('message.content', 'Processo de liberação executado com sucesso');
        }

        return redirect()->route('empresa_funcionario.show', compact('empresa', 'aba', 'resultado_invite'));
    }

    public function logAvaliacao(Campanha $campanha, Request $request)
    {
        if(Gate::denies('release_campanha_funcionario')){
            abort('403', 'Página não disponível');
        }

        $user = Auth()->User();

        if(!$this->valida_consultor($campanha->empresa)){
            abort('403', 'Página não disponível');
        }

        $filename = $request->filename;

        $filePath = 'logs/invite/' . $campanha->empresa->id . '/campanha/'  . $campanha->id . '/' . $filename;

        if (!Storage::exists($filePath)) {
            abort(404, 'Arquivo de log não encontrado.');
        }

        return Storage::download($filePath, $filename, [
            'Content-Type' => 'text/plain',
        ]);
    }

    public function avaliacaos(Campanha $campanha, Request $request)
    {
        if(Gate::denies('view_empresa_funcionario')){
            abort('403', 'Página não disponível');
        }

        $user = Auth()->User();

        if(!$this->valida_consultor($campanha->empresa)){
            abort('403', 'Página não disponível');
        }

        return view('painel.gestao.campanha_empresa.list', compact('user', 'campanha'));

    }

    public function analisar_hse(Campanha $campanha, Request $request)
    {
        if(Gate::denies('analisa_campanha_funcionario')){
            abort('403', 'Página não disponível');
        }

        $user = Auth()->User();

        $results = DB::table('campanha_respostas')
                            ->join('campanha_funcionarios', 'campanha_respostas.campanha_funcionario_id', '=', 'campanha_funcionarios.id')
                            ->join('campanhas', 'campanha_funcionarios.campanha_id', '=', 'campanhas.id')
                            ->where('campanhas.id', $campanha->id)
                            ->join('formulario_perguntas', 'campanha_respostas.formulario_pergunta_id', '=', 'formulario_perguntas.id')
                            ->join('formulario_etapas', 'formulario_perguntas.formulario_etapa_id', '=', 'formulario_etapas.id')
                            ->select('campanha_respostas.formulario_pergunta_id', 'campanha_respostas.resposta_indicador_id', DB::raw('COUNT(campanha_respostas.resposta_indicador_id) as count'))
                            ->groupBy('campanha_respostas.formulario_pergunta_id', 'campanha_respostas.resposta_indicador_id')
                            ->orderBy('formulario_etapas.ordem')
                            ->orderBy('formulario_perguntas.ordem')
                            ->get();

        $matriz = [];
        $matrizes = [];
        $etapa = 0;
        $pergunta = 0;
        foreach($results as $result){
            $formulario_pergunta = FormularioPergunta::where('id', $result->formulario_pergunta_id)->first();
            $formulario_etapa = $formulario_pergunta->formulario_etapa;

            if($formulario_etapa->id != $etapa){
                $etapa = $formulario_etapa->id;
                $pergunta = $formulario_pergunta->id;
                $matriz = [
                    'etapa' => $etapa,
                    'pergunta' => $pergunta,
                    'consequencia' => $formulario_pergunta->ind_consequencia,
                    'resposta_12' => ($result->resposta_indicador_id == 12) ? $result->count : 0,
                    'resposta_13' => ($result->resposta_indicador_id == 13) ? $result->count : 0,
                    'resposta_14' => ($result->resposta_indicador_id == 14) ? $result->count : 0,
                    'resposta_15' => ($result->resposta_indicador_id == 15) ? $result->count : 0,
                    'resposta_16' => ($result->resposta_indicador_id == 16) ? $result->count : 0,
                ];
                array_push($matrizes, $matriz);
            } elseif($formulario_pergunta->id != $pergunta){
                $pergunta = $formulario_pergunta->id;
                $matriz = [
                    'etapa' => $etapa,
                    'pergunta' => $pergunta,
                    'consequencia' => $formulario_pergunta->ind_consequencia,
                    'resposta_12' => ($result->resposta_indicador_id == 12) ? $result->count : 0,
                    'resposta_13' => ($result->resposta_indicador_id == 13) ? $result->count : 0,
                    'resposta_14' => ($result->resposta_indicador_id == 14) ? $result->count : 0,
                    'resposta_15' => ($result->resposta_indicador_id == 15) ? $result->count : 0,
                    'resposta_16' => ($result->resposta_indicador_id == 16) ? $result->count : 0,
                ];
                array_push($matrizes, $matriz);
            } else {
                foreach ($matrizes as &$array) {
                    if($array['etapa'] === $etapa && $array['pergunta'] === $pergunta) {
                        $array['resposta_12'] = ($result->resposta_indicador_id == 12) ? $result->count : $array['resposta_12'];
                        $array['resposta_13'] = ($result->resposta_indicador_id == 13) ? $result->count : $array['resposta_13'];
                        $array['resposta_14'] = ($result->resposta_indicador_id == 14) ? $result->count : $array['resposta_14'];
                        $array['resposta_15'] = ($result->resposta_indicador_id == 15) ? $result->count : $array['resposta_15'];
                        $array['resposta_16'] = ($result->resposta_indicador_id == 16) ? $result->count : $array['resposta_16'];
                    }
                }
            }
       }

       $indicador_resposta = $campanha->formulario->resposta->resposta_indicadors()->orderBy('ordem')->pluck('indicador','id')->toArray();
       $total_respondido = $campanha->campanha_funcionarios->whereNotNull('data_realizado')->count();

       $etapa = 0;
       $total_perguntas = 0;
       $newEtapa = [];
       $analise_etapas = [];

       foreach(collect($matrizes)->sortBy('etapa') as &$array) {
            $array['resposta_12'] = $array['resposta_12'] * $indicador_resposta['12'];
            $array['resposta_13'] = $array['resposta_13'] * $indicador_resposta['13'];
            $array['resposta_14'] = $array['resposta_14'] * $indicador_resposta['14'];
            $array['resposta_15'] = $array['resposta_15'] * $indicador_resposta['15'];
            $array['resposta_16'] = $array['resposta_16'] * $indicador_resposta['16'];
            $array['prob_invertida'] = ($array['resposta_12'] + $array['resposta_13'] + $array['resposta_14'] + $array['resposta_15'] + $array['resposta_16']) / $total_respondido;
            $array['indice_risco'] = $array['prob_invertida'] * $array['consequencia'];

            if($array['etapa'] != $etapa){
                $etapa = $array['etapa'];
                $total_perguntas = 0;
                $newEtapa = [
                    'etapa' => $array['etapa'],
                    'soma_valores' => 0,
                    'total_perguntas' => 0,
                    'indice_risco_medio' => 0,
                    'indice_risco_round' => 0,
                ];
                array_push($analise_etapas, $newEtapa);
            }

            $total_perguntas++;
            foreach ($analise_etapas as &$newArray) {
                if($newArray['etapa'] === $etapa) {
                    $newArray['soma_valores'] = $newArray['soma_valores'] +  $array['indice_risco'];
                    $newArray['total_perguntas'] = $total_perguntas;
                    $newArray['indice_risco_medio'] = $newArray['soma_valores'] / $total_perguntas;
                    $newArray['indice_risco_round'] = round($newArray['indice_risco_medio']);
                }
            }
       }

       $indice_risco = [
            ['indice' => 1,  'classificacao' => 'Risco Irrelevante', 'diretriz' => 'Monitoramento contínuo. Ações dentro da melhoria contínua.'],
            ['indice' => 2,  'classificacao' => 'Risco Irrelevante', 'diretriz' => 'Monitoramento contínuo. Ações dentro da melhoria contínua.'],
            ['indice' => 3,  'classificacao' => 'Risco Irrelevante', 'diretriz' => 'Monitoramento contínuo. Ações dentro da melhoria contínua.'],
            ['indice' => 4,  'classificacao' => 'Risco Baixo',		'diretriz' => 'Incluir em planos de ação coletivos. Monitorar tendências.'],
            ['indice' => 5,  'classificacao' => 'Risco Baixo',		'diretriz' => 'Incluir em planos de ação coletivos. Monitorar tendências.'],
            ['indice' => 6,  'classificacao' => 'Risco Baixo',		'diretriz' => 'Incluir em planos de ação coletivos. Monitorar tendências.'],
            ['indice' => 7,  'classificacao' => 'Risco Baixo',		'diretriz' => 'Incluir em planos de ação coletivos. Monitorar tendências.'],
            ['indice' => 8,  'classificacao' => 'Risco Moderado',	'diretriz' => 'Prioridade básica. Elaborar plano de ação corretiva.'],
            ['indice' => 9,  'classificacao' => 'Risco Moderado',	'diretriz' => 'Prioridade básica. Elaborar plano de ação corretiva.'],
            ['indice' => 10, 'classificacao' => 'Risco Moderado',	'diretriz' => 'Prioridade básica. Elaborar plano de ação corretiva.'],
            ['indice' => 11, 'classificacao' => 'Risco Moderado',	'diretriz' => 'Prioridade básica. Elaborar plano de ação corretiva.'],
            ['indice' => 12, 'classificacao' => 'Risco Alto',		'diretriz' => 'Ação corretiva prioritária. Incluir em indicadores gerenciais.'],
            ['indice' => 13, 'classificacao' => 'Risco Alto',		'diretriz' => 'Ação corretiva prioritária. Incluir em indicadores gerenciais.'],
            ['indice' => 14, 'classificacao' => 'Risco Alto',		'diretriz' => 'Ação corretiva prioritária. Incluir em indicadores gerenciais.'],
            ['indice' => 15, 'classificacao' => 'Risco Alto',		'diretriz' => 'Ação corretiva prioritária. Incluir em indicadores gerenciais.'],
            ['indice' => 16, 'classificacao' => 'Risco Muito Alto',  'diretriz' => 'Ação imediata. Pode demandar afastamentos, mudanças organizacionais ou suporte clínico.'],
        ];

        dd($results, $analise_etapas, $matrizes, $indice_risco);



        return view('painel.gestao.campanha_empresa.analisar', compact('user', 'campanha', 'pivoted','respostaIds'));





        dd($campanha->campanha_funcionarios->whereNotNull('data_realizado'));

        dd($campanha->formulario->formulario_etapas()->orderBy('ordem')->pluck('titulo','id')->toArray());
        dd($campanha->formulario->resposta->resposta_indicadors()->orderBy('ordem')->pluck('titulo','id')->toArray());

        dd($campanha->campanha_funcionarios->whereNotNull('data_realizado'));

        dd('aqui');

        if(!$this->valida_consultor($campanha->empresa)){
            abort('403', 'Página não disponível');
        }

        return view('painel.gestao.campanha_empresa.list', compact('user', 'campanha'));

    }

    public function destroy_funcionario(Campanha $campanha, CampanhaFuncionario $campanha_funcionario, Request $request)
    {
        if(Gate::denies('release_campanha_funcionario')){
            abort('403', 'Página não disponível');
        }

        $user = Auth()->User();

        $message = '';

        if(!$this->valida_consultor($campanha->empresa)){
            abort('403', 'Página não disponível');
        }

        try {
            DB::beginTransaction();

            CampanhaFuncionario::where('id', $campanha_funcionario->id)
                                ->delete();

            DB::commit();

        } catch (Exception $ex){

            DB::rollBack();

            if(strpos($ex->getMessage(), 'Integrity constraint violation') !== false){
                $message = "Não foi possível excluir o registro, pois existem referências ao mesmo em outros processos.";
            } else{
                $message = "Erro desconhecido, por gentileza, entre em contato com o administrador. ".$ex->getMessage();
            }
        }

        if ($message && $message !='') {
            $request->session()->flash('message.level', 'danger');
            $request->session()->flash('message.content', $message);
        } else {
            $request->session()->flash('message.level', 'success');
            $request->session()->flash('message.content', 'O Funcionário foi desvinculada da Campanha com sucesso');
        }

        return redirect()->route('campanha_empresa.avaliacaos', compact('campanha'));
    }

    private function valida_consultor(Empresa $empresa){

        $user = Auth()->User();

        $roles = $user->roles;
        if(!$roles->contains('name', 'Gestor') && !$roles->contains('name', 'Consultor')) {
            return false;
        }
        else if($roles->contains('name', 'Consultor')) {

            $consultor_empresa = ConsultorEmpresa::where('consultor_id', $user->consultor->id)
                                                  ->where('empresa_id', $empresa->id)
                                                  ->first();
            if(!$consultor_empresa){
                return false;
            }
        }

        return true;
    }
}
