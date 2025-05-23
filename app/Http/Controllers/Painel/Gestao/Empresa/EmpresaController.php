<?php

namespace App\Http\Controllers\Painel\Gestao\Empresa;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Empresa;
use App\Models\EmpresaFuncionario;
use App\Models\ConsultorEmpresa;
use App\Models\Funcionario;
use App\Models\CampanhaEmpresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Gestao\Empresa\CreateRequest;
use App\Http\Requests\Gestao\Empresa\UpdateRequest;
use Carbon\Carbon;
use App\Http\Requests\Gestao\Empresa\ImportFuncionarioRequest;
use App\Services\Import\FuncionarioImportService;
use App\Services\Mail\FuncionarioInviteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Colors\Rgb\Channels\Red;

class EmpresaController extends Controller
{

    public function __construct(Request $request)
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if(Gate::denies('view_empresa_funcionario')){
            abort('403', 'Página não disponível');
            //return redirect()->back();
        }

        $user = Auth()->User();

        $roles = $user->roles;

        $empresas = [];
        if ($roles->contains('name', 'Gestor')) {
            $empresas = Empresa::where('status','A')
                                ->orderBy('nome')->get();
        }
        else if ($roles->contains('name', 'Consultor')) {
            $empresas = Empresa::where('empresas.status','A')
                                ->join('consultor_empresas', 'consultor_empresas.empresa_id', '=', 'empresas.id')
                                ->where('consultor_empresas.consultor_id', $user->consultor->id)
                                ->where('consultor_empresas.status','A')
                                ->orderBy('empresas.nome')->get();
        } else{
            abort('403', 'Página não disponível');
        }

