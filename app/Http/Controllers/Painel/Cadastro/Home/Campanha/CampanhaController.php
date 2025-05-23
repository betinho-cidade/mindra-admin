<?php

namespace App\Http\Controllers\Painel\Cadastro\Home\Campanha;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Campanha;
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

        $campanhas_AT = Campanha::where('status','A')->orderBy('titulo')->get();

        $campanhas_IN = Campanha::where('status','I')->orderBy('titulo')->get();

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

        return view('painel.cadastro.home.campanha.create', compact('user', 'formularios'));
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

        return redirect()->route('campanha.index');
    }

    public function show(Campanha $campanha, Request $request)
    {

        if(Gate::denies('edit_campanha')){
            abort('403', 'Página não disponível');
            //return redirect()->back();
        }

        $user = Auth()->User();

        $roles = $user->roles;

        return view('painel.cadastro.home.campanha.show', compact('user', 'campanha'));
    }

    public function update(UpdateRequest $request, Campanha $campanha)
    {
        if(Gate::denies('edit_campanha')){
            abort('403', 'Página não disponível');
        }

        $user = Auth()->User();

        $roles = $user->roles;

        if ($user->id != $campanha->campanha_created) {
            $message = 'Somente o usuário que criou a campanha pode alterá-la.';
            $request->session()->flash('message.level', 'danger');
            $request->session()->flash('message.content', $message);
            return redirect()->route('campanha.show', compact('campanha'));
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

        if ($user->id != $campanha->campanha_created) {
            $message = 'Somente o usuário que criou a campanha pode excluí-la.';
            $request->session()->flash('message.level', 'danger');
            $request->session()->flash('message.content', $message);
            return redirect()->route('campanha.index');
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
