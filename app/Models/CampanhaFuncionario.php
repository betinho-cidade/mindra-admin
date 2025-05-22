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

    public function funcionario()
    {
        return $this->belongsTo('App\Models\Funcionario');
    }

}
