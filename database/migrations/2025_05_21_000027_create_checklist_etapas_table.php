<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChecklistEtapasTable extends Migration
{

    public function up()
    {
        Schema::create('checklist_etapas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('checklist_id');
            $table->string('titulo', 255)->nullable();
            $table->string('descricao', 255)->nullable();
            $table->integer('ordem')->default(1);
            $table->enum('visivel_formulario', ['S', 'N'])->default('S');  //S->Sim  N->Não
            $table->enum('visivel_report', ['S', 'N'])->default('S');  //S->Sim  N->Não
            $table->timestamps();
            $table->foreign('checklist_id')->references('id')->on('checklists');
        });
    }


    public function down()
    {
        Schema::dropIfExists('checklist_etapas');
    }
}
