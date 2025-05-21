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
            $table->unsignedBigInteger('funcionario_id');
            $table->datetime('data_liberacao');
            $table->datetime('data_realizacao')->nullable();
            $table->timestamps();
            $table->unique(['campanha_id', 'funcionario_id'], 'campanha_funcionario_uk');
            $table->foreign('campanha_id')->references('id')->on('campanhas');
            $table->foreign('funcionario_id')->references('id')->on('funcionarios');
        });
    }


    public function down()
    {
        Schema::dropIfExists('campanha_funcionarios');
    }
}
