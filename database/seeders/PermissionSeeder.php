<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{

    public function run()
    {
        if(DB::table('permissions')->get()->count() == 0){

            DB::table('permissions')->insert([
                [
                    'id' => 1,
                    'name' => 'view_usuario',
                    'description' => 'Visualizar as informações do usuário do sistema',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'id' => 2,
                    'name' => 'edit_usuario',
                    'description' => 'Editar as informações do usuário do sistema',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'id' => 3,
                    'name' => 'create_usuario',
                    'description' => 'Criar um novo usuário do sistema',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'id' => 4,
                    'name' => 'delete_usuario',
                    'description' => 'Excluir o usuário do sistema',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'id' => 5,
                    'name' => 'view_painel',
                    'description' => 'Visualizar as informações do Painel',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'id' => 6,
                    'name' => 'view_usuario_logado',
                    'description' => 'Acessar a informação do usuário logado do sistema ',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'id' => 7,
                    'name' => 'edit_usuario_logado',
                    'description' => 'Editar a informação do usuário logado do sistema ',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'id' => 8,
                    'name' => 'view_empresa',
                    'description' => 'Visualizar as informações da empresa',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'id' => 9,
                    'name' => 'edit_empresa',
                    'description' => 'Editar as informações da empresa',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'id' => 10,
                    'name' => 'create_empresa',
                    'description' => 'Criar uma nova empresa',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'id' => 11,
                    'name' => 'delete_empresa',
                    'description' => 'Excluir a empresa',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'id' => 12,
                    'name' => 'view_empresa_consultor',
                    'description' => 'Visualizar as empresas vinculadas ao consultor',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'id' => 13,
                    'name' => 'create_empresa_consultor',
                    'description' => 'Criar um novo vínculo entre a empresa e o consultor',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'id' => 14,
                    'name' => 'delete_empresa_consultor',
                    'description' => 'Excluir o vínculo da empresa e o consultor',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'id' => 15,
                    'name' => 'view_empresa_funcionario',
                    'description' => 'Visualizar as informações da empresa x funcionários',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'id' => 16,
                    'name' => 'create_empresa_funcionario',
                    'description' => 'Criar um novo funcionário da empresa',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'id' => 17,
                    'name' => 'edit_empresa_funcionario',
                    'description' => 'Editar um funcionário da empresa',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'id' => 18,
                    'name' => 'delete_empresa_funcionario',
                    'description' => 'Excluir um funcionário da empresa',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'id' => 19,
                    'name' => 'import_empresa_funcionario',
                    'description' => 'Importar uma lista de funcionários da empresa',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'id' => 20,
                    'name' => 'invite_empresa_funcionario',
                    'description' => 'Enviar e-mail em lote para ativação do funcionário',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'id' => 21,
                    'name' => 'view_campanha',
                    'description' => 'Visualizar as informações da campanha',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'id' => 22,
                    'name' => 'create_campanha',
                    'description' => 'Criar uma nova campanha',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'id' => 23,
                    'name' => 'edit_campanha',
                    'description' => 'Editar uma campanha',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'id' => 24,
                    'name' => 'delete_campanha',
                    'description' => 'Excluir uma campanha',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'id' => 25,
                    'name' => 'release_campanha_funcionario',
                    'description' => 'Liberar funcionários para realizar a avaliação da campanha e envia notificação por email',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'id' => 26,
                    'name' => 'view_preview_formulario',
                    'description' => 'Visualizar o preview do Formulário',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'id' => 27,
                    'name' => 'view_avaliacao',
                    'description' => 'Visualizar as avaliações',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'id' => 28,
                    'name' => 'populate_avaliacao',
                    'description' => 'Realizar a avaliação',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'id' => 29,
                    'name' => 'analisa_campanha_funcionario',
                    'description' => 'Realiza a análise dos formulários respondidos na campanha',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'id' => 30,
                    'name' => 'view_dashboard',
                    'description' => 'Visualiza os relatórios em tela',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
            ]);

        } else { echo "\e[31mTabela Permissions não está vazia. "; }

    }

}
