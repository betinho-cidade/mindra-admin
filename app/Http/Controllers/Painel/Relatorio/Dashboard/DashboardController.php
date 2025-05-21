<?php

namespace App\Http\Controllers\Painel\Relatorio\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AulaAvaliacao;
use App\Models\MepaTransacao;
use App\Models\MepaSituacao;
use App\Models\User;
use App\Models\Categoria;
use App\Models\Curso;
use App\Models\CursoRealizado;
use App\Models\CursoRealizadoMedalha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Excel;
use App\Exports\RelatorioPagamentoExport;
use App\Exports\RelatorioAlunoExport;
use App\Exports\RelatorioAlunoAndamentoExport;
use App\Exports\RelatorioVendaExport;


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

        return view('painel.relatorio.dashboard.index', compact('user'));
    }

}
