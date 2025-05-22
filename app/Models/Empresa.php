<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    use HasFactory;

    public function getBreadcrumbAttribute()
    {

        $empresa = $this->id;

        $bread = '<a href="' . route('empresa.index') . '">Lista Empresas</a>';
        $bread .= ' > ';
        $bread .= '<a href="' . route('empresa.show', compact('empresa')) . '">' . $this->nome . '</a>';

        return $bread;
    }

    public function getBreadcrumbGestaoAttribute()
    {

        $empresa = $this->id;

        $bread = '<a href="' . route('empresa_funcionario.index') . '">Lista Empresas</a>';
        $bread .= ' > ';
        $bread .= '<a href="' . route('empresa_funcionario.show', compact('empresa')) . '">' . $this->nome . '</a>';

        return $bread;
    }

    public function empresa_created()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function empresa_updated()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function consultor_empresas(){
        return $this->hasMany('App\Models\ConsultorEmpresa');
    }

    public function empresa_funcionarios(){
        return $this->hasMany('App\Models\EmpresaFuncionario');
    }

    public function campanha_empresas(){
        return $this->hasMany('App\Models\CampanhaEmpresa');
    }
}
