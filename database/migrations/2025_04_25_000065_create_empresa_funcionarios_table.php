<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpresaFuncionariosTable extends Migration
{

    public function up()
    {
        Schema::create('empresa_funcionarios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id');
            $table->unsignedBigInteger('funcionario_id');
            $table->string('matricula', 20)->nullable();
            $table->string('cargo', 50);
            $table->string('departamento', 50)->nullable();
            $table->date('data_admissao')->nullable();
            $table->enum('status', ['A', 'I'])->default('I');  //A->Ativo  I->Inativo
            $table->unsignedBigInteger('empresa_funcionario_created');
            $table->unsignedBigInteger('empresa_funcionario_updated');
            $table->timestamps();
            $table->foreign('empresa_id')->references('id')->on('empresas');
            $table->foreign('funcionario_id')->references('id')->on('funcionarios');
            $table->unique(['empresa_id', 'funcionario_id'], 'empresa_funcionario_uk');
            $table->foreign('empresa_funcionario_created')->references('id')->on('users');
            $table->foreign('empresa_funcionario_updated')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('empresa_funcionarios');
    }
}
