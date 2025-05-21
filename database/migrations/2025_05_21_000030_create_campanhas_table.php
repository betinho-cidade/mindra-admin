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
            $table->string('titulo', 255);
            $table->longtext('descricao')->nullable();
            $table->datetime('data_inicio');
            $table->datetime('data_fim');
            $table->timestamps();
            $table->foreign('formulario_id')->references('id')->on('formularios');
        });
    }


    public function down()
    {
        Schema::dropIfExists('campanhas');
    }
}
