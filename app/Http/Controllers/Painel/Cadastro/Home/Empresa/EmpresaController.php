<?php

namespace App\Http\Controllers\Painel\Cadastro\Home\Empresa;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Empresa;
use App\Models\Consultor;
use App\Models\ConsultorEmpresa;
use App\Models\EmpresaFuncionario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Cadastro\Home\Empresa\CreateRequest;
use App\Http\Requests\Cadastro\Home\Empresa\UpdateRequest;
use App\Http\Requests\Cadastro\Home\Empresa\SearchRequest;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Carbon\Carbon;




class EmpresaController extends Controller
{

    public function __construct(Request $request)
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if(Gate::denies('view_empresa')){
            abort('403', 'Página não disponível');
            //return redirect()->back();
        }

        $user = Auth()->User();

        $empresas_AT = Empresa::where('status','A')->orderBy('nome')->get();
        $empresas_IN = Empresa::where('status','I')->orderBy('nome')->get();

        return view('painel.cadastro.home.empresa.index', compact('user', 'empresas_AT', 'empresas_IN'));
    }

    public function create()
    {
        if(Gate::denies('create_empresa')){
            abort('403', 'Página não disponível');
        }

        $user = Auth()->User();

        return view('painel.cadastro.home.empresa.create', compact('user'));
    }

    public function store(CreateRequest $request)
    {
        if(Gate::denies('create_empresa')){
            abort('403', 'Página não disponível');
        }

        $user = Auth()->User();

        $message = '';

        try {

            DB::beginTransaction();

            $empresa = new Empresa();

            $empresa->nome = $request->nome;
            $empresa->cnpj = $request->cnpj;
            $empresa->email = $request->email;
            $empresa->responsavel_nome = $request->responsavel_nome;
            $empresa->responsavel_telefone = $request->responsavel_telefone;
            $empresa->telefone = $request->telefone;
            $empresa->num_contrato = $request->num_contrato;
            $empresa->inscricao_estadual = $request->inscricao_estadual;
            $empresa->atividade_principal = $request->atividade_principal;
            $empresa->site = $request->site;
            $empresa->data_abertura = $request->data_abertura;
            $empresa->qtd_funcionario = $request->qtd_funcionario ?? 0;
            $empresa->end_cep = $request->end_cep;
            $empresa->end_cidade = $request->end_cidade;
            $empresa->end_uf = $request->end_uf;
            $empresa->end_logradouro = $request->end_logradouro;
            $empresa->end_numero = $request->end_numero;
            $empresa->end_bairro = $request->end_bairro;
            $empresa->end_complemento = $request->end_complemento;

            $empresa->empresa_created = $user->id;
            $empresa->empresa_updated = $user->id;
            $empresa->status = $request->situacao;

            $empresa->save();

            if ($request->path_imagem) {
                $img_empresa = 'empresa_' . time() . '.' . $request->path_imagem->extension();
                $path_imagem = 'images/empresa/' . $empresa->id;

                $empresa->path_imagem = $img_empresa;

                if (!\File::isDirectory(public_path('images/empresa'))) {
                    \File::makeDirectory('images/empresa');
                }

                if (!\File::isDirectory(public_path($path_imagem))) {
                    \File::makeDirectory($path_imagem);
                }

                $manager = new ImageManager(new Driver());

                $img = $manager->read($request->path_imagem);

                $img->save($path_imagem.'/'.$img_empresa, 50);

                $empresa->save();
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
            $request->session()->flash('message.content', 'A Empresa <code class="highlighter-rouge">'. $request->nome .'</code> foi criada com sucesso');
        }

        return redirect()->route('empresa.show', compact('empresa'));
    }

    public function show(Empresa $empresa, Request $request)
    {

        if(Gate::denies('edit_empresa')){
            abort('403', 'Página não disponível');
            //return redirect()->back();
        }

        $user = Auth()->User();

        $roles = $user->roles;

        $consultor_empresas = ConsultorEmpresa::where('empresa_id', $empresa->id)->get();

        $empresa_funcionarios = EmpresaFuncionario::where('empresa_id', $empresa->id)->get();

        return view('painel.cadastro.home.empresa.show', compact('user', 'empresa', 'consultor_empresas', 'empresa_funcionarios'));
    }

    public function update(UpdateRequest $request, Empresa $empresa)
    {
        if(Gate::denies('edit_empresa')){
            abort('403', 'Página não disponível');
        }

        $user = Auth()->User();

        $roles = $user->roles;

        $message = '';

        try {

            DB::beginTransaction();

            $empresa->nome = $request->nome;
            $empresa->cnpj = $request->cnpj;
            $empresa->email = $request->email;
            $empresa->responsavel_nome = $request->responsavel_nome;
            $empresa->responsavel_telefone = $request->responsavel_telefone;
            $empresa->telefone = $request->telefone;
            $empresa->num_contrato = $request->num_contrato;
            $empresa->inscricao_estadual = $request->inscricao_estadual;
            $empresa->atividade_principal = $request->atividade_principal;
            $empresa->site = $request->site;
            $empresa->data_abertura = $request->data_abertura;
            $empresa->qtd_funcionario = $request->qtd_funcionario ?? 0;
            $empresa->end_cep = $request->end_cep;
            $empresa->end_cidade = $request->end_cidade;
            $empresa->end_uf = $request->end_uf;
            $empresa->end_logradouro = $request->end_logradouro;
            $empresa->end_numero = $request->end_numero;
            $empresa->end_bairro = $request->end_bairro;
            $empresa->end_complemento = $request->end_complemento;

            $empresa->empresa_updated = $user->id;
            $empresa->status = $request->situacao;

            $empresa->save();

            if ($request->path_imagem) {
                $img_empresa = 'empresa_' . time() . '.' . $request->path_imagem->extension();
                $path_imagem = 'images/empresa/' . $empresa->id;

                $empresa_path_old = $empresa->path_imagem;
                $empresa->path_imagem = $img_empresa;

                $manager = new ImageManager(new Driver());

                $img = $manager->read($request->path_imagem);

                $img->save($path_imagem.'/'.$img_empresa, 50);

                if (\File::exists(public_path($path_imagem . '/' . $empresa_path_old))) {
                    \File::delete(public_path($path_imagem . '/' . $empresa_path_old));
                }

                $empresa->save();
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
            $request->session()->flash('message.content', 'A Empresa <code class="highlighter-rouge">'. $empresa->nome .'</code> foi alterada com sucesso');
        }

        return redirect()->route('empresa.show', compact('empresa'));
    }

    public function destroy(Empresa $empresa, Request $request)
    {
        if(Gate::denies('delete_empresa')){
            abort('403', 'Página não disponível');
        }

        $user = Auth()->User();

        $message = '';
        $empresa_nome = $empresa->nome;

        $path_imagem = 'images/emprsa/' . $empresa->id;
        $imagem = $empresa->path_imagem;

        try {
            DB::beginTransaction();

            $empresa->delete();

            if (\File::exists(public_path($path_imagem . '/' . $imagem))) {
                \File::delete(public_path($path_imagem . '/' . $imagem));
                \File::deleteDirectory($path_imagem);
            }

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
            $request->session()->flash('message.content', 'A Empresa <code class="highlighter-rouge">'. $empresa_nome .'</code> foi excluída com sucesso');
        }

        return redirect()->route('empresa.index');
    }

    public function consultor_create(Empresa $empresa)
    {

        if(Gate::denies('create_empresa_consultor')){
            abort('403', 'Página não disponível');
            //return redirect()->back();
        }

        $user = Auth()->User();


        $consultors = Consultor::join('users', 'consultors.user_id', '=', 'users.id')
                        ->join('role_user', 'role_user.user_id', '=', 'users.id')
                        ->where('role_user.status', 'A')
                        ->join('roles', 'role_user.role_id', '=', 'roles.id')
                        ->where('roles.name', 'Consultor')
                        ->whereNotExists(function($query) use ($empresa)
                        {
                            $query->select(DB::raw(1))
                                ->from('consultor_empresas')
                                ->whereRaw('consultor_empresas.consultor_id = consultors.id')
                                ->where('consultor_empresas.empresa_id', $empresa->id);
                        })
                        ->select('consultors.*')
                        ->get();

        return view('painel.cadastro.home.empresa.consultor_create', compact('user', 'empresa', 'consultors'));
    }

    public function consultor_store(Request $request, Empresa $empresa)
    {

        if(Gate::denies('create_empresa_consultor')){
            abort('403', 'Página não disponível');
            //return redirect()->back();
        }

        $user = Auth()->User();

        $message = '';

        try {
            DB::beginTransaction();

            foreach($request->consultors as $consultor)
            {

                $newConsultorEmpresa = new ConsultorEmpresa();
                $newConsultorEmpresa->empresa_id = $empresa->id;
                $newConsultorEmpresa->consultor_id = $consultor;
                $newConsultorEmpresa->save();
            }

            DB::commit();

        } catch (Exception $ex){

            DB::rollBack();
            if(strpos($ex->getMessage(), 'consultor_empresa_uk') !== false){
                $message = "Um dos consultores informados já está registrado nessa empresa.";

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
            $request->session()->flash('message.content', 'Os consultores foram vinculados com sucesso');
        }

        return redirect()->route('empresa.show', compact('empresa'));
    }

    public function consultor_destroy(Empresa $empresa, ConsultorEmpresa $consultor_empresa, Request $request)
    {
        if(Gate::denies('delete_empresa_consultor')){
            abort('403', 'Página não disponível');
        }

        $user = Auth()->User();

        $message = '';

        if(($empresa->id == $consultor_empresa->empresa->id)) {

            try {
                DB::beginTransaction();

                ConsultorEmpresa::where('id', $consultor_empresa->id)
                                ->where('empresa_id', $empresa->id)
                                ->delete();

                DB::commit();

            } catch (Exception $ex){

                DB::rollBack();
                $message = "Erro desconhecido, por gentileza, entre em contato com o administrador. ".$ex->getMessage();
            }

        } else {
            $message = "Não foi possível excluir o consultor da empresa - informações inconsistentes.";
        }

        if ($message && $message !='') {
            $request->session()->flash('message.level', 'danger');
            $request->session()->flash('message.content', $message);
        } else {
            $request->session()->flash('message.level', 'success');
            $request->session()->flash('message.content', 'O Consultor foi desvinculado da Empresa com sucesso');
        }

        return redirect()->route('empresa.show', compact('empresa'));
    }

    public function consultor_status(Empresa $empresa, ConsultorEmpresa $consultor_empresa, Request $request)
    {
        if(Gate::denies('create_empresa_consultor')){
            abort('403', 'Página não disponível');
        }

        $user = Auth()->User();

        $message = '';

        if(($empresa->id == $consultor_empresa->empresa->id)) {

            try {
                DB::beginTransaction();

                $consultor_empresa->status = $consultor_empresa->status == 'A' ? 'I' : 'A';

                $consultor_empresa->save();

                DB::commit();

            } catch (Exception $ex){

                DB::rollBack();
                $message = "Erro desconhecido, por gentileza, entre em contato com o administrador. ".$ex->getMessage();
            }

        } else {
            $message = "Não foi possível altearr o status do consultor da empresa - informações inconsistentes.";
        }

        if ($message && $message !='') {
            $request->session()->flash('message.level', 'danger');
            $request->session()->flash('message.content', $message);
        } else {
            $request->session()->flash('message.level', 'success');
            $request->session()->flash('message.content', 'O Consultor teve seu status alterado com sucesso');
        }

        return redirect()->route('empresa.show', compact('empresa'));
    }


}
