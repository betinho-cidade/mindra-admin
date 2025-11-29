<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistConsultor extends Model
{
    use HasFactory;
    public function campanha()
    {
        return $this->belongsTo('App\Models\Campanha');
    }

    public function consultor_empresa()
    {
        return $this->belongsTo('App\Models\ConsultorEmpresa');
    }

    public function checklist_respostas(){
        return $this->hasMany('App\Models\ChecklistResposta');
    }

    public function getDataLiberadoFormatadaAttribute()
    {
        return ($this->data_liberado) ? date('d.m.Y H:i', strtotime($this->data_liberado)) : ' - ';
    }

    public function getDataRealizadoFormatadaAttribute()
    {
        return ($this->data_realizado) ? date('d.m.Y H:i', strtotime($this->data_realizado)) : ' - ';
    }

    public function getDataIniciadoFormatadaAttribute()
    {
        return ($this->data_iniciado) ? date('d.m.Y H:i', strtotime($this->data_iniciado)) : ' - ';
    }

}
