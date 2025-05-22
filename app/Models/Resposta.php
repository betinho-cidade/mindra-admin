<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resposta extends Model
{
    use HasFactory;

    public function formularios(){
        return $this->hasMany('App\Models\Formulario');
    }

    public function resposta_indicadors(){
        return $this->hasMany('App\Models\RespostaIndicador');
    }

}
