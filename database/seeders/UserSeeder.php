<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{

    public function run()
    {

        if(DB::table('users')->get()->count() == 0){

            DB::table('users')->insert([
                [
                    'id' => 1,
                    'nome' => 'Gestor',
                    'email' => 'gestor@mindra.com.br',
                    'cpf' => '111',
                    'data_nascimento' => '2010-01-01',
                    'telefone' => '43999999999',
                    'end_cep' => '86047560',
                    'end_cidade' => 'Londrina',
                    'end_uf' => 'PR',
                    'end_logradouro' => 'Rua Antero de Quental',
                    'end_numero' => '52',
                    'end_bairro' => 'Conjunto Residencial Vivendas do Arvoredo',
                    'end_complemento' => '',
                    'password' => bcrypt('12345678'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
            ]);

        } else { echo "\e[31mTabela Users não está vazia. "; }

    }
}

