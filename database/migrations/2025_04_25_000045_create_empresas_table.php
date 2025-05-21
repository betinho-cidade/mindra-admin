<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpresasTable extends Migration
{

    public function up()
    {
        Schema::create('empresas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nome', 255);
            $table->string('cnpj', 14)->unique('empresa_uk');
            $table->string('responsavel_nome', 255)->nullable();
            $table->string('responsavel_telefone', 20)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('telefone', 20)->nullable();
            $table->string('num_contrato', 20)->nullable();
            $table->string('inscricao_estadual', 50)->nullable();
            $table->string('atividade_principal', 300)->nullable();
            $table->string('site', 200)->nullable();
            $table->date('data_abertura')->nullable();
            $table->integer('qtd_funcionario')->default(0);
            $table->string('end_cep', 8)->nullable();
            $table->string('end_cidade', 60)->nullable();
            $table->string('end_uf', 2)->nullable();
            $table->string('end_logradouro', 80)->nullable();
            $table->string('end_numero', 20)->nullable();
            $table->string('end_bairro', 60)->nullable();
            $table->string('end_complemento', 100)->nullable();
            $table->enum('status', ['A', 'I'])->default('A');  //A->Ativo  I->Inativo
            $table->unsignedBigInteger('empresa_created');
            $table->unsignedBigInteger('empresa_updated');
            $table->timestamps();
            $table->foreign('empresa_created')->references('id')->on('users');
            $table->foreign('empresa_updated')->references('id')->on('users');
        });
    }


    public function down()
    {
        Schema::dropIfExists('empresas');
    }
}
