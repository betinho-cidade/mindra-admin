<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampanhasTable extends Migration
{

    public function up()
    {
        Schema::create('campanhas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('formulario_id');
            $table->unsignedBigInteger('empresa_id');
            $table->string('titulo', 255);
            $table->longtext('descricao')->nullable();
            $table->datetime('data_inicio');
            $table->datetime('data_fim');
            $table->enum('status', ['A', 'I'])->default('A');  //A->Ativo  I->Inativo
            $table->unsignedBigInteger('campanha_created');
            $table->unsignedBigInteger('campanha_updated');
            $table->timestamps();
            $table->foreign('campanha_created')->references('id')->on('users');
            $table->foreign('campanha_updated')->references('id')->on('users');
            $table->foreign('formulario_id')->references('id')->on('formularios');
            $table->foreign('empresa_id')->references('id')->on('empresas');
        });
    }


    public function down()
    {
        Schema::dropIfExists('campanhas');
    }
}
