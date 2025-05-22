<?php

namespace App\Http\Controllers\Painel\Gestao\Campanha;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Campanha;
use App\Models\Empresa;
use App\Models\CampanhaEmpresa;
use App\Models\ConsultorEmpresa;
use App\Models\Formulario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Gestao\Campanha\CreateRequest;
use App\Http\Requests\Gestao\Campanha\UpdateRequest;
use Carbon\Carbon;



class CampanhaController extends Controller
{

    public function __construct(Request $request)
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if(Gate::denies('view_campanha')){
            abort('403', 'Página não disponível');
            //return redirect()->back();
        }

        $user = Auth()->User();

        $roles = $user->roles;

        $campanhas_AT = Campanha::where('status','A')->orderBy('titulo')->get();

        $campanhas_IN = Campanha::where('status','I')->orderBy('titulo')->get();

        return view('painel.gestao.campanha.index', compact('user', 'campanhas_AT', 'campanhas_IN'));
    }

    public function create()
    {
        if(Gate::denies('create_campanha')){
            abort('403', 'Página não disponível');
        }

        $user = Auth()->User();
        $roles = $user->roles;

        $formularios = Formulario::whereIn('status', ['A'])->get();

        return view('painel.gestao.campanha.create', compact('user', 'formularios'));
    }

    public function store(CreateRequest $request)
    {
        if(Gate::denies('create_campanha')){
            abort('403', 'Página não disponível');
        }

        $user = Auth()->User();

        $message = '';

        try {

            DB::beginTransaction();

            $campanha = new Campanha();

            $campanha->formulario_id = $request->formulario;
            $campanha->titulo = $request->titulo;
            $campanha->descricao = $request->descricao;
            $campanha->data_inicio = $request->data_inicio . ' 00:00:01';
            $campanha->data_fim = $request->data_fim . ' 23:59:59';
            $campanha->campanha_created = $user->id;
            $campanha->campanha_updated = $user->id;
            $campanha->status = $request->situacao;

            $campanha->save();

            DB::commit();

        } catch (Exception $ex){

            DB::rollBack();

            $message = "Erro desconhecido, por gentileza, entre em contato com o administrador. " . $ex->getMessage();
        }

        if ($message && $message !='') {
            $request->session()->flash('message.level', 'danger');
            $request->session()->flash('message.content', $message);
        } else {
            $request->session()->flash('message.level', 'success');
            $request->session()->flash('message.content', 'A Campanha <code class="highlighter-rouge">'. $request->titulo .'</code> foi criada com sucesso');
        }

        return redirect()->route('campanha.show', compact('campanha'));
    }

    public function show(Campanha $campanha, Request $request)
    {

        if(Gate::denies('edit_campanha')){
            abort('403', 'Página não disponível');
            //return redirect()->back();
        }

        $user = Auth()->User();

        $roles = $user->roles;

        $campanha_empresas = [];
        if ($roles->contains('name', 'Gestor')) {
            $campanha_empresas = CampanhaEmpresa::where('campanha_id', $campanha->id)->get();
        }
        else if ($roles->contains('name', 'Consultor')) {
            $campanha_empresas = CampanhaEmpresa::join('campanhas', 'campanha_empresas.campanha_id', '=', 'campanhas.id')
                                ->where('campanhas.id', $campanha->id)
                                ->join('empresas', 'campanha_empresas.empresa_id', '=', 'empresas.id')
                                ->join('consultor_empresas', 'consultor_empresas.empresa_id', '=', 'empresas.id')
                                ->where('consultor_empresas.consultor_id', $user->consultor->id)
                                ->where('consultor_empresas.status','A')
                                ->orderBy('empresas.nome')
                                ->select('campanha_empresas.*')
                                ->get();
        } else{
            abort('403', 'Página não disponível');
        }

        return view('painel.gestao.campanha.show', compact('user', 'campanha', 'campanha_empresas'));
    }

    public function update(UpdateRequest $request, Campanha $campanha)
    {
        if(Gate::denies('edit_campanha')){
            abort('403', 'Página não disponível');
        }

        $user = Auth()->User();

        $roles = $user->roles;

        $message = '';

        try {

            DB::beginTransaction();

            $campanha->titulo = $request->titulo;
            $campanha->descricao = $request->descricao;
            $campanha->data_inicio = $request->data_inicio . ' 00:00:01';
            $campanha->data_fim = $request->data_fim . ' 23:59:59';

            $campanha->campanha_updated = $user->id;
            $campanha->status = $request->situacao;

            $campanha->save();

            DB::commit();

        } catch (Exception $ex){

            DB::rollBack();

            $message = "Erro desconhecido, por gentileza, entre em contato com o administrador. " . $ex->getMessage();
        }

        if ($message && $message !='') {
            $request->session()->flash('message.level', 'danger');
            $request->session()->flash('message.content', $message);
        } else {
            $request->session()->flash('message.level', 'success');
            $request->session()->flash('message.content', 'A campanha <code class="highlighter-rouge">'. $campanha->titulo .'</code> foi alterada com sucesso');
        }

        return redirect()->route('campanha.show', compact('campanha'));
    }

    public function destroy(campanha $campanha, Request $request)
    {
        if(Gate::denies('delete_campanha')){
            abort('403', 'Página não disponível');
        }

        $user = Auth()->User();

        $message = '';
        $campanha_titulo = $campanha->titulo;

        try {
            DB::beginTransaction();

            $campanha->delete();

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
            $request->session()->flash('message.content', 'A campanha <code class="highlighter-rouge">'. $campanha_titulo .'</code> foi excluída com sucesso');
        }

        return redirect()->route('campanha.index');
    }

    public function empresa_create(campanha $campanha)
    {

        if(Gate::denies('join_campanha_empresa')){
            abort('403', 'Página não disponível');
            //return redirect()->back();
        }

        $user = Auth()->User();
        $roles = $user->roles;

        $empresas = [];
        if ($roles->contains('name', 'Gestor')) {
            $empresas = Empresa::whereIn('empresas.status', ['A'])
                            ->whereNotExists(function($query) use ($campanha)
                            {
                                $query->select(DB::raw(1))
                                    ->from('campanha_empresas')
                                    ->whereRaw('campanha_empresas.empresa_id = empresas.id')
                                    ->where('campanha_empresas.campanha_id', $campanha->id);
                            })
                            ->select('empresas.*')
                            ->get();
        }
        else if ($roles->contains('name', 'Consultor')) {
            $empresas = Empresa::whereIn('empresas.status', ['A'])
                        ->join('consultor_empresas', 'consultor_empresas.empresa_id', '=', 'empresas.id')
                        ->where('consultor_empresas.consultor_id', $user->consultor->id)
                        ->where('consultor_empresas.status','A')
                        ->whereNotExists(function($query) use ($campanha)
                        {
                            $query->select(DB::raw(1))
                                ->from('campanha_empresas')
                                ->whereRaw('campanha_empresas.empresa_id = empresas.id')
                                ->where('campanha_empresas.campanha_id', $campanha->id);
                        })
                        ->select('empresas.*')
                        ->get();
        } else{
            abort('403', 'Página não disponível');
        }

        return view('painel.gestao.campanha.empresa_create', compact('user', 'campanha', 'empresas'));
    }

    public function empresa_store(Request $request, Campanha $campanha)
    {

        if(Gate::denies('join_campanha_empresa')){
            abort('403', 'Página não disponível');
            //return redirect()->back();
        }

        $user = Auth()->User();

        $message = '';

        try {
            DB::beginTransaction();

            foreach($request->empresas as $new_empresa)
            {
                $empresa = Empresa::where('id', $new_empresa)->first();

                if(!$empresa || !$this->valida_consultor($empresa)){
                    abort('403', 'Página não disponível');
                }

                $newCampanhaEmpresa = new CampanhaEmpresa();
                $newCampanhaEmpresa->campanha_id = $campanha->id;
                $newCampanhaEmpresa->empresa_id = $empresa->id;
                $newCampanhaEmpresa->save();
            }

            DB::commit();

        } catch (Exception $ex){

            DB::rollBack();
            if(strpos($ex->getMessage(), 'campanha_empresa_uk') !== false){
                $message = "Uma das empresas informadas já está registrada nessa campanha.";

                $request->session()->flash('message.level', 'warning');
                $request->session()->flash('message.content', $message);

                return redirect()->back()->withInput();

            } else{
                $message = "Erro desconhecido, por gentileza, entre em contato com o administrador. ".$ex->getMessage();
            }
        }

        if ($message && $message !='') {
            $request->session()->flash('message.level', 'danger');
            $request->session()->flash('message.content', $message);
        } else {
            $request->session()->flash('message.level', 'success');
            $request->session()->flash('message.content', 'As empresas foram vinculados com sucesso');
        }

        return redirect()->route('campanha.show', compact('campanha'));
    }

    public function empresa_destroy(Campanha $campanha, CampanhaEmpresa $campanha_empresa, Request $request)
    {
        if(Gate::denies('join_campanha_empresa')){
            abort('403', 'Página não disponível');
        }

        $user = Auth()->User();

        $message = '';

        if(!$this->valida_consultor($campanha_empresa->empresa)){
            abort('403', 'Página não disponível');
        }

        if(($campanha->id == $campanha_empresa->campanha->id)) {

            try {
                DB::beginTransaction();

                CampanhaEmpresa::where('id', $campanha_empresa->id)
                                ->where('campanha_id', $campanha->id)
                                ->delete();

                DB::commit();

            } catch (Exception $ex){

                DB::rollBack();
                $message = "Erro desconhecido, por gentileza, entre em contato com o administrador. ".$ex->getMessage();
            }

        } else {
            $message = "Não foi possível excluir a empresa da campanha - informações inconsistentes.";
        }

        if ($message && $message !='') {
            $request->session()->flash('message.level', 'danger');
            $request->session()->flash('message.content', $message);
        } else {
            $request->session()->flash('message.level', 'success');
            $request->session()->flash('message.content', 'A Empresa foi desvinculada da campanha com sucesso');
        }

        return redirect()->route('campanha.show', compact('campanha'));
    }


    public function preview_formulario(Formulario $formulario, Request $request)
    {
        if(Gate::denies('view_campanha')){
            abort('403', 'Página não disponível');
        }

        $user = Auth()->User();

        return view('painel.gestao.campanha.preview_formulario', compact('user', 'formulario'));
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
