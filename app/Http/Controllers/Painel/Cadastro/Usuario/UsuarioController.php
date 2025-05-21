<?php

namespace App\Http\Controllers\Painel\Cadastro\Usuario;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Funcionario;
use App\Models\EmpresaFuncionario;
use App\Models\Consultor;
use App\Models\ConsultorEmpresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Cadastro\Usuario\CreateRequest;
use App\Http\Requests\Cadastro\Usuario\UpdateRequest;
use App\Http\Requests\Cadastro\Usuario\SearchRequest;
use Carbon\Carbon;



class UsuarioController extends Controller
{

    public function __construct(Request $request)
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if(Gate::denies('view_usuario')){
            abort('403', 'Página não disponível');
            //return redirect()->back();
        }

        $user = Auth()->User();

        $usuarios = null;
        $excel_params = [];


        return view('painel.cadastro.usuario.index', compact('user', 'usuarios', 'excel_params'));
    }

    public function search(SearchRequest $request)
    {
        if(Gate::denies('view_usuario')){
            abort('403', 'Página não disponível');
            //return redirect()->back();
        }

        $user = Auth()->User();

        if($request->excel_params) {
            if($request->has('excel_params_set') && ($request->has('excel_params_set') == 'update'  || $request->has('excel_params_set') == 'delete')){
                $excel_params_set = json_decode($request->excel_params);

                $excel_params = [
                    'nome' => isset($excel_params_set->nome) ? $excel_params_set->nome : '',
                    'cpf' => isset($excel_params_set->cpf) ?$excel_params_set->cpf : '',
                    'email' => isset($excel_params_set->email) ? $excel_params_set->email : '',
                    'situacao' => isset($excel_params_set->situacao) ? $excel_params_set->situacao : '',
                ];
            } else{
                $excel_params = [
                    'nome' => isset($request->excel_params['nome']) ? $request->excel_params['nome'] : '',
                    'cpf' => isset($request->excel_params['cpf']) ? $request->excel_params['cpf'] : '',
                    'email' => isset($request->excel_params['email']) ? $request->excel_params['email'] : '',
                    'situacao' => isset($request->excel_params['situacao']) ? $request->excel_params['situacao'] : '',
                ];
            }
        } else {
            $excel_params = [
                'nome' => isset($request->nome) ? $request->nome : '',
                'cpf' => isset($request->cpf) ? $request->cpf : '',
                'email' => isset($request->email) ? $request->email : '',
                'situacao' => isset($request->situacao) ? $request->situacao : '',
            ];
        }

        $excel_params_translate = [
            'nome' => 'Nome',
            'cpf' => 'CPF',
            'email' => 'e-mail',
            'situacao' => 'Situação',
        ];


        $usuarios = User::join('role_user', 'role_user.user_id', 'users.id')
                    ->where(function($query) use ($excel_params){
                        if($excel_params['situacao']){
                            if ($excel_params['situacao'] == 'A') {
                                $query->where('role_user.status', 'A');
                            } elseif ($excel_params['situacao'] == 'I') {
                                $query->where('role_user.status', 'I');
                            }
                        }
                        if ($excel_params['nome']) {
                            $query->where('nome', 'like', '%' . $excel_params['nome'] . '%');
                        }
                        if ($excel_params['cpf']) {
                            $query->where('cpf', 'like', '%' . $excel_params['cpf'] . '%');
                        }
                        if ($excel_params['email']) {
                            $query->where('email', 'like', '%' . $excel_params['email'] . '%');
                        }
                    })
                    ->select('users.*')
                    ->paginate(300);

        return view('painel.cadastro.usuario.index', compact('user', 'usuarios', 'excel_params', 'excel_params_translate'));
    }

    public function create()
    {
        if(Gate::denies('create_usuario')){
            abort('403', 'Página não disponível');
        }

        $user = Auth()->User();

        $perfis = Role::all();

        return view('painel.cadastro.usuario.create', compact('user', 'perfis'));
    }

    public function store(CreateRequest $request)
    {
        if(Gate::denies('create_usuario')){
            abort('403', 'Página não disponível');
        }

        $user = Auth()->User();

        $message = '';

        try {

            DB::beginTransaction();

            $usuario = new User();

            $usuario->nome = $request->nome;
            $usuario->email = $request->email;
            $usuario->password = bcrypt($request->password);
            $usuario->cpf = $request->cpf;
            $usuario->rg = $request->rg;
            $usuario->data_nascimento = $request->data_nascimento;
            $usuario->telefone = $request->telefone;
            $usuario->sexo = $request->sexo;
            $usuario->end_cep = $request->end_cep;
            $usuario->end_cidade = $request->end_cidade;
            $usuario->end_uf = $request->end_uf;
            $usuario->end_logradouro = $request->end_logradouro;
            $usuario->end_numero = $request->end_numero;
            $usuario->end_bairro = $request->end_bairro;
            $usuario->end_complemento = $request->end_complemento;

            $usuario->save();

            $usuario->rolesAll()->attach($request->perfil);

            $status = $usuario->rolesAll()
                              ->withPivot(['status'])
                              ->first()
                              ->pivot;

            $status['status'] = $request->situacao;
            $status->save();

            $roles = $usuario->roles;

            if($roles->contains('name', 'Funcionario')){
                $funcionario = new Funcionario();
                $funcionario->user_id = $usuario->id;
                $funcionario->save();
            } else if($roles->contains('name', 'Consultor')){
                $consultor = new Consultor();
                $consultor->user_id = $usuario->id;
                $consultor->save();
            }

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
            $request->session()->flash('message.content', 'O Usuário <code class="highlighter-rouge">'. $request->nome .'</code> foi criado com sucesso');
        }

        return redirect()->route('usuario.index');
    }

    public function show(User $usuario, Request $request)
    {

        if(Gate::denies('edit_usuario')){
            abort('403', 'Página não disponível');
            //return redirect()->back();
        }

        $user = Auth()->User();

        $perfis = Role::all();

        $excel_params = ($request->has('excel_params')) ? $request->excel_params : [];

        return view('painel.cadastro.usuario.show', compact('user', 'usuario', 'perfis', 'excel_params'));
    }

    public function update(UpdateRequest $request, User $usuario)
    {
        if(Gate::denies('edit_usuario')){
            abort('403', 'Página não disponível');
        }

        $user = Auth()->User();
        $roles = $usuario->roles;

        $message = '';

        try {

            DB::beginTransaction();

            $usuario->nome = $request->nome;
            $usuario->email = $request->email;
            $usuario->cpf = $request->cpf;
            $usuario->data_nascimento = $request->data_nascimento;
            $usuario->telefone = $request->telefone;
            $usuario->rg = $request->rg;
            $usuario->sexo = $request->sexo;
            $usuario->end_cep = $request->end_cep;
            $usuario->end_cidade = $request->end_cidade;
            $usuario->end_uf = $request->end_uf;
            $usuario->end_logradouro = $request->end_logradouro;
            $usuario->end_numero = $request->end_numero;
            $usuario->end_bairro = $request->end_bairro;
            $usuario->end_complemento = $request->end_complemento;

            if($request->password){
                $usuario->password = bcrypt($request->password);
            }

            $usuario->save();

            $status = $usuario->situacao;

            if($request->situacao && ($request->situacao != $status['status']) && ($usuario->id != $user->id)){
                $status['status'] = $request->situacao;
                $status->save();

                if($request->situacao == 'I'){

                    if($roles->contains('name', 'Funcionario')){
                        EmpresaFuncionario::where('funcionario_id', $usuario->funcionario->id)
                                            ->update(['status' => 'I']);
                    } else if($roles->contains('name', 'Consultor')){
                        ConsultorEmpresa::where('consultor_id', $usuario->consultor->id)
                                            ->update(['status' => 'I']);
                    }
                }
            }

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
            $request->session()->flash('message.content', 'O Usuário <code class="highlighter-rouge">'. $usuario->nome .'</code> foi alterado com sucesso');
        }

        if($request->has('excel_params')){
            $excel_params = $request->excel_params;
            $excel_params_set = ($request->has('excel_params_set')) ? $request->excel_params_set : '';
            return redirect()->route('usuario.search', compact('excel_params', 'excel_params_set'));
        } else{
            return redirect()->route('usuario.index');
        }

    }

    public function destroy(User $usuario, Request $request)
    {
        if(Gate::denies('delete_usuario')){
            abort('403', 'Página não disponível');
        }

        $user = Auth()->User();

        $roles = $usuario->roles;

        $message = '';
        $usuario_nome = $usuario->nome;

        if(($usuario->id != $user->id)) {
            try {
                DB::beginTransaction();

                if($roles->contains('name', 'Funcionario')){
                    DB::table('empresa_funcionarios')->where('funcionario_id', '=', $usuario->funcionario->id)->delete();
                    DB::table('funcionarios')->where('user_id', '=', $usuario->id)->delete();
                } else if($roles->contains('name', 'Consultor')){
                    DB::table('consultor_empresas')->where('consultor_id', '=', $usuario->consultor->id)->delete();
                    DB::table('consultors')->where('user_id', '=', $usuario->id)->delete();
                }

                DB::table('role_user')->where('user_id', '=', $usuario->id)->delete();

                $usuario->delete();

                DB::commit();

            } catch (Exception $ex){

                DB::rollBack();

                if(strpos($ex->getMessage(), 'Integrity constraint violation') !== false){
                    $message = "Não foi possível excluir o registro, pois existem referências ao mesmo em outros processos.";
                } else{
                    $message = "Erro desconhecido, por gentileza, entre em contato com o administrador. ".$ex->getMessage();
                }

            }
        } else {
            $message = "Não é possível excluir o usuário logado.";
        }


        if ($message && $message !='') {
            $request->session()->flash('message.level', 'danger');
            $request->session()->flash('message.content', $message);
        } else {
            $request->session()->flash('message.level', 'success');
            $request->session()->flash('message.content', 'O Usuário <code class="highlighter-rouge">'. $usuario_nome .'</code> foi excluído com sucesso');
        }

        if($request->has('excel_params')){
            $excel_params = $request->excel_params;
            $excel_params_set = ($request->has('excel_params_set')) ? $request->excel_params_set : '';
            return redirect()->route('usuario.search', compact('excel_params', 'excel_params_set'));
        } else{
            return redirect()->route('usuario.index');
        }
    }

}
