<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormulariosTable extends Migration
{

    public function up()
    {
        Schema::create('formularios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('resposta_id');
            $table->string('titulo', 255);
            $table->longtext('descricao')->nullable();
            $table->enum('status', ['A', 'I'])->default('A');  //A->Ativo  I->Inativo
            $table->timestamps();
            $table->foreign('resposta_id')->references('id')->on('respostas');
        });
    }


    public function down()
    {
        Schema::dropIfExists('formularios');
    }
}
