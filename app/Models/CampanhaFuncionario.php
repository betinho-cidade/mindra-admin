<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampanhaFuncionario extends Model
{
    use HasFactory;
    public function campanha_empresa()
    {
        return $this->belongsTo('App\Models\CampanhaEmpresa');
    }

    public function empresa_funcionario()
    {
        return $this->belongsTo('App\Models\EmpresaFuncionario');
    }

    public function campanha_respostas(){
        return $this->hasMany('App\Models\CampanhaResposta');
    }

}
