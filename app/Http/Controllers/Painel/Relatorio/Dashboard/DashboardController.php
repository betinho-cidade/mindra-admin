<?php

namespace App\Http\Controllers\Painel\Relatorio\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Formulario;
use App\Models\FormularioEtapa;
use App\Models\FormularioPergunta;
use App\Models\RespostaIndicador;
use App\Models\CampanhaFuncionario;
use App\Models\Campanha;
use App\Models\ConsultorEmpresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class DashboardController extends Controller
{

    public function __construct(Request $request)
    {
        $this->middleware('auth');
    }


    public function index(Request $request)
    {
        if(Gate::denies('view_dashboard')){
            abort('403', 'Página não disponível');
            //return redirect()->back();
        }

        $user = Auth()->User();
        $roles = $user->roles;

        $empresas_consultor = [];
        if($roles->contains('name', 'Consultor')){
            $empresas_consultor = ConsultorEmpresa::where('consultor_id', $user->consultor->id)
                                                    ->pluck('empresa_id')
                                                    ->toArray();
        }

        $empresas = Empresa::whereIn('status', ['A'])
                            ->where(function($query) use ($roles, $empresas_consultor)
                            {
                                if($roles->contains('name', 'Consultor')){
                                    $query->whereIn('id', $empresas_consultor);
                                }
                            })        
                            ->orderBy('nome')
                            ->get();

        $dash_empresa = [];
        foreach($empresas as $empresa){
            $analise_HSE_empresa = $this->analisaHSE_empresa($empresa);

            array_push($dash_empresa, [
                'Empresa' => $empresa->nome,
                'risco_medio' => $analise_HSE_empresa['risco_medio'],
                'campanha' => '<div style="padding:5px; font-size:14px;"><span style="font-size:12px">'.$analise_HSE_empresa['mes'].'</font><br><b>'.$analise_HSE_empresa['campanha'].'</b></div>',
                'percentual_respondido' => $analise_HSE_empresa['percentual_respondido'],
            ]);
        }

        return view('painel.relatorio.dashboard.index', compact('user', 'dash_empresa', 'empresas'));
    }

    private function analisaHSE_empresa(Empresa $empresa){

        $results = DB::table('campanha_respostas')
                            ->join('campanha_funcionarios', 'campanha_respostas.campanha_funcionario_id', '=', 'campanha_funcionarios.id')
                            ->join('campanhas', function ($join) use($empresa) {
                                $join->on('campanha_funcionarios.campanha_id', '=', 'campanhas.id')
                                    ->where('campanhas.empresa_id',$empresa->id)
                                    ->where('campanhas.formulario_id', 3) // Fixo para ID do Formulário HSE (3)
                                    ->whereIn('campanhas.status', ['A']);
                            })
                            ->join('formulario_perguntas', 'campanha_respostas.formulario_pergunta_id', '=', 'formulario_perguntas.id')
                            ->join('formulario_etapas', 'formulario_perguntas.formulario_etapa_id', '=', 'formulario_etapas.id')
                            ->join('formularios', 'formulario_etapas.formulario_id', '=', 'formularios.id')
                            ->whereIn('formularios.status', ['A'])
                            ->select('campanhas.id as campanha_id', 'formulario_etapas.titulo as titulo_etapa', 'formulario_etapas.descricao as desc_etapa', 'campanha_respostas.formulario_pergunta_id', 'formulario_perguntas.titulo as desc_pergunta', 'campanha_respostas.resposta_indicador_id', DB::raw('COUNT(campanha_respostas.resposta_indicador_id) as count'))
                            ->groupBy('campanhas.id', 'formulario_etapas.titulo', 'formulario_etapas.descricao', 'campanha_respostas.formulario_pergunta_id', 'formulario_perguntas.titulo', 'campanha_respostas.resposta_indicador_id')
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
                    'titulo_etapa' => $result->titulo_etapa,
                    'desc_etapa' => $result->desc_etapa,
                    'pergunta' => $pergunta,
                    'desc_pergunta' => $result->desc_pergunta,
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
                    'titulo_etapa' => $result->titulo_etapa,
                    'desc_etapa' => $result->desc_etapa,
                    'pergunta' => $pergunta,
                    'desc_pergunta' => $result->desc_pergunta,
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

       $indicador_resposta = Formulario::where('id', 3)->first()->resposta->resposta_indicadors()->orderBy('ordem')->pluck('indicador','id')->toArray(); // Fixo para ID do Formulário HSE (3)
       $total_liberado = CampanhaFuncionario::whereIn('campanha_id', $results->unique('campanha_id')->pluck('campanha_id'))->count();
       $total_respondido = CampanhaFuncionario::whereIn('campanha_id', $results->unique('campanha_id')->pluck('campanha_id'))->whereNotNull('data_realizado')->count();

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
            $array['prob_invertida'] = ($total_respondido > 0) ? ($array['resposta_12'] + $array['resposta_13'] + $array['resposta_14'] + $array['resposta_15'] + $array['resposta_16']) / $total_respondido : 0;            
            $array['indice_risco'] = $array['prob_invertida'] * $array['consequencia'];

            if($array['etapa'] != $etapa){
                $etapa = $array['etapa'];
                $total_perguntas = 0;
                $newEtapa = [
                    'etapa' => $array['etapa'],
                    'titulo_etapa' => $array['titulo_etapa'],
                    'desc_etapa' => $array['desc_etapa'],
                    'soma_valores' => 0,
                    'total_perguntas' => 0,
                    'indice_risco_medio' => 0,
                    'indice_risco_round' => 0,
                    'classificacao' => '',
                    'diretriz' => '',
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


        $risco_medio = 0;
        foreach($analise_etapas as $analise_etapa) {
            $risco_medio += $analise_etapa['indice_risco_round'];
        }

        $ultima_campanha = Campanha::whereIn('id', $results->unique('campanha_id')->pluck('campanha_id'))->orderBy('data_inicio', 'desc')->first();
        $retorno_analise = [
            //'total_liberado' => $total_liberado,
            //'total_respondido' => $total_respondido,
            'campanha' => $ultima_campanha->titulo ?? '',
            'mes' => $ultima_campanha->mes_report ?? '',
            'percentual_respondido' => ($total_respondido > 0 && $total_liberado > 0) ? round(($total_respondido/$total_liberado)*100,2) : 0,
            'risco_medio' => round($risco_medio / FormularioEtapa::where('formulario_id', 3)->count()), // Fixo para ID do Formulário HSE (3)
            //'analise_etapas' => $analise_etapas
        ];

        return $retorno_analise;
    }

    public function js_evolucao_empresa(Request $request){

        if(Gate::denies('view_dashboard')){
            abort('403', 'Página não disponível');
            //return redirect()->back();
        }

        $empresa = $request->empresa;

        if(!$this->valida_consultor($empresa) || !$empresa){
            abort('403', 'Página não disponível');
        }        

        $results = DB::table('campanha_respostas')
                            ->join('campanha_funcionarios', 'campanha_respostas.campanha_funcionario_id', '=', 'campanha_funcionarios.id')
                            ->join('campanhas', function ($join) use($empresa) {
                                $join->on('campanha_funcionarios.campanha_id', '=', 'campanhas.id')
                                    ->where('campanhas.empresa_id',$empresa)
                                    ->where('campanhas.formulario_id', 3) // Fixo para ID do Formulário HSE (3)
                                    ->whereIn('campanhas.status', ['A']);
                            })
                            ->join('formulario_perguntas', 'campanha_respostas.formulario_pergunta_id', '=', 'formulario_perguntas.id')
                            ->join('formulario_etapas', 'formulario_perguntas.formulario_etapa_id', '=', 'formulario_etapas.id')
                            ->join('formularios', 'formulario_etapas.formulario_id', '=', 'formularios.id')
                            ->whereIn('formularios.status', ['A'])
                            ->select('campanhas.id as campanha_id', 'formulario_etapas.titulo as titulo_etapa', 'formulario_etapas.descricao as desc_etapa', 'campanha_respostas.formulario_pergunta_id', 'formulario_perguntas.titulo as desc_pergunta', 'campanha_respostas.resposta_indicador_id', DB::raw('COUNT(campanha_respostas.resposta_indicador_id) as count'))
                            ->groupBy('campanhas.id', 'formulario_etapas.titulo', 'formulario_etapas.descricao', 'campanha_respostas.formulario_pergunta_id', 'formulario_perguntas.titulo', 'campanha_respostas.resposta_indicador_id')
                            ->orderBy('campanhas.id')
                            ->orderBy('formulario_etapas.ordem')
                            ->orderBy('formulario_perguntas.ordem')
                            ->get();

        $matriz = [];
        $matrizes = [];
        $campanha = 0;
        $campanha_old = 0;
        $campanhas = [];
        $etapa = 0;
        $pergunta = 0;
        foreach($results as $result){

            if($campanha != $result->campanha_id){
                $campanha_old = $campanha;

                if($matrizes) {
                    array_push($campanhas, ['campanha' => $campanha_old, 'matrizes' => $matrizes]);
                    $matriz = [];
                    $matrizes = [];
                    $etapa = 0;
                    $pergunta = 0;
                }
                $campanha = $result->campanha_id;
            }

            $formulario_pergunta = FormularioPergunta::where('id', $result->formulario_pergunta_id)->first();
            $formulario_etapa = $formulario_pergunta->formulario_etapa;

            if($formulario_etapa->id != $etapa){
                $etapa = $formulario_etapa->id;
                $pergunta = $formulario_pergunta->id;
                $matriz = [
                    'etapa' => $etapa,
                    'titulo_etapa' => $result->titulo_etapa,
                    'desc_etapa' => $result->desc_etapa,
                    'pergunta' => $pergunta,
                    'desc_pergunta' => $result->desc_pergunta,
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
                    'titulo_etapa' => $result->titulo_etapa,
                    'desc_etapa' => $result->desc_etapa,
                    'pergunta' => $pergunta,
                    'desc_pergunta' => $result->desc_pergunta,
                    'consequencia' => $formulario_pergunta->ind_consequencia,
                    'resposta_12' => ($result->resposta_indicador_id == 12) ? $result->count : 0,
                    'resposta_13' => ($result->resposta_indicador_id == 13) ? $result->count : 0,
                    'resposta_14' => ($result->resposta_indicador_id == 14) ? $result->count : 0,
                    'resposta_15' => ($result->resposta_indicador_id == 15) ? $result->count : 0,
                    'resposta_16' => ($result->resposta_indicador_id == 16) ? $result->count : 0,
                ];
                array_push($matrizes, $matriz);

                $matriz_aterada = false;
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

        if($matrizes) {
            array_push($campanhas, ['campanha' => $campanha, 'matrizes' => $matrizes]);
            $matriz = [];
            $matrizes = [];
        }

        $retorno_analise = [];
        $indicador_resposta = Formulario::where('id', 3)->first()->resposta->resposta_indicadors()->orderBy('ordem')->pluck('indicador','id')->toArray(); // Fixo para ID do Formulário HSE (3)
        foreach($campanhas as $campanha){

            $total_liberado = CampanhaFuncionario::where('campanha_id', $campanha['campanha'])->count();
            $total_respondido = CampanhaFuncionario::where('campanha_id', $campanha['campanha'])->whereNotNull('data_realizado')->count();

            $etapa = 0;
            $total_perguntas = 0;
            $newEtapa = [];
            $analise_etapas = [];

            foreach(collect($campanha['matrizes'])->sortBy('etapa') as &$array) {
                $array['resposta_12'] = $array['resposta_12'] * $indicador_resposta['12'];
                $array['resposta_13'] = $array['resposta_13'] * $indicador_resposta['13'];
                $array['resposta_14'] = $array['resposta_14'] * $indicador_resposta['14'];
                $array['resposta_15'] = $array['resposta_15'] * $indicador_resposta['15'];
                $array['resposta_16'] = $array['resposta_16'] * $indicador_resposta['16'];
                $array['prob_invertida'] = ($total_respondido > 0) ? ($array['resposta_12'] + $array['resposta_13'] + $array['resposta_14'] + $array['resposta_15'] + $array['resposta_16']) / $total_respondido : 0;                
                $array['indice_risco'] = $array['prob_invertida'] * $array['consequencia'];

                if($array['etapa'] != $etapa){
                    $etapa = $array['etapa'];
                    $total_perguntas = 0;
                    $newEtapa = [
                        'etapa' => $array['etapa'],
                        'titulo_etapa' => $array['titulo_etapa'],
                        'desc_etapa' => $array['desc_etapa'],
                        'soma_valores' => 0,
                        'total_perguntas' => 0,
                        'indice_risco_medio' => 0,
                        'indice_risco_round' => 0,
                        'classificacao' => '',
                        'diretriz' => '',
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

            $risco_medio = 0;
            foreach($analise_etapas as $analise_etapa) {
                $risco_medio += $analise_etapa['indice_risco_round'];
            }

            $dados_campanha = Campanha::where('id', $campanha['campanha'])->first();
            $retorno = [
                //'total_liberado' => $total_liberado,
                //'total_respondido' => $total_respondido,
                'campanha' => $dados_campanha->titulo ?? '',
                'mes' => $dados_campanha->mes_report ?? '',
                'data_campanha' => $dados_campanha->data_inicio_reduzida,
                'percentual_respondido' => ($total_respondido > 0 && $total_liberado > 0) ? round(($total_respondido/$total_liberado)*100,2) : 0,
                'risco_medio' => round($risco_medio / FormularioEtapa::where('formulario_id', 3)->count()), // Fixo para ID do Formulário HSE (3)
                //'analise_etapas' => $analise_etapas
            ];
            array_push($retorno_analise, $retorno);
        };

        usort($retorno_analise, function($a, $b) {
            // strtotime converte a string de data/hora em um timestamp Unix,
            // o que permite uma comparação numérica fácil.
            $timeA = strtotime($a['data_campanha']);
            $timeB = strtotime($b['data_campanha']);

            // Para ordem ascendente (ASC), se $timeA for menor que $timeB,
            // $a deve vir antes de $b.
            if ($timeA == $timeB) {
                return 0;
            }
            return ($timeA < $timeB) ? -1 : 1;
        });

        echo json_encode($retorno_analise);
    }

    public function js_departamento(Request $request){

        if(Gate::denies('view_dashboard')){
            abort('403', 'Página não disponível');
            //return redirect()->back();
        }

        $empresa = $request->empresa;
        $campanha = $request->campanha;

        if(!$this->valida_consultor($empresa) || !$empresa || !$campanha){
            abort('403', 'Página não disponível');
        }           

        $results = DB::table('campanha_respostas')
                            ->join('campanha_funcionarios', 'campanha_respostas.campanha_funcionario_id', '=', 'campanha_funcionarios.id')
                            ->join('campanhas', function ($join) use($empresa, $campanha) {
                                $join->on('campanha_funcionarios.campanha_id', '=', 'campanhas.id')
                                    ->where('campanhas.id',$campanha)
                                    ->where('campanhas.empresa_id',$empresa)
                                    ->where('campanhas.formulario_id', 3) // Fixo para ID do Formulário HSE (3)
                                    ->whereIn('campanhas.status', ['A']);
                            })
                            ->join('empresa_funcionarios', function ($join) use($empresa) {
                                $join->on('campanha_funcionarios.empresa_funcionario_id', '=', 'empresa_funcionarios.funcionario_id')
                                    ->where('empresa_funcionarios.empresa_id',$empresa);
                            })
                            ->join('formulario_perguntas', 'campanha_respostas.formulario_pergunta_id', '=', 'formulario_perguntas.id')
                            ->join('formulario_etapas', 'formulario_perguntas.formulario_etapa_id', '=', 'formulario_etapas.id')
                            ->join('formularios', 'formulario_etapas.formulario_id', '=', 'formularios.id')
                            ->whereIn('formularios.status', ['A'])
                            ->select('empresa_funcionarios.departamento as departamento', 'formulario_etapas.titulo as titulo_etapa', 'formulario_etapas.descricao as desc_etapa', 'campanha_respostas.formulario_pergunta_id', 'formulario_perguntas.titulo as desc_pergunta', 'campanha_respostas.resposta_indicador_id', DB::raw('COUNT(campanha_respostas.resposta_indicador_id) as count'))
                            ->groupBy('empresa_funcionarios.departamento', 'formulario_etapas.titulo', 'formulario_etapas.descricao', 'campanha_respostas.formulario_pergunta_id', 'formulario_perguntas.titulo', 'campanha_respostas.resposta_indicador_id')
                            ->orderBy('empresa_funcionarios.departamento')
                            ->orderBy('formulario_etapas.ordem')
                            ->orderBy('formulario_perguntas.ordem')
                            ->get();

        $matriz = [];
        $matrizes = [];
        $departamento = 0;
        $departamento_old = 0;
        $departamentos = [];
        $etapa = 0;
        $pergunta = 0;
        foreach($results as $result){

            if($departamento != $result->departamento){
                $departamento_old = $departamento;

                if($matrizes) {
                    array_push($departamentos, ['departamento' => $departamento_old, 'matrizes' => $matrizes]);
                    $matriz = [];
                    $matrizes = [];
                    $etapa = 0;
                    $pergunta = 0;
                }
                $departamento = $result->departamento;
            }

            $formulario_pergunta = FormularioPergunta::where('id', $result->formulario_pergunta_id)->first();
            $formulario_etapa = $formulario_pergunta->formulario_etapa;

            if($formulario_etapa->id != $etapa){
                $etapa = $formulario_etapa->id;
                $pergunta = $formulario_pergunta->id;
                $matriz = [
                    'etapa' => $etapa,
                    'titulo_etapa' => $result->titulo_etapa,
                    'desc_etapa' => $result->desc_etapa,
                    'pergunta' => $pergunta,
                    'desc_pergunta' => $result->desc_pergunta,
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
                    'titulo_etapa' => $result->titulo_etapa,
                    'desc_etapa' => $result->desc_etapa,
                    'pergunta' => $pergunta,
                    'desc_pergunta' => $result->desc_pergunta,
                    'consequencia' => $formulario_pergunta->ind_consequencia,
                    'resposta_12' => ($result->resposta_indicador_id == 12) ? $result->count : 0,
                    'resposta_13' => ($result->resposta_indicador_id == 13) ? $result->count : 0,
                    'resposta_14' => ($result->resposta_indicador_id == 14) ? $result->count : 0,
                    'resposta_15' => ($result->resposta_indicador_id == 15) ? $result->count : 0,
                    'resposta_16' => ($result->resposta_indicador_id == 16) ? $result->count : 0,
                ];
                array_push($matrizes, $matriz);

                $matriz_aterada = false;
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

        if($matrizes) {
            array_push($departamentos, ['departamento' => $departamento, 'matrizes' => $matrizes]);
            $matriz = [];
            $matrizes = [];
        }

        $retorno_analise = [];
        $indicador_resposta = Formulario::where('id', 3)->first()->resposta->resposta_indicadors()->orderBy('ordem')->pluck('indicador','id')->toArray(); // Fixo para ID do Formulário HSE (3)
        foreach($departamentos as $departamento){

            $total_liberado = CampanhaFuncionario::where('campanha_funcionarios.campanha_id', $campanha)
                                                    ->join('empresa_funcionarios', function ($join) use($empresa, $departamento) {
                                                        $join->on('campanha_funcionarios.empresa_funcionario_id', '=', 'empresa_funcionarios.funcionario_id')
                                                            ->where('empresa_funcionarios.empresa_id',$empresa)
                                                            ->where('empresa_funcionarios.departamento',$departamento['departamento']);
                                                    })
                                                    ->count();

            $total_respondido = CampanhaFuncionario::where('campanha_funcionarios.campanha_id', $campanha)
                                                    ->whereNotNull('campanha_funcionarios.data_realizado')            
                                                    ->join('empresa_funcionarios', function ($join) use($empresa, $departamento) {
                                                        $join->on('campanha_funcionarios.empresa_funcionario_id', '=', 'empresa_funcionarios.funcionario_id')
                                                            ->where('empresa_funcionarios.empresa_id',$empresa)
                                                            ->where('empresa_funcionarios.departamento',$departamento['departamento']);
                                                    })
                                                    ->count();        
                                                    
            $etapa = 0;
            $total_perguntas = 0;
            $newEtapa = [];
            $analise_etapas = [];

            foreach(collect($departamento['matrizes'])->sortBy('etapa') as &$array) {
                $array['resposta_12'] = $array['resposta_12'] * $indicador_resposta['12'];
                $array['resposta_13'] = $array['resposta_13'] * $indicador_resposta['13'];
                $array['resposta_14'] = $array['resposta_14'] * $indicador_resposta['14'];
                $array['resposta_15'] = $array['resposta_15'] * $indicador_resposta['15'];
                $array['resposta_16'] = $array['resposta_16'] * $indicador_resposta['16'];
                $array['prob_invertida'] = ($total_respondido > 0) ? ($array['resposta_12'] + $array['resposta_13'] + $array['resposta_14'] + $array['resposta_15'] + $array['resposta_16']) / $total_respondido : 0;
                $array['indice_risco'] = $array['prob_invertida'] * $array['consequencia'];

                if($array['etapa'] != $etapa){
                    $etapa = $array['etapa'];
                    $total_perguntas = 0;
                    $newEtapa = [
                        'etapa' => $array['etapa'],
                        'titulo_etapa' => $array['titulo_etapa'],
                        'desc_etapa' => $array['desc_etapa'],
                        'soma_valores' => 0,
                        'total_perguntas' => 0,
                        'indice_risco_medio' => 0,
                        'indice_risco_round' => 0,
                        'classificacao' => '',
                        'diretriz' => '',
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

            $risco_medio = 0;
            foreach($analise_etapas as $analise_etapa) {
                $risco_medio += $analise_etapa['indice_risco_round'];
            }

            $dados_campanha = Campanha::where('id', $campanha)->first();
            $retorno = [
                //'total_liberado' => $total_liberado,
                //'total_respondido' => $total_respondido,
                'campanha' => $dados_campanha->titulo ?? '',
                'departamento' => $departamento['departamento'],
                'mes' => $dados_campanha->mes_report ?? '',
                'data_campanha' => $dados_campanha->data_inicio_reduzida,
                'percentual_respondido' => ($total_respondido > 0 && $total_liberado > 0) ? round(($total_respondido/$total_liberado)*100,2) : 0,
                'risco_medio' => round($risco_medio / FormularioEtapa::where('formulario_id', 3)->count()), // Fixo para ID do Formulário HSE (3)
                //'analise_etapas' => $analise_etapas
            ];
            array_push($retorno_analise, $retorno);
        };

        usort($retorno_analise, function($a, $b) {
            // strtotime converte a string de data/hora em um timestamp Unix,
            // o que permite uma comparação numérica fácil.
            $timeA = strtotime($a['data_campanha']);
            $timeB = strtotime($b['data_campanha']);

            // Para ordem ascendente (ASC), se $timeA for menor que $timeB,
            // $a deve vir antes de $b.
            if ($timeA == $timeB) {
                return 0;
            }
            return ($timeA < $timeB) ? -1 : 1;
        });

        echo json_encode($retorno_analise);
    }        

    public function js_risco(Request $request){

        if(Gate::denies('view_dashboard')){
            abort('403', 'Página não disponível');
            //return redirect()->back();
        }

        $empresa = $request->empresa;
        $campanha = $request->campanha;
        $departamento = $request->departamento;

        if(!$this->valida_consultor($empresa) || !$empresa || !$campanha){
            abort('403', 'Página não disponível');
        }            

        $results = DB::table('campanha_respostas')
                            ->join('campanha_funcionarios', 'campanha_respostas.campanha_funcionario_id', '=', 'campanha_funcionarios.id')
                            ->join('campanhas', function ($join) use($empresa, $campanha) {
                                $join->on('campanha_funcionarios.campanha_id', '=', 'campanhas.id')
                                    ->where('campanhas.id',$campanha)
                                    ->where('campanhas.empresa_id',$empresa)
                                    ->where('campanhas.formulario_id', 3) // Fixo para ID do Formulário HSE (3)
                                    ->whereIn('campanhas.status', ['A']);
                            })
                            ->join('empresa_funcionarios', function ($join) use($empresa, $departamento) {
                                $join->on('campanha_funcionarios.empresa_funcionario_id', '=', 'empresa_funcionarios.funcionario_id')
                                    ->where('empresa_funcionarios.empresa_id',$empresa)
                                    ->where('empresa_funcionarios.departamento', $departamento);
                            })
                            ->join('formulario_perguntas', 'campanha_respostas.formulario_pergunta_id', '=', 'formulario_perguntas.id')
                            ->join('formulario_etapas', 'formulario_perguntas.formulario_etapa_id', '=', 'formulario_etapas.id')
                            ->join('formularios', 'formulario_etapas.formulario_id', '=', 'formularios.id')
                            ->whereIn('formularios.status', ['A'])
                            ->select('formulario_etapas.titulo as titulo_etapa', 'formulario_etapas.descricao as desc_etapa', 'campanha_respostas.formulario_pergunta_id', 'formulario_perguntas.titulo as desc_pergunta', 'campanha_respostas.resposta_indicador_id', DB::raw('COUNT(campanha_respostas.resposta_indicador_id) as count'))
                            ->groupBy('formulario_etapas.titulo', 'formulario_etapas.descricao', 'campanha_respostas.formulario_pergunta_id', 'formulario_perguntas.titulo', 'campanha_respostas.resposta_indicador_id')
                            ->orderBy('formulario_etapas.ordem')
                            ->orderBy('formulario_perguntas.ordem')
                            ->get();

        $matriz = [];
        $matrizes = [];
        $dimensao = 0;
        $dimensao_old = 0;
        $dimensaos = [];
        $etapa = 0;
        $pergunta = 0;
        foreach($results as $result){

            if($dimensao != $result->titulo_etapa){
                $dimensao_old = $dimensao;

                if($matrizes) {
                    array_push($dimensaos, ['dimensao' => $dimensao_old, 'matrizes' => $matrizes]);
                    $matriz = [];
                    $matrizes = [];
                    $etapa = 0;
                    $pergunta = 0;
                }
                $dimensao = $result->titulo_etapa;
            }

            $formulario_pergunta = FormularioPergunta::where('id', $result->formulario_pergunta_id)->first();
            $formulario_etapa = $formulario_pergunta->formulario_etapa;

            if($formulario_etapa->id != $etapa){
                $etapa = $formulario_etapa->id;
                $pergunta = $formulario_pergunta->id;
                $matriz = [
                    'etapa' => $etapa,
                    'titulo_etapa' => $result->titulo_etapa,
                    'desc_etapa' => $result->desc_etapa,
                    'pergunta' => $pergunta,
                    'desc_pergunta' => $result->desc_pergunta,
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
                    'titulo_etapa' => $result->titulo_etapa,
                    'desc_etapa' => $result->desc_etapa,
                    'pergunta' => $pergunta,
                    'desc_pergunta' => $result->desc_pergunta,
                    'consequencia' => $formulario_pergunta->ind_consequencia,
                    'resposta_12' => ($result->resposta_indicador_id == 12) ? $result->count : 0,
                    'resposta_13' => ($result->resposta_indicador_id == 13) ? $result->count : 0,
                    'resposta_14' => ($result->resposta_indicador_id == 14) ? $result->count : 0,
                    'resposta_15' => ($result->resposta_indicador_id == 15) ? $result->count : 0,
                    'resposta_16' => ($result->resposta_indicador_id == 16) ? $result->count : 0,
                ];
                array_push($matrizes, $matriz);

                $matriz_aterada = false;
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

        if($matrizes) {
            array_push($dimensaos, ['dimensao' => $dimensao, 'matrizes' => $matrizes]);
            $matriz = [];
            $matrizes = [];
        }

        $retorno_analise = [];
        $indicador_resposta = Formulario::where('id', 3)->first()->resposta->resposta_indicadors()->orderBy('ordem')->pluck('indicador','id')->toArray(); // Fixo para ID do Formulário HSE (3)
        foreach($dimensaos as $dimensao){

            // $total_liberado = CampanhaFuncionario::where('campanha_id', $campanha)->count();
            // $total_respondido = CampanhaFuncionario::where('campanha_id', $campanha)->whereNotNull('data_realizado')->count();

            $total_liberado = CampanhaFuncionario::where('campanha_funcionarios.campanha_id', $campanha)
                                                    ->join('empresa_funcionarios', function ($join) use($empresa, $departamento) {
                                                        $join->on('campanha_funcionarios.empresa_funcionario_id', '=', 'empresa_funcionarios.funcionario_id')
                                                            ->where('empresa_funcionarios.empresa_id',$empresa)
                                                            ->where('empresa_funcionarios.departamento',$departamento);
                                                    })
                                                    ->join('campanhas', 'campanha_funcionarios.campanha_id', '=', 'campanhas.id')
                                                    ->join('formularios', 'campanhas.formulario_id', '=', 'formularios.id')
                                                    ->join('formulario_etapas', function ($join) use($departamento, $dimensao) {
                                                        $join->on('formulario_etapas.formulario_id', '=', 'formularios.id')
                                                            ->where('formulario_etapas.titulo',$dimensao['dimensao']);
                                                    })
                                                    ->count();

            $total_respondido = CampanhaFuncionario::where('campanha_funcionarios.campanha_id', $campanha)
                                                    ->whereNotNull('campanha_funcionarios.data_realizado')            
                                                    ->join('empresa_funcionarios', function ($join) use($empresa, $departamento) {
                                                        $join->on('campanha_funcionarios.empresa_funcionario_id', '=', 'empresa_funcionarios.funcionario_id')
                                                            ->where('empresa_funcionarios.empresa_id',$empresa)
                                                            ->where('empresa_funcionarios.departamento',$departamento);
                                                    })
                                                    ->join('campanhas', 'campanha_funcionarios.campanha_id', '=', 'campanhas.id')
                                                    ->join('formularios', 'campanhas.formulario_id', '=', 'formularios.id')
                                                    ->join('formulario_etapas', function ($join) use($departamento, $dimensao) {
                                                        $join->on('formulario_etapas.formulario_id', '=', 'formularios.id')
                                                            ->where('formulario_etapas.titulo',$dimensao['dimensao']);
                                                    })
                                                    ->count();             

            $etapa = 0;
            $total_perguntas = 0;
            $newEtapa = [];
            $analise_etapas = [];

            foreach(collect($dimensao['matrizes'])->sortBy('etapa') as &$array) {
                $array['resposta_12'] = $array['resposta_12'] * $indicador_resposta['12'];
                $array['resposta_13'] = $array['resposta_13'] * $indicador_resposta['13'];
                $array['resposta_14'] = $array['resposta_14'] * $indicador_resposta['14'];
                $array['resposta_15'] = $array['resposta_15'] * $indicador_resposta['15'];
                $array['resposta_16'] = $array['resposta_16'] * $indicador_resposta['16'];
                $array['prob_invertida'] = ($total_respondido > 0) ? ($array['resposta_12'] + $array['resposta_13'] + $array['resposta_14'] + $array['resposta_15'] + $array['resposta_16']) / $total_respondido : 0;
                $array['indice_risco'] = $array['prob_invertida'] * $array['consequencia'];

                if($array['etapa'] != $etapa){
                    $etapa = $array['etapa'];
                    $total_perguntas = 0;
                    $newEtapa = [
                        'etapa' => $array['etapa'],
                        'titulo_etapa' => $array['titulo_etapa'],
                        'desc_etapa' => $array['desc_etapa'],
                        'soma_valores' => 0,
                        'total_perguntas' => 0,
                        'indice_risco_medio' => 0,
                        'indice_risco_round' => 0,
                        'classificacao' => '',
                        'diretriz' => '',
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

            $risco_medio = 0;
            foreach($analise_etapas as $analise_etapa) {
                $risco_medio += $analise_etapa['indice_risco_round'];
            }

            $dados_campanha = Campanha::where('id', $campanha)->first();
            $retorno = [
                //'total_liberado' => $total_liberado,
                //'total_respondido' => $total_respondido,
                'campanha' => $dados_campanha->titulo ?? '',
                'departamento' => $departamento,
                'titulo_etapa' => $dimensao['dimensao'],
                'mes' => $dados_campanha->mes_report ?? '',
                'data_campanha' => $dados_campanha->data_inicio_reduzida,
                //'percentual_respondido' => ($total_respondido > 0 && $total_liberado > 0) ? round(($total_respondido/$total_liberado)*100,2) : 0,
                'risco_medio' => round($risco_medio / FormularioEtapa::where('formulario_id', 3)->count()), // Fixo para ID do Formulário HSE (3)
                //'analise_etapas' => $analise_etapas
            ];
            array_push($retorno_analise, $retorno);
        };

        usort($retorno_analise, function($a, $b) {
            // strtotime converte a string de data/hora em um timestamp Unix,
            // o que permite uma comparação numérica fácil.
            $timeA = strtotime($a['data_campanha']);
            $timeB = strtotime($b['data_campanha']);

            // Para ordem ascendente (ASC), se $timeA for menor que $timeB,
            // $a deve vir antes de $b.
            if ($timeA == $timeB) {
                return 0;
            }
            return ($timeA < $timeB) ? -1 : 1;
        });

        echo json_encode($retorno_analise);
    }        
    
    public function js_busca_campanhas(Request $request){

        if(Gate::denies('view_dashboard')){
            abort('403', 'Página não disponível');
            //return redirect()->back();
        }

        $empresa = $request->empresa;

        if(!$this->valida_consultor($empresa) || !$empresa){
            abort('403', 'Página não disponível');
        }            

        $results = Campanha::where('empresa_id', $empresa)
                            ->where('status', 'A')
                            ->where('formulario_id', 3)
                            ->orderBy('titulo')
                            ->select('id', 'titulo')
                            ->get();

        echo json_encode($results);
    }       
    
    public function js_busca_departamentos(Request $request){

        if(Gate::denies('view_dashboard')){
            abort('403', 'Página não disponível');
            //return redirect()->back();
        }

        $empresa = $request->empresa;
        $campanha = $request->campanha;

        $results = DB::table('campanha_respostas')
                            ->join('campanha_funcionarios', 'campanha_respostas.campanha_funcionario_id', '=', 'campanha_funcionarios.id')
                            ->join('campanhas', function ($join) use($empresa, $campanha) {
                                $join->on('campanha_funcionarios.campanha_id', '=', 'campanhas.id')
                                    ->where('campanhas.id',$campanha)
                                    ->where('campanhas.empresa_id',$empresa)
                                    ->where('campanhas.formulario_id', 3) // Fixo para ID do Formulário HSE (3)
                                    ->whereIn('campanhas.status', ['A']);
                            })
                            ->join('empresa_funcionarios', function ($join) use($empresa) {
                                $join->on('campanha_funcionarios.empresa_funcionario_id', '=', 'empresa_funcionarios.funcionario_id')
                                    ->where('empresa_funcionarios.empresa_id',$empresa);
                            })
                            ->join('formulario_perguntas', 'campanha_respostas.formulario_pergunta_id', '=', 'formulario_perguntas.id')
                            ->join('formulario_etapas', 'formulario_perguntas.formulario_etapa_id', '=', 'formulario_etapas.id')
                            ->join('formularios', 'formulario_etapas.formulario_id', '=', 'formularios.id')
                            ->whereIn('formularios.status', ['A'])
                            ->select('empresa_funcionarios.departamento as departamento')
                            ->groupBy('empresa_funcionarios.departamento')
                            ->orderBy('empresa_funcionarios.departamento')
                            ->get();

        echo json_encode($results);
    }        

    private function valida_consultor($empresa){

        $user = Auth()->User();

        $roles = $user->roles;
        if(!$roles->contains('name', 'Gestor') && !$roles->contains('name', 'Consultor')) {
            return false;
        }
        else if($roles->contains('name', 'Consultor')) {

            $consultor_empresa = ConsultorEmpresa::where('consultor_id', $user->consultor->id)
                                                  ->where('empresa_id', $empresa)
                                                  ->first();
            if(!$consultor_empresa){
                return false;
            }
        }

        return true;
    }


}
