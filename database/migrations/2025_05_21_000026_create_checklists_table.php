<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChecklistsTable extends Migration
{

    public function up()
    {
        Schema::create('checklists', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('resposta_id');
            $table->string('titulo', 255);
            $table->longtext('descricao')->nullable();
            $table->enum('status', ['A', 'I'])->default('A');  //A->Ativo  I->Inativo
            $table->enum('visivel_formulario', ['S', 'N'])->default('S');  //A->Ativo  I->Inativo
            $table->enum('visivel_report', ['S', 'N'])->default('S');  //A->Ativo  I->Inativo
            $table->timestamps();
            $table->foreign('resposta_id')->references('id')->on('respostas');
            $table->index(['visivel_report', 'status'], 'idx_checklists_01');
        });
    }


    public function down()
    {
        Schema::dropIfExists('checklists');
    }
}