        return view('painel.gestao.empresa.index', compact('user', 'empresas'));
    }

    public function show(Empresa $empresa, Request $request)
    {

        if(Gate::denies('view_empresa_funcionario')){
            abort('403', 'Página não disponível');
            //return redirect()->back();
        }

        $user = Auth()->User();

        $roles = $user->roles;

        if(!$this->valida_consultor($empresa)){
            abort('403', 'Página não disponível');
        }

        $empresa_funcionarios = [];
        if ($roles->contains('name', 'Gestor')) {
            $empresa_funcionarios = EmpresaFuncionario::where('empresa_id', $empresa->id)->get();
        }
        else if ($roles->contains('name', 'Consultor')) {
            $empresa_funcionarios = EmpresaFuncionario::join('empresas', 'empresa_funcionarios.empresa_id', '=', 'empresas.id')
                                                    ->join('consultor_empresas', 'consultor_empresas.empresa_id', '=', 'empresas.id')
                                                    ->join('funcionarios', 'empresa_funcionarios.funcionario_id', '=', 'funcionarios.id')
                                                    ->join('users', 'funcionarios.user_id', '=', 'users.id')
                                                    ->where('empresas.id', $empresa->id)
                                                    ->where('consultor_empresas.consultor_id', $user->consultor->id)
                                                    ->where('consultor_empresas.status','A')
                                                    ->where('empresas.status','A')
                                                    ->select('empresa_funcionarios.*')
                                                    ->orderBy('users.nome')
                                                    ->get();
        } else{
            abort('403', 'Página não disponível');
        }

        $campanha_empresas = [];
        if ($roles->contains('name', 'Gestor')) {
            $campanha_empresas = CampanhaEmpresa::where('empresa_id', $empresa->id)->get();
        }
        else if ($roles->contains('name', 'Consultor')) {
            $campanha_empresas = CampanhaEmpresa::join('empresas', 'campanha_empresas.empresa_id', '=', 'empresas.id')
                                              ->where('empresas.id', $empresa->id)
                                              ->join('consultor_empresas', 'consultor_empresas.empresa_id', '=', 'empresas.id')
                                              ->where('consultor_empresas.consultor_id', $user->consultor->id)
                                              ->where('consultor_empresas.status','A')
                                              ->orderBy('empresas.nome')
                                              ->select('campanha_empresas.*')
                                              ->get();
        } else{
            abort('403', 'Página não disponível');
        }

        $resultado_import = ($request->has('resultado_import')) ? $request->resultado_import : [];
        $resultado_invite = ($request->has('resultado_invite')) ? $request->resultado_invite : [];
        $aba = ($request->has('aba') ? $request->aba : '');

        return view('painel.gestao.empresa.show', compact('user', 'empresa','empresa_funcionarios', 'campanha_empresas', 'resultado_import', 'resultado_invite', 'aba'));
    }

    public function create(Empresa $empresa)
    {
        if(Gate::denies('create_empresa_funcionario')){
            abort('403', 'Página não disponível');
        }

        $user = Auth()->User();

        if(!$this->valida_consultor($empresa)){
            abort('403', 'Página não disponível');
        }

        return view('painel.gestao.empresa.create', compact('user', 'empresa'));
    }

    public function store(Empresa $empresa, CreateRequest $request)
    {
        if(Gate::denies('create_empresa_funcionario')){
            abort('403', 'Página não disponível');
        }

        $user = Auth()->User();

        if(!$this->valida_consultor($empresa)){
            abort('403', 'Página não disponível');
        }

        $message = '';

        try {

            DB::beginTransaction();

            $usuario = new User();
            $usuario->nome = $request->nome;
            $usuario->email = $request->email;
            $usuario->password = bcrypt(Str::random(8));
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

            $usuario->rolesAll()->attach(Role::where('name', 'Funcionario')->first()->id);
            $status = $usuario->rolesAll()
                              ->withPivot(['status'])
                              ->first()
                              ->pivot;
            $status['status'] = 'I';
            $status->save();

            $funcionario = new Funcionario();
            $funcionario->user_id = $usuario->id;
            $funcionario->save();

            $empresa_funcionario = new EmpresaFuncionario();
            $empresa_funcionario->empresa_id = $empresa->id;
            $empresa_funcionario->funcionario_id = $funcionario->id;
            $empresa_funcionario->matricula = $request->matricula;
            $empresa_funcionario->cargo = $request->cargo;
            $empresa_funcionario->departamento = $request->departamento;
            $empresa_funcionario->data_admissao = $request->data_admissao;
            $empresa_funcionario->status = 'I';
            $empresa_funcionario->empresa_funcionario_created = $user->id;
            $empresa_funcionario->empresa_funcionario_updated = $user->id;
            $empresa_funcionario->save();

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
            $request->session()->flash('message.content', 'O Funcionário <code class="highlighter-rouge">'. $request->nome .'</code> foi criado com sucesso');
        }

        $aba = '';
        return redirect()->route('empresa_funcionario.show', compact('empresa', 'aba'));
    }

    public function show_funcionario(EmpresaFuncionario $empresa_funcionario, Request $request)
    {

        if(Gate::denies('edit_empresa_funcionario')){
            abort('403', 'Página não disponível');
            //return redirect()->back();
        }

        $user = Auth()->User();

        $roles = $user->roles;

        if(!$this->valida_consultor($empresa_funcionario->empresa)){
            abort('403', 'Página não disponível');
        }

        return view('painel.gestao.empresa.show_funcionario', compact('user', 'empresa_funcionario'));
    }

    public function update_funcionario(EmpresaFuncionario $empresa_funcionario, UpdateRequest $request)
    {
        if(Gate::denies('edit_empresa_funcionario')){
            abort('403', 'Página não disponível');
        }

        $user = Auth()->User();

        if(!$this->valida_consultor($empresa_funcionario->empresa)){
            abort('403', 'Página não disponível');
        }

        $message = '';
        $empresa = $empresa_funcionario->empresa;

        try {

            DB::beginTransaction();

            $empresa_funcionario->funcionario->user->nome = $request->nome;
            $empresa_funcionario->funcionario->user->email = $request->email;
            $empresa_funcionario->funcionario->user->cpf = $request->cpf;
            $empresa_funcionario->funcionario->user->rg = $request->rg;
            $empresa_funcionario->funcionario->user->data_nascimento = $request->data_nascimento;
            $empresa_funcionario->funcionario->user->telefone = $request->telefone;
            $empresa_funcionario->funcionario->user->sexo = $request->sexo;
            $empresa_funcionario->funcionario->user->end_cep = $request->end_cep;
            $empresa_funcionario->funcionario->user->end_cidade = $request->end_cidade;
            $empresa_funcionario->funcionario->user->end_uf = $request->end_uf;
            $empresa_funcionario->funcionario->user->end_logradouro = $request->end_logradouro;
            $empresa_funcionario->funcionario->user->end_numero = $request->end_numero;
            $empresa_funcionario->funcionario->user->end_bairro = $request->end_bairro;
            $empresa_funcionario->funcionario->user->end_complemento = $request->end_complemento;
            $empresa_funcionario->funcionario->user->save();

            $empresa_funcionario->matricula = $request->matricula;
            $empresa_funcionario->cargo = $request->cargo;
            $empresa_funcionario->departamento = $request->departamento;
            $empresa_funcionario->data_admissao = $request->data_admissao;
            $empresa_funcionario->status = $request->situacao;
            $empresa_funcionario->empresa_funcionario_updated = $user->id;
            $empresa_funcionario->save();

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
            $request->session()->flash('message.content', 'O Funcionário <code class="highlighter-rouge">'. $empresa_funcionario->funcionario->user->nome .'</code> foi alterado com sucesso');
        }

        $aba = '';
        return redirect()->route('empresa_funcionario.show', compact('empresa', 'aba'));
    }

    public function destroy_funcionario(EmpresaFuncionario $empresa_funcionario, Request $request)
    {
        if(Gate::denies('delete_empresa_funcionario')){
            abort('403', 'Página não disponível');
        }

        $user = Auth()->User();

        if(!$this->valida_consultor($empresa_funcionario->empresa)){
            abort('403', 'Página não disponível');
        }

        $message = '';
        $empresa = $empresa_funcionario->empresa;
        $funcionario_nome = $empresa_funcionario->funcionario->user->nome;

        try {
            DB::beginTransaction();

            $funcionario = $empresa_funcionario->funcionario->user->id;

            $empresa_funcionario->delete();

            DB::table('funcionarios')->where('user_id', '=', $funcionario)->delete();

            DB::table('role_user')->where('user_id', '=', $funcionario)->delete();

            DB::table('users')->where('id', '=', $funcionario)->delete();

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
            $request->session()->flash('message.content', 'O Funcionário <code class="highlighter-rouge">'. $funcionario_nome .'</code> foi excluído da empresa com sucesso e da sua conta de usuário');
        }

        $aba = '';
        return redirect()->route('empresa_funcionario.show', compact('empresa', 'aba'));

    }
    //public function import(Empresa $empresa, ImportFuncionarioRequest $request): JsonResponse
    public function import(Empresa $empresa, ImportFuncionarioRequest $request)
    {

        if(Gate::denies('import_empresa_funcionario')){
            abort('403', 'Página não disponível');
        }

        $user = Auth()->User();

        if(!$this->valida_consultor($empresa)){
            abort('403', 'Página não disponível');
        }

        $resultado_import = [];

        try {
            $filePath = $request->file('file')->getPathname();
            $importService = new FuncionarioImportService($empresa, $user);
            $results = $importService->import($filePath);

            $resultado_import = [
                'success_count' => count($results['success']),
                'errors_count' => count($results['failed']),
                'log_file' => ($results['log_file']) ? $importService->getLogImportDownloadLink($results['log_file']) : ''
            ];


        } catch (\Exception $e) {
            if($e->getMessage()){
                $request->session()->flash('message.level', 'danger');
                $request->session()->flash('message.content', $e->getMessage());
            }

            $resultado_import = [
                'success_count' => 0,
                'errors_count' => 0,
                'log_file' => ''
            ];
        }

        $aba = 'Imports';
        $resultado_invite = [];
        return redirect()->route('empresa_funcionario.show', compact('empresa', 'resultado_import', 'resultado_invite', 'aba'));
    }

    public function invite(Empresa $empresa, Request $request)
    {

        if(Gate::denies('invite_empresa_funcionario')){
            abort('403', 'Página não disponível');
        }

        $user = Auth()->User();

        if(!$this->valida_consultor($empresa)){
            abort('403', 'Página não disponível');
        }

        $resultado_invite = [];

        try {
            $service = new FuncionarioInviteService($empresa);
            $results = $service->sendInvites();

            $resultado_invite = [
                'success_count' => count($results['success']),
                'errors_count' => count($results['failed']),
                'log_file' => ($results['log_file']) ? $service->getLogInviteDownloadLink($results['log_file']) : ''
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

        $aba = '';
        $resultado_import = [];
        return redirect()->route('empresa_funcionario.show', compact('empresa', 'resultado_import', 'resultado_invite', 'aba'));
    }

    public function logImport(Empresa $empresa, Request $request)
    {
        if(Gate::denies('import_empresa_funcionario')){
            abort('403', 'Página não disponível');
        }

        $user = Auth()->User();

        if(!$this->valida_consultor($empresa)){
            abort('403', 'Página não disponível');
        }

        $filename = $request->filename;

        $filePath = 'logs/import/' . $empresa->id . '/' . $filename;

        if (!Storage::exists($filePath)) {
            abort(404, 'Arquivo de log não encontrado.');
        }

        return Storage::download($filePath, $filename, [
            'Content-Type' => 'text/plain',
        ]);
    }

    public function logInvite(Empresa $empresa, Request $request)
    {
        if(Gate::denies('invite_empresa_funcionario')){
            abort('403', 'Página não disponível');
        }

        $user = Auth()->User();

        if(!$this->valida_consultor($empresa)){
            abort('403', 'Página não disponível');
        }

        $filename = $request->filename;

        $filePath = 'logs/invite/' . $empresa->id . '/' . $filename;

        if (!Storage::exists($filePath)) {
            abort(404, 'Arquivo de log não encontrado.');
        }

        return Storage::download($filePath, $filename, [
            'Content-Type' => 'text/plain',
        ]);
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
