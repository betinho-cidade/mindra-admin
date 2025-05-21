<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsultorEmpresasTable extends Migration
{

    public function up()
    {
        Schema::create('consultor_empresas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('consultor_id');
            $table->unsignedBigInteger('empresa_id');
            $table->enum('status', ['A', 'I'])->default('A');  //A->Ativo  I->Inativo
            $table->timestamps();
            $table->foreign('consultor_id')->references('id')->on('consultors');
            $table->foreign('empresa_id')->references('id')->on('empresas');
            $table->unique(['empresa_id', 'consultor_id'], 'consultor_empresa_uk');
        });
    }


    public function down()
    {
        Schema::dropIfExists('consultor_empresas');
    }
}
