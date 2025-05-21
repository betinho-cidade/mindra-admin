<?php

namespace App\Http\Controllers\Painel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\DB;
use Exception;

class PainelController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {

        if(Gate::denies('view_painel')){
            return redirect()->route('logout');
        }

        $user = Auth()->User();

        $roles = $user->roles;

        $role = $roles->first()->name;

        if($role == 'Gestor' || $role == 'Consultor') {
            return redirect()->route('dashboard.index');
        }else{
            return redirect()->route('logout');
        }

    }

    public function js_viacep(Request $request)
    {

        $cep = Str::of($request->cep)->replaceMatches('/[^z0-9]++/', '')->__toString();

        $url = 'https://viacep.com.br/ws/'. $cep .'/json/';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 3);

        $result = curl_exec($ch);

        curl_close($ch);

        $mensagem = json_decode($result,true);

        echo json_encode($mensagem);
    }

    public function js_menu_aberto(Request $request)
    {

        if(Gate::denies('view_painel')){
            return redirect()->route('logout');
        }

        $user = Auth()->User();

        try {

            DB::beginTransaction();

            $user->menu_aberto = ($user->menu_aberto == 'N') ? 'S' : 'N';

            $user->save();

            DB::commit();

        } catch (Exception $ex){

            DB::rollBack();
        }

        echo json_encode('');
    }

    public function js_cnpj(Request $request)
    {

        if(Gate::denies('view_painel')){
            return redirect()->route('logout');
        }

        $cnpj = Str::of($request->cnpj)->replaceMatches('/[^z0-9]++/', '')->__toString();

        $url = 'https://receitaws.com.br/v1/cnpj/'. $cnpj;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 3);

        $result = curl_exec($ch);

        curl_close($ch);

        $mensagem = json_decode($result,true);

        echo json_encode($mensagem);
    }


}
