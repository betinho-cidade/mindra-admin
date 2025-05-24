<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampanhaFuncionariosTable extends Migration
{

    public function up()
    {
        Schema::create('campanha_funcionarios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('campanha_empresa_id');
            $table->unsignedBigInteger('empresa_funcionario_id');
            $table->datetime('data_liberado');
            $table->datetime('data_iniciado')->nullable();
            $table->datetime('data_realizado')->nullable();
            $table->timestamps();
            $table->unique(['campanha_empresa_id', 'empresa_funcionario_id'], 'campanha_funcionario_uk');
            $table->foreign('campanha_empresa_id')->references('id')->on('campanha_empresas');
            $table->foreign('empresa_funcionario_id')->references('id')->on('empresa_funcionarios');
        });
    }


    public function down()
    {
        Schema::dropIfExists('campanha_funcionarios');
    }
}
