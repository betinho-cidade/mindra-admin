<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpresaFuncionario extends Model
{
    use HasFactory;
    public function funcionario()
    {
        return $this->belongsTo('App\Models\Funcionario');
    }

    public function empresa()
    {
        return $this->belongsTo('App\Models\Empresa');
    }

    public function campanha_funcionarios(){
        return $this->hasMany('App\Models\CampanhaFuncionarios');
    }

    public function empresa_funcionario_created()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function empresa_funcionario_updated()
    {
        return $this->belongsTo('App\Models\User');
    }

}
