<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampanhaRespostasTable extends Migration
{

    public function up()
    {
        Schema::create('campanha_respostas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('campanha_funcionario_id');
            $table->unsignedBigInteger('formulario_pergunta_id');
            $table->unsignedBigInteger('resposta_indicador_id');
            $table->timestamps();
            $table->unique(['campanha_funcionario_id', 'formulario_pergunta_id'], 'campanha_resposta_uk');
            $table->foreign('campanha_funcionario_id')->references('id')->on('campanha_funcionarios');
            $table->foreign('formulario_pergunta_id')->references('id')->on('formulario_perguntas');
            $table->foreign('resposta_indicador_id')->references('id')->on('resposta_indicadors');
        });
    }


    public function down()
    {
        Schema::dropIfExists('campanha_respostas');
    }
}
