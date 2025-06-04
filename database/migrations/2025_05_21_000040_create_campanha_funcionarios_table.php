<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampanhaFuncionariosTable extends Migration
{

    public function up()
    {
        Schema::create('campanha_funcionarios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('campanha_id');
            $table->unsignedBigInteger('empresa_funcionario_id');
            $table->datetime('data_liberado');
            $table->datetime('data_iniciado')->nullable();
            $table->datetime('data_realizado')->nullable();
            $table->longtext('observacao')->nullable();
            $table->timestamps();
            $table->unique(['campanha_id', 'empresa_funcionario_id'], 'campanha_funcionario_uk');
            $table->foreign('campanha_id')->references('id')->on('campanhas');
            $table->foreign('empresa_funcionario_id')->references('id')->on('empresa_funcionarios');
        });
    }


    public function down()
    {
        Schema::dropIfExists('campanha_funcionarios');
    }
}
