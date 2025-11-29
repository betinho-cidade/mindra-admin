<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistPergunta extends Model
{
    use HasFactory;

    public function checklist_etapa()
    {
        return $this->belongsTo('App\Models\ChecklistEtapa');
    }

    public function checklist_respostas(){
        return $this->hasMany('App\Models\ChecklistResposta');
    }

}
