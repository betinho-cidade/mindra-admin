<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RespostaIndicador extends Model
{
    use HasFactory;

    public function resposta()
    {
        return $this->belongsTo('App\Models\Resposta');
    }

}
