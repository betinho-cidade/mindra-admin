<?php

namespace App\Http\Controllers\Painel\Relatorio\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\EmpresaFuncionario;

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
        if(Gate::denies('view_painel')){
            abort('403', 'Página não disponível');
            //return redirect()->back();
        }

        $user = Auth()->User();

        $roles = $user->roles;

        $role = $roles->first()->name;

        $empresa_funcionarios = [];
        if($role == 'Funcionario') {
            $empresa_funcionarios = EmpresaFuncionario::join('funcionarios', 'empresa_funcionarios.funcionario_id', '=', 'funcionarios.id')
                                                    ->join('users', 'funcionarios.user_id', '=', 'users.id')
                                                    ->where('users.id', $user->id)
                                                    ->select('empresa_funcionarios.*')
                                                    ->get();
        }

        return view('painel.relatorio.dashboard.index', compact('user','empresa_funcionarios'));
    }

}
