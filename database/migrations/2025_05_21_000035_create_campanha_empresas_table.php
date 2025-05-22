<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampanhaEmpresasTable extends Migration
{

    public function up()
    {
        Schema::create('campanha_empresas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('campanha_id');
            $table->unsignedBigInteger('empresa_id');
            $table->timestamps();
            $table->foreign('campanha_id')->references('id')->on('campanhas');
            $table->foreign('empresa_id')->references('id')->on('empresas');
            $table->unique(['campanha_id', 'empresa_id'], 'campanha_empresa_uk');
        });
    }


    public function down()
    {
        Schema::dropIfExists('campanha_empresas');
    }
}
