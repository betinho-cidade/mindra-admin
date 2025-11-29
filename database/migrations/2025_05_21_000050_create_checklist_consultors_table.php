<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChecklistConsultorsTable extends Migration
{

    public function up()
    {
        Schema::create('checklist_consultors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('campanha_id');
            $table->unsignedBigInteger('consultor_empresa_id');
            $table->datetime('data_liberado');
            $table->datetime('data_iniciado')->nullable();
            $table->datetime('data_realizado')->nullable();
            $table->longtext('observacao')->nullable();
            $table->timestamps();
            $table->unique(['campanha_id', 'consultor_empresa_id'], 'campanha_consultor_uk');
            $table->foreign('campanha_id')->references('id')->on('campanhas');
            $table->foreign('consultor_empresa_id')->references('id')->on('consultor_empresas');
        });
    }


    public function down()
    {
        Schema::dropIfExists('checklist_consultors');
    }
}
