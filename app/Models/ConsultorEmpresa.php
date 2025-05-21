<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsultorEmpresa extends Model
{
    use HasFactory;
    public function consultor()
    {
        return $this->belongsTo('App\Models\Consultor');
    }

    public function empresa()
    {
        return $this->belongsTo('App\Models\Empresa');
    }

}
