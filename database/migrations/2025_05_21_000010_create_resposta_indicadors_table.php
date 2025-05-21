<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRespostaIndicadorsTable extends Migration
{

    public function up()
    {
        Schema::create('resposta_indicadors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('resposta_id');
            $table->string('titulo', 255);
            $table->integer('indicador');
            $table->integer('ordem')->default(1);
            $table->timestamps();
            $table->unique(['resposta_id', 'indicador'], 'resposta_indicador_uk');
            $table->foreign('resposta_id')->references('id')->on('respostas');
        });
    }

    public function down()
    {
        Schema::dropIfExists('resposta_indicadors');
    }
}
