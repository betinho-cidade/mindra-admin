<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campanha extends Model
{
    use HasFactory;

    public function getBreadcrumbAttribute()
    {

        $campanha = $this->id;

        $bread = '<a href="' . route('campanha.index') . '">Lista Campanhas</a>';
        $bread .= ' > ';
        $bread .= '<a href="' . route('campanha.show', compact('campanha')) . '">' . $this->titulo . '</a>';

        return $bread;
    }

    public function campanha_created()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function campanha_updated()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function formulario()
    {
        return $this->belongsTo('App\Models\Formulario');
    }

    public function campanha_empresas(){
        return $this->hasMany('App\Models\CampanhaEmpresa');
    }

    public function getDataInicioAjustadaAttribute()
    {
        return ($this->data_inicio) ? date('Y-m-d', strtotime($this->data_inicio)): '';
    }

    public function getDataInicioFormatadaAttribute()
    {
        return ($this->data_inicio) ? date('d-m-Y H:i', strtotime($this->data_inicio)) : '';
    }

    public function getDataFimAjustadaAttribute()
    {
        return ($this->data_fim) ? date('Y-m-d', strtotime($this->data_fim)): '';
    }

        public function getDataFimFormatadaAttribute()
    {
        return ($this->data_fim) ? date('d-m-Y H:i', strtotime($this->data_fim)) : '';
    }


    public function getPeriodoAttribute()
    {
        return date('d.m.Y', strtotime($this->data_inicio)) . ' à ' . date('d.m.Y', strtotime($this->data_fim));
    }

}
