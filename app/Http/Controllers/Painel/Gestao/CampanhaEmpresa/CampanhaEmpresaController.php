<?php

namespace App\Http\Controllers\Painel\Gestao\CampanhaEmpresa;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Campanha;
use App\Models\Empresa;
use App\Models\CampanhaEmpresa;
use App\Models\ConsultorEmpresa;
use App\Models\Formulario;
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
