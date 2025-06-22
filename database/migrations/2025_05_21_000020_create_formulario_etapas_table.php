<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormularioEtapasTable extends Migration
{

    public function up()
    {
        Schema::create('formulario_etapas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('formulario_id');
            $table->string('titulo', 255)->nullable();
            $table->string('descricao', 255)->nullable();
            $table->integer('ordem')->default(1);
            $table->enum('visivel_formulario', ['S', 'N'])->default('S');  //S->Sim  N->Não
            $table->enum('visivel_report', ['S', 'N'])->default('S');  //S->Sim  N->Não
            $table->timestamps();
            $table->foreign('formulario_id')->references('id')->on('formularios');
        });
    }


    public function down()
    {
        Schema::dropIfExists('formulario_etapas');
    }
}
