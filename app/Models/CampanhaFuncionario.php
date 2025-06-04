<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampanhaFuncionario extends Model
{
    use HasFactory;
    public function campanha()
    {
        return $this->belongsTo('App\Models\Campanha');
    }

    public function empresa_funcionario()
    {
        return $this->belongsTo('App\Models\EmpresaFuncionario');
    }

    public function campanha_respostas(){
        return $this->hasMany('App\Models\CampanhaResposta');
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
