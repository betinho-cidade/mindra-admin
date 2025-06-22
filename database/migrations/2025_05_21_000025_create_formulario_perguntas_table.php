<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormularioPerguntasTable extends Migration
{

    public function up()
    {
        Schema::create('formulario_perguntas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('formulario_etapa_id');
            $table->string('titulo', 255);
            $table->integer('ordem')->default(1);
            $table->float('ind_consequencia')->default(1);
            $table->timestamps();
            $table->foreign('formulario_etapa_id')->references('id')->on('formulario_etapas');
        });
    }


    public function down()
    {
        Schema::dropIfExists('formulario_perguntas');
    }
}
