<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormularioPergunta extends Model
{
    use HasFactory;

    public function formulario_etapa()
    {
        return $this->belongsTo('App\Models\FormularioEtapa');
    }

    public function campanha_respostas(){
        return $this->hasMany('App\Models\CampanhaResposta');
    }

}
