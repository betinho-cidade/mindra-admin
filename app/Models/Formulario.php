<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formulario extends Model
{
    use HasFactory;

    public function resposta()
    {
        return $this->belongsTo('App\Models\Resposta');
    }

    public function campanhas(){
        return $this->hasMany('App\Models\Campanha');
    }

    public function formulario_etapas(){
        return $this->hasMany('App\Models\FormularioEtapa');
    }

}
