<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChecklistRespostasTable extends Migration
{

    public function up()
    {
        Schema::create('checklist_respostas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('checklist_consultor_id');
            $table->unsignedBigInteger('checklist_pergunta_id');
            $table->unsignedBigInteger('resposta_indicador_id');
            $table->string('descricao', 500)->nullable();
            $table->timestamps();
            $table->unique(['checklist_consultor_id', 'checklist_pergunta_id'], 'checklist_resposta_uk');
            $table->foreign('checklist_consultor_id')->references('id')->on('checklist_consultors');
            $table->foreign('checklist_pergunta_id')->references('id')->on('checklist_perguntas');
            $table->foreign('resposta_indicador_id')->references('id')->on('resposta_indicadors');
            $table->index(['checklist_consultor_id'], 'idx_checklist_respostas_01');		
        });
    }


    public function down()
    {
        Schema::dropIfExists('checklist_respostas');
    }
}
