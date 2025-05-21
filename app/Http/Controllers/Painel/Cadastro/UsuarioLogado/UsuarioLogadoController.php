<?php

namespace App\Http\Controllers\Painel\Cadastro\UsuarioLogado;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Membro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Cadastro\UsuarioLogado\UpdateRequest;

class UsuarioLogadoController extends Controller
{

    public function __construct(Request $request)
    {
        $this->middleware('auth');
    }

    public function show(User $user)
    {

        if(Gate::denies('view_usuario_logado')){
            abort('403', 'Página não disponível');
            //return redirect()->back();
        }

        $usuario_logado = Auth()->User();

        if($user->id != $usuario_logado->id){
            abort('403', 'Página não disponível');
        }

        return view('painel.cadastro.usuario_logado.show', compact('user', 'usuario_logado'));
    }

    public function update(UpdateRequest $request, User $usuario_logado)
    {

        if(Gate::denies('edit_usuario_logado')){
            abort('403', 'Página não disponível');
            //return redirect()->back();
        }

        $user = Auth()->User();

        if($usuario_logado->id != $user->id){
            abort('403', 'Página não disponível');
        }

        $message = '';

        try {

            DB::beginTransaction();

            $usuario_logado->nome = $request->nome;
            $usuario_logado->email = $request->email;
            $usuario_logado->cpf = $request->cpf;
            $usuario_logado->data_nascimento = $request->data_nascimento;
            $usuario_logado->rg = $request->rg;
            $usuario_logado->sexo = $request->sexo;
            $usuario_logado->telefone = $request->telefone;
            $usuario_logado->end_cep = $request->end_cep;
            $usuario_logado->end_cidade = $request->end_cidade;
            $usuario_logado->end_uf = $request->end_uf;
            $usuario_logado->end_logradouro = $request->end_logradouro;
            $usuario_logado->end_numero = $request->end_numero;
            $usuario_logado->end_bairro = $request->end_bairro;
            $usuario_logado->end_complemento = $request->end_complemento;

            if($request->password){
                $usuario_logado->password = bcrypt($request->password);
            }

            $usuario_logado->save();

            DB::commit();

        } catch (Exception $ex){

            DB::rollBack();

            $message = "Erro desconhecido, por gentileza, entre em contato com o administrador. " . $ex->getMessage();
        }

        if ($message && $message !='') {
            $request->session()->flash('messa   ge.level', 'danger');
            $request->session()->flash('message.content', $message);
        } else {
            $request->session()->flash('message.level', 'success');
            $request->session()->flash('message.content', 'Seus dados foram atualizados com sucesso');
        }

        return redirect()->route('painel');
    }

}
