<?php

namespace App\Http\Controllers\Guest\ResetPassword;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendResetPasswordMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Exception;


class ForgotPasswordController extends Controller
{

    public function getEmail()
    {
       return view('guest.reset_password.forgotPassword');
    }


    public function postEmail(Request $request)
    {

        $request->validate([
            'email' => 'required|email|exists:users',
        ], [
            'email.required' => 'O e-mail é obrigatório',
            'email.email' => 'O e-mail informado é inválido',
            'email.exists' => 'O e-mail informado não existe em nossos cadastros',
        ]);

        $token = Str::random(64);

        try {

            DB::beginTransaction();

            DB::table('password_resets')->where(['email'=> $request->email])->delete();

            DB::table('password_resets')->insert(
                ['email' => $request->email, 'token' => $token, 'created_at' => Carbon::now()]
            );

            $message = '';

            $funcionario = User::where('email', $request->email)->first()->nome;

            try{
                Mail::to($request->email)->send(new SendResetPasswordMail($funcionario, $token));
            } catch(Exception $ex)
            {
                $message = $ex->getMessage();
            }

            DB::commit();

        } catch (Exception $ex2){

            DB::rollBack();

            $message = "Erro desconhecido, por gentileza, entre em contato com o administrador. " . $ex2->getMessage();
        }


        if ($message && $message !='') {
            $request->session()->flash('message.level', 'danger');
            $request->session()->flash('message.content', 'Não foi possível enviar o link. Por gentileza, tente novamente. ' . $message);
        } else {
            $request->session()->flash('message.level', 'success');
            $request->session()->flash('message.content', 'Foi enviado um e-mail com um link de alteração de senha para <code>'. $request->email .'</code>');
        }

        return back();
    }

}
