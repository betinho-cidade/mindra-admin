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
use PhpOffice\PhpWord\TemplateProcessor;
use Carbon\Carbon;
use App\Services\Mail\FuncionarioAvaliacaoService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use League\CommonMark\Extension\CommonMark\Node\Block\HtmlBlock;
use Phplot\Phplot;
use Phplot\Phplot\phplot as PhplotPhplot;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Maximum;
use PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report\Php;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report\Html;

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
                            ->whereIn('formularios.visivel_report', ['S'])
                            ->whereIn('formularios.status', ['A'])
                            ->join('formulario_perguntas', 'campanha_respostas.formulario_pergunta_id', '=', 'formulario_perguntas.id')
                            ->join('formulario_etapas', 'formulario_perguntas.formulario_etapa_id', '=', 'formulario_etapas.id')
                            ->join('formularios', 'formulario_etapas.formulario_id', '=', 'formularios.id')
                            ->select('formulario_etapas.titulo as titulo_etapa', 'formulario_etapas.descricao as desc_etapa', 'campanha_respostas.formulario_pergunta_id', 'formulario_perguntas.titulo as desc_pergunta', 'campanha_respostas.resposta_indicador_id', DB::raw('COUNT(campanha_respostas.resposta_indicador_id) as count'))
                            ->groupBy('formulario_etapas.titulo', 'formulario_etapas.descricao', 'campanha_respostas.formulario_pergunta_id', 'formulario_perguntas.titulo', 'campanha_respostas.resposta_indicador_id')
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
                    'prob_inv' => $formulario_pergunta->prob_invertida,    
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
                    'prob_inv' => $formulario_pergunta->prob_invertida,    
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

            if($array['prob_inv'] == 0){
                $array['prob_invertida'] = ($total_respondido > 0) ? ($array['resposta_12'] + $array['resposta_13'] + $array['resposta_14'] + $array['resposta_15'] + $array['resposta_16']) / $total_respondido : 0;            
            } else {
                $array['prob_invertida'] = ($total_respondido > 0) ? $array['prob_inv'] - (($array['resposta_12'] + $array['resposta_13'] + $array['resposta_14'] + $array['resposta_15'] + $array['resposta_16']) / $total_respondido) : 0;            
            }

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

       foreach($analise_etapas as &$analise_etapa){
            $analise_etapa['classificacao'] = $this->textoDiretrizClassificacao($analise_etapa['indice_risco_round'], 'C');
            $analise_etapa['diretriz'] = $this->textoDiretrizClassificacao($analise_etapa['indice_risco_round'], 'D');
       }

       return $this->generateDocument($campanha, $analise_etapas, $matrizes);
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

    private function generateDocument($campanha, $analise_etapas, $matrizes)
    {
        // Caminho do template Word
        $templatePath = storage_path('app/templates/template_hse.docx');

        // Verificar se o template existe
        if (!file_exists($templatePath)) {
            return response()->json(['error' => 'Template não encontrado'], 404);
        }

        // Dados para substituir no template
        $data = [
            'DATA' => now()->format('d/m/Y'),
            'NOME_EMPRESA' => $campanha->empresa->nome,
            'CNPJ_EMPRESA' => $this->formatCnpj($campanha->empresa->cnpj),
            'RUA_EMPRESA' => $campanha->empresa->end_logradouro . ' ' . $campanha->empresa->end_numero,
            'CEP_EMPRESA' => $campanha->empresa->end_cep,
            'ATIVIDADE_EMPRESA' => $campanha->empresa->atividade_principal,
            'QTD_FUNC_EMPRESA' => $campanha->empresa->qtd_funcionario,
            'ANALISE_ETAPA_1' => '',
            'ANALISE_ETAPA_2' => '',
            'ANALISE_ETAPA_3' => '',
            'ANALISE_ETAPA_4' => '',
            'ANALISE_ETAPA_5' => '',
            'ANALISE_ETAPA_6' => '',
            'ANALISE_ETAPA_7' => '',
            'ANALISE_ETAPA_8' => '',
            'ANALISE_ETAPA_9' => '',
            'ANALISE_ETAPA_10' => '',
            'ANALISE_ETAPA_11' => '',
            'ANALISE_ETAPA_12' => '',
            'ANALISE_ETAPA_13' => '',
            'ANALISE_ETAPA_14' => '',
            'ANALISE_ETAPA_15' => '',
            'ANALISE_ETAPA_16' => '',
            'ANALISE_ETAPA_17' => '',
            'ANALISE_ETAPA_18' => '',
            'ANALISE_ETAPA_19' => '',
            'ANALISE_ETAPA_20' => '',
            'ANALISE_ETAPA_21' => '',
            'ANALISE_ETAPA_22' => '',
            'ANALISE_ETAPA_23' => '',
            'ANALISE_ETAPA_24' => '',
            'ANALISE_ETAPA_25' => '',
            'ANALISE_ETAPA_26' => '',
            'ANALISE_ETAPA_27' => '',
            'ANALISE_ETAPA_28' => '',
            'ANALISE_ETAPA_29' => '',
            'ANALISE_ETAPA_30' => '',
            'ANALISE_ETAPA_31' => '',
            'ANALISE_ETAPA_32' => '',
            'ANALISE_ETAPA_33' => '',
            'ANALISE_ETAPA_34' => '',
            'ANALISE_ETAPA_35' => '',
        ];

        try {
            $templateProcessor = new TemplateProcessor($templatePath);

            $cnt_irrelevante = 0;
            $cnt_baixo = 0;
            $cnt_moderado = 0;
            $cnt_alto = 0;
            $cnt_malto = 0;
            foreach($analise_etapas as $analise_etapa){

                $valor = $analise_etapa['indice_risco_round'];

                switch (true) {
                    case ($valor >= 0 && $valor <= 3):
                        $cnt_irrelevante++;
                        if($cnt_irrelevante == 1){$templateProcessor->cloneRow('ET_IRRELEVANTE_DIM',7);}
                        $templateProcessor->cloneRow('ET_IRRELEVANTE_DIM',1);
                        $templateProcessor->setValue('ET_IRRELEVANTE_DIM#'.$cnt_irrelevante, $analise_etapa['titulo_etapa']);
                        $templateProcessor->setValue('ET_IRRELEVANTE_IND#'.$cnt_irrelevante, $analise_etapa['indice_risco_round']);
                        $templateProcessor->setValue('ET_IRRELEVANTE_CLAS#'.$cnt_irrelevante, $analise_etapa['classificacao']);
                        $templateProcessor->setValue('ET_IRRELEVANTE_DIR#'.$cnt_irrelevante, $analise_etapa['diretriz']);
                        break;
                    case ($valor > 3 && $valor <= 7):
                        $cnt_baixo++;
                        if($cnt_baixo == 1){$templateProcessor->cloneRow('ET_BAIXO_DIM',7);}
                        $templateProcessor->setValue('ET_BAIXO_DIM#'.$cnt_baixo, $analise_etapa['titulo_etapa']);
                        $templateProcessor->setValue('ET_BAIXO_IND#'.$cnt_baixo, $analise_etapa['indice_risco_round']);
                        $templateProcessor->setValue('ET_BAIXO_CLAS#'.$cnt_baixo, $analise_etapa['classificacao']);
                        $templateProcessor->setValue('ET_BAIXO_DIR#'.$cnt_baixo, $analise_etapa['diretriz']);
                        break;
                    case ($valor > 7 && $valor <= 11):
                        $cnt_moderado++;
                        if($cnt_moderado == 1){$templateProcessor->cloneRow('ET_MODERADO_DIM',7);}
                        $templateProcessor->setValue('ET_MODERADO_DIM#'.$cnt_moderado, $analise_etapa['titulo_etapa']);
                        $templateProcessor->setValue('ET_MODERADO_IND#'.$cnt_moderado, $analise_etapa['indice_risco_round']);
                        $templateProcessor->setValue('ET_MODERADO_CLAS#'.$cnt_moderado, $analise_etapa['classificacao']);
                        $templateProcessor->setValue('ET_MODERADO_DIR#'.$cnt_moderado, $analise_etapa['diretriz']);
                        break;
                    case ($valor > 11 && $valor <= 15):
                        $cnt_alto++;
                        if($cnt_alto == 1){$templateProcessor->cloneRow('ET_ALTO_DIM',7);}
                        $templateProcessor->setValue('ET_ALTO_DIM#'.$cnt_alto, $analise_etapa['titulo_etapa']);
                        $templateProcessor->setValue('ET_ALTO_IND#'.$cnt_alto, $analise_etapa['indice_risco_round']);
                        $templateProcessor->setValue('ET_ALTO_CLAS#'.$cnt_alto, $analise_etapa['classificacao']);
                        $templateProcessor->setValue('ET_ALTO_DIR#'.$cnt_alto, $analise_etapa['diretriz']);
                        break;
                    case ($valor > 15):
                        $cnt_malto++;
                        if($cnt_malto == 1){$templateProcessor->cloneRow('ET_MALTO_DIM',7);}
                        $templateProcessor->setValue('ET_MALTO_DIM#'.$cnt_malto, $analise_etapa['titulo_etapa']);
                        $templateProcessor->setValue('ET_MALTO_IND#'.$cnt_malto, $analise_etapa['indice_risco_round']);
                        $templateProcessor->setValue('ET_MALTO_CLAS#'.$cnt_malto, $analise_etapa['classificacao']);
                        $templateProcessor->setValue('ET_MALTO_DIR#'.$cnt_malto, $analise_etapa['diretriz']);
                        break;
                    default:
                        $resultado = 'Fora do intervalo';
                }
            }

            if ($cnt_irrelevante >= 0 && $cnt_irrelevante < 7){
                if($cnt_irrelevante == 0){
                    $templateProcessor->deleteRow('ET_IRRELEVANTE_DIM', 1);
                } else {
                    for($i=7; $i>$cnt_irrelevante; $i--){
                        $templateProcessor->deleteRow('ET_IRRELEVANTE_DIM#'.$i, 1);
                    }
                }
            }
            if ($cnt_baixo >= 0 && $cnt_baixo < 7){
                if($cnt_baixo == 0){
                    $templateProcessor->deleteRow('ET_BAIXO_DIM', 1);
                } else {
                    for($i=7; $i>$cnt_baixo; $i--){
                        $templateProcessor->deleteRow('ET_BAIXO_DIM#'.$i, 1);
                    }
                }
            }
            if ($cnt_moderado >= 0 && $cnt_moderado < 7){
                if($cnt_moderado == 0){
                    $templateProcessor->deleteRow('ET_MODERADO_DIM', 1);
                } else {
                    for($i=7; $i>$cnt_moderado; $i--){
                        $templateProcessor->deleteRow('ET_MODERADO_DIM#'.$i, 1);
                    }
                }
            }
            if ($cnt_alto >= 0 && $cnt_alto < 7){
                if($cnt_alto == 0){
                    $templateProcessor->deleteRow('ET_ALTO_DIM', 1);
                } else {
                    for($i=7; $i>$cnt_alto; $i--){
                        $templateProcessor->deleteRow('ET_ALTO_DIM#'.$i, 1);
                    }
                }
            }
            if ($cnt_malto >= 0 && $cnt_malto < 7){
                if($cnt_malto == 0){
                    $templateProcessor->deleteRow('ET_MALTO_DIM', 1);
                } else {
                    for($i=7; $i>$cnt_malto; $i--){
                        $templateProcessor->deleteRow('ET_MALTO_DIM#'.$i, 1);
                    }
                }
            }

            //Substituir LOGO Empresa
            $imageLogoPath = base_path() . '/public/images/empresa/'.$campanha->empresa->id.'/'.$campanha->empresa->path_imagem;
            $templateProcessor->setImageValue('LOGO_EMPRESA', [
                        'path' => $imageLogoPath,
                        'width' => 250,
                        'height' => 125,
                    ],100);

            $cont = 0; $images_list = [];
            $indice_imagem = now()->format('YmdHis');
            // Carregar o template e substituir as imagens das perguntas
            foreach($matrizes as $matriz){
                $imagePath = $this->generateImageWithBarChart($matriz, $indice_imagem);
                array_push($images_list, $imagePath);

                $chart = 'CHART_' . ++$cont;
                $templateProcessor->setImageValue($chart, [
                        'path' => $imagePath,
                        'width' => 400,
                        'height' => 250,
                    ], 1);

                $data['ANALISE_ETAPA_'.$cont] = $matriz['desc_etapa'];
            }

            //Substituir os placeholders
            foreach($data as $key => $value) {
                $templateProcessor->setValue($key, $value);
            }

            $observacaos = CampanhaFuncionario::where('campanha_id', $campanha->id)
                                               ->whereNotNull('observacao')
                                                ->get();
            if($observacaos->count() > 1){
                $templateProcessor->cloneRow('TEXTO_N', $observacaos->count());
                //Substituir os placeholders
                $indexObservacao = 0;
                foreach($observacaos as $observacao) {
                    $indexObservacao++;
                    $rowIndex = $indexObservacao; // Índice da linha (começa em 1)
                    $templateProcessor->setValue("TEXTO_N#{$rowIndex}", $observacao->observacao);
                }
            } elseif($observacaos->count() == 1) {
                $templateProcessor->setValue("TEXTO_N", $observacaos->first()->observacao);
            } elseif (empty($observacoes)) {
                // Remove a linha da tabela se não houver observações
                $templateProcessor->deleteRow('TEXTO_N', 1);
            }


            $fileName = 'output_' . time() . '.docx';
            $tempPath = storage_path('app/public/documents/' . $fileName);

            // Salvar o documento
            //$templateProcessor->saveAs($outputPath);
            $templateProcessor->saveAs($tempPath);


        // Limpar arquivo temporário
        //unlink($tempFile);

            foreach($images_list as $image){
                if (file_exists($image)) {
                    // Remover o arquivo temporário da imagem do gráfico após o uso
                    unlink($image);
                }
            }

            // Retornar o arquivo para download
            //return response()->download($outputPath)->deleteFileAfterSend(true);
            // Configurar os cabeçalhos para download
            $headers = [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ];
            return response()->file($tempPath, $headers)->deleteFileAfterSend(true);

            //return response()->download($outputPath)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao gerar o documento: ' . $e->getMessage()], 500);
        }
    }

    public function generateImageWithBarChart($matriz, $indice_imagem)
    {
        // --- 1. Gerar o Gráfico de Barras com PHPlot ---
        // https://phplot.sourceforge.net/phplotdocs/
        try{
            $data = array(
                array('Sempre', $matriz['resposta_12']),
                array('Frequentemente', $matriz['resposta_13']),
                array('Às vezes', $matriz['resposta_14']),
                array('Raramente',$matriz['resposta_15']),
                array('Nunca', $matriz['resposta_16']),
                );

            $plot = new PhplotPhplot(500, 300);

            $plot->SetFontTTF('title', storage_path('app/public/fonts/calibri-bold.ttf'));
            $plot->SetFontTTF('y_label', storage_path('app/public/fonts/calibri-regular.ttf'));

            $plot->SetImageBorderType('plain'); // Improves presentation in the manual
            // $plot->SetTitle("Average Annual Precipitation (inches)\n"
            //                 . "Selected U.S. Cities");

            $titulo = $this->breakStringIntoLines($matriz['desc_pergunta']);
            $plot->SetTitle($titulo);

            $plot->SetBackgroundColor('white');
            #  Set a tiled background image:
            $plot->SetPlotAreaBgImage('images/graygradient.png', 'centeredtile');
            #  Force the X axis range to start at 0:
            $plot->SetPlotAreaWorld(0);
            #  No ticks along Y axis, just bar labels:
            $plot->SetYTickPos('none');
            #  No ticks along X axis:
            $plot->SetXTickPos('none');
            #  No X axis labels. The data values labels are sufficient.
            //$plot->SetXTickLabelPos('none');

            #  Turn on the data value labels:
            $plot->SetXDataLabelPos('plotin');
            #  No grid lines are needed:
            $plot->SetDrawXGrid(FALSE);
            #  Set the bar fill color:
            $plot->SetDataColors('#1B6487');
            #  Use less 3D shading on the bars:
            $plot->SetShading(2);
            $plot->SetDataValues($data);
            $plot->SetDataType('text-data-yx');
            $plot->SetPlotType('bars');

            $tempImagePath = 'temp_charts/chart_' . $indice_imagem . '_' . $matriz['etapa']  . '_' . $matriz['pergunta'] .  '.png';
            $fullTempImagePath = Storage::disk('public')->path($tempImagePath);

            Storage::disk('public')->makeDirectory('temp_charts');

            // PHPlot pode renderizar diretamente para um arquivo
            $plot->SetIsInline(true);
            $plot->SetOutputFile($fullTempImagePath);

            $plot->DrawGraph();

            return $fullTempImagePath;

        }catch(Exception $ex){
            dd($ex->getMessage());
        }
    }

    private function breakStringIntoLines($inputString) {
        // Divide a string em tokens usando espaço como delimitador
        $tokens = explode(' ', trim($inputString));

        // Agrupa os tokens em blocos de 8 palavras
        $lines = [];
        for ($i = 0; $i < count($tokens); $i += 8) {
            // Pega o próximo grupo de até 8 palavras
            $line = array_slice($tokens, $i, 8);
            // Junta as palavras do grupo com espaço e adiciona à lista de linhas
            $lines[] = implode(' ', $line);
        }

        // Concatena as linhas com quebra de linha
        return implode("\n", $lines);
    }

    private function formatCnpj($cnpj)
    {
        // Remove qualquer caractere que não seja número
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);

        // Verifica se tem 14 dígitos
        if (strlen($cnpj) === 14) {
            // Aplica a máscara 12.345.678/0001-95
        return sprintf('%s.%s.%s/%s-%s',
                substr($cnpj, 0, 2), // 12
                substr($cnpj, 2, 3), // 345
                substr($cnpj, 5, 3), // 678
                substr($cnpj, 8, 4), // 0001
                substr($cnpj, 12, 2) // 95
            );
        }
        return $cnpj; // Retorna sem formatação se inválido
    }

    private function textoDiretrizClassificacao($indice, $tipo)
    {

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

        // Buscar o item correspondente ao índice
        foreach($indice_risco as $item) {
            if ($item['indice'] === (int)$indice) {
                return $tipo === 'C' ? $item['classificacao'] : $item['diretriz'];
            }
        }

        if((int)$indice > 16){
            return $tipo === 'C' ? 'Risco Muito Alto' : 'Ação imediata. Pode demandar afastamentos, mudanças organizacionais ou suporte clínico.';
        }

        // Retornar valor padrão se o índice não for encontrado
        return $tipo === 'C' ? 'Risco Não Classificado' : 'Diretriz Não Definida';
    }

    private function getCorFundo($indice)
    {
        // Definir cores com base no intervalo de índices (ajustado conforme a imagem)
        if ($indice <= 3) {
            return 'C6EFCE'; // Verde claro (Risco Irrelevante)
        } elseif ($indice <= 7) {
            return 'FFEB9C'; // Amarelo claro (Risco Baixo)
        } elseif ($indice <= 11) {
            return 'F9CB9C'; // Laranja claro (Risco Moderado)
        } elseif ($indice <= 15) {
            return 'FF9999'; // Vermelho claro (Risco Alto)
        } else {
            return 'FF6666'; // Vermelho escuro (Risco Muito Alto)
        }
    }

}
