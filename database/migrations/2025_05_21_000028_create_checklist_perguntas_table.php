<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChecklistPerguntasTable extends Migration
{

    public function up()
    {
        Schema::create('checklist_perguntas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('checklist_etapa_id');
            $table->string('titulo', 255);
            $table->integer('ordem')->default(1);
            $table->timestamps();
            $table->foreign('checklist_etapa_id')->references('id')->on('checklist_etapas');
        });
    }


    public function down()
    {
        Schema::dropIfExists('checklist_perguntas');
    }
}
