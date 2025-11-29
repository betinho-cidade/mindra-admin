<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistEtapa extends Model
{
    use HasFactory;

    public function checklist()
    {
        return $this->belongsTo('App\Models\Checklist');
    }

    public function checklist_perguntas(){
        return $this->hasMany('App\Models\ChecklistPergunta');
    }

}
