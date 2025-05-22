<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormularioEtapa extends Model
{
    use HasFactory;

    public function formulario()
    {
        return $this->belongsTo('App\Models\Formulario');
    }

    public function formulario_perguntas(){
        return $this->hasMany('App\Models\FormularioPergunta');
    }

}
