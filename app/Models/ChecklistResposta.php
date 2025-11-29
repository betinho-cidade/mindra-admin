<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistResposta extends Model
{
    use HasFactory;
    public function checklist_consultor()
    {
        return $this->belongsTo('App\Models\ChecklistConsultor');
    }

    public function checklist_pergunta()
    {
        return $this->belongsTo('App\Models\ChecklistPergunta');
    }

    public function resposta_indicador()
    {
        return $this->belongsTo('App\Models\RespostaIndicador');
    }

}
