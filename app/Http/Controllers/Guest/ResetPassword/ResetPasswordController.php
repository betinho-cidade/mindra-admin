<?php

namespace App\Http\Controllers\Guest\ResetPassword;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;


class ResetPasswordController extends Controller
{

    public function getPassword($token) {

        return view('guest.reset_password.reset', compact('token'));
     }


    //  public function createPassword($token) {

    //     return view('guest.reset_password.create', ['token' => $token]);
    //  }


     public function updatePassword(Request $request)
     {

        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users',
            'password' => 'required|string|min:8',
            'newPassword' => 'required|same:password',
        ], [
            'token.required' => 'O token gerado no passo anterior não está corretamente informado',
            'email.required' => 'O e-mail é obrigatório',
            'email.email' => 'O e-mail informado é inválido',
            'email.exists' => 'O e-mail informado não existe em nossos cadastros',
            'password.required' => 'A senha é requerida',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres',
            'newPassword.required' => 'A senha de confirmação é requerida',
            'newPassword.same' => 'A senha de confirmação deve ser a mesma informada na nova senha',
        ]);

        $message = '';

        try {

            DB::beginTransaction();

            $updatePassword = DB::table('password_resets')
                                 ->where(['email' => $request->email, 'token' => $request->token])
                                 ->first();

            if(!$updatePassword){
                $request->session()->flash('message.level', 'danger');
                $request->session()->flash('message.content', 'Não foi possível trocar a senha, informações inválidas.');

                DB::rollBack();
                return back();
            }


            $user = User::where('email', $request->email)
                         ->update(['password' => bcrypt($request->password)]);

            DB::table('password_resets')->where(['email'=> $request->email])->delete();

            DB::commit();

        } catch (Exception $ex2){

            DB::rollBack();

            $message = "Erro desconhecido, por gentileza, entre em contato com o administrador. " . $ex2->getMessage();
        }

        if ($message && $message !='') {
            $request->session()->flash('message.level', 'danger');
            $request->session()->flash('message.content', 'Não foi possível trocar a senha, informações inválidas. ' . $message);
        } else {
            $request->session()->flash('message.level', 'success');
            $request->session()->flash('message.content', 'Sua senha foi registrada com sucesso. <a href="https://mindra.com.br" style="text-decoration: underline!important;">Acesse o Portal Mindra e faça seu login</a>');
        }

        return back();
     }

}
