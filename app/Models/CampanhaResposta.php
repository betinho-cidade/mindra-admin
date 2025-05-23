<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampanhaResposta extends Model
{
    use HasFactory;
    public function campanha_funcionario()
    {
        return $this->belongsTo('App\Models\CampanhaFuncionario');
    }

    public function formulario_pergunta()
    {
        return $this->belongsTo('App\Models\FormularioPergunta');
    }

    public function resposta_indicador()
    {
        return $this->belongsTo('App\Models\RespostaIndicador');
    }

}
