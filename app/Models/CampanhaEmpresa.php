<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampanhaEmpresa extends Model
{
    use HasFactory;
    public function campanha()
    {
        return $this->belongsTo('App\Models\Campanha');
    }

    public function empresa()
    {
        return $this->belongsTo('App\Models\Empresa');
    }

    public function campanha_funcionarios(){
        return $this->hasMany('App\Models\CampanhaFuncionario');
    }

}
