<?php

namespace App\Http\Controllers\Painel\Cadastro\Home\Campanha;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Campanha;
use App\Models\Empresa;
use App\Models\Formulario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Cadastro\Home\Campanha\CreateRequest;
use App\Http\Requests\Cadastro\Home\Campanha\UpdateRequest;
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

        $campanhas_AT = Campanha::where('status','A')
                            ->where(function($query) use ($user)
                            {
                                if($user->roles->first()->name == 'Consultor'){
                                    $query->whereIn('campanhas.empresa_id', $user->consultor->consultor_empresas->pluck('empresa_id'));
                                }
                            })
                            ->orderBy('titulo')->get();

        $campanhas_IN = Campanha::where('status','I')
                            ->where(function($query) use ($user)
                            {
                                if($user->roles->first()->name == 'Consultor'){
                                    $query->whereIn('campanhas.empresa_id', $user->consultor->consultor_empresas->pluck('empresa_id'));
                                }
                            })
                            ->orderBy('titulo')->get();

        return view('painel.cadastro.home.campanha.index', compact('user', 'campanhas_AT', 'campanhas_IN'));
    }

    public function create()
    {
        if(Gate::denies('create_campanha')){
            abort('403', 'Página não disponível');
        }

        $user = Auth()->User();
        $roles = $user->roles;

        $formularios = Formulario::whereIn('status', ['A'])->get();

        $empresas = Empresa::whereIn('status', ['A'])
                                    ->where(function($query) use ($user)
                                    {
                                        if($user->roles->first()->name == 'Consultor'){
                                            $query->whereIn('empresas.id', $user->consultor->consultor_empresas->pluck('empresa_id'));
                                        }
                                    })
                                    ->get();

        return view('painel.cadastro.home.campanha.create', compact('user', 'formularios', 'empresas'));
    }

    public function store(CreateRequest $request)
    {
        if(Gate::denies('create_campanha')){
            abort('403', 'Página não disponível');
        }

        $user = Auth()->User();

        $message = '';

        if($user->roles->first()->name != 'Gestor'){
            if(!in_array(intval($request->empresa), $user->consultor->consultor_empresas->pluck('empresa_id')->toArray(), TRUE)){
                $message = 'Somente é possível criar campanhas para as Empresas liberadas ao Consultor.';
                $request->session()->flash('message.level', 'danger');
                $request->session()->flash('message.content', $message);
                return redirect()->route('campanha.index');
            }
        }

        try {

            DB::beginTransaction();

            $campanha = new Campanha();

            $campanha->empresa_id = $request->empresa;
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

        return redirect()->route('campanha.index');
    }

    public function show(Campanha $campanha, Request $request)
    {

        if(Gate::denies('edit_campanha')){
            abort('403', 'Página não disponível');
            //return redirect()->back();
        }

        $user = Auth()->User();

        if($user->roles->first()->name != 'Gestor'){
            if(!in_array($campanha->empresa->id, $user->consultor->consultor_empresas->pluck('empresa_id')->toArray(), TRUE)){
                $message = 'Somente é possível visualizar campanhas liberadas para as Empresas atendidas pelo Consultor.';
                $request->session()->flash('message.level', 'danger');
                $request->session()->flash('message.content', $message);
                return redirect()->route('campanha.index');
            }
        }

        return view('painel.cadastro.home.campanha.show', compact('user', 'campanha'));
    }

    public function update(UpdateRequest $request, Campanha $campanha)
    {
        if(Gate::denies('edit_campanha')){
            abort('403', 'Página não disponível');
        }

        $user = Auth()->User();

        if($user->roles->first()->name != 'Gestor'){
            if(!in_array($campanha->empresa->id, $user->consultor->consultor_empresas->pluck('empresa_id')->toArray(), TRUE)){
                $message = 'Somente é possível alterar campanhas liberadas para as Empresas atendidas pelo Consultor.';
                $request->session()->flash('message.level', 'danger');
                $request->session()->flash('message.content', $message);
                return redirect()->route('campanha.index');
            }
        }

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

        return redirect()->route('campanha.index');
    }

    public function destroy(Campanha $campanha, Request $request)
    {
        if(Gate::denies('delete_campanha')){
            abort('403', 'Página não disponível');
        }

        $user = Auth()->User();

        if($user->roles->first()->name != 'Gestor'){
            if(!in_array($campanha->empresa->id, $user->consultor->consultor_empresas->pluck('empresa_id')->toArray(), TRUE)){
                $message = 'Somente é possível excluir campanhas liberadas para as Empresas atendidas pelo Consultor.';
                $request->session()->flash('message.level', 'danger');
                $request->session()->flash('message.content', $message);
                return redirect()->route('campanha.index');
            }
        }

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
}
