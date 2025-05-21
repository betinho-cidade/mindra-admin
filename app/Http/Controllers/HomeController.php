<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;


class HomeController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index()
    {
        $user = Auth()->User();

        if(Gate::denies('view_painel')){
            $this->logout();
        }

        return redirect()->route('painel');

    }


    public function logout()
    {
        Auth()->logout();

        return redirect('/login');
    }

}
