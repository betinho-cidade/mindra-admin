<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParceiroUser extends Model
{
    use HasFactory;

    public function parceiro()
    {
        return $this->belongsTo('App\Models\Parceiro');
    }    

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

}
