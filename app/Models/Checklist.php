<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Checklist extends Model
{
    use HasFactory;

    public function resposta()
    {
        return $this->belongsTo('App\Models\Resposta');
    }

    public function campanhas(){
        return $this->hasMany('App\Models\Campanha');
    }

    public function checklist_etapas(){
        return $this->hasMany('App\Models\ChecklistEtapa');
    }

    public function getTituloReduzidoAttribute()
    {
        $titulo_reduzido =  Str::limit($this->titulo, ENV('TITULO_REDUZIDO'), '...');

        return $titulo_reduzido;
    }    

}
