<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;


class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nome',
        'cpf',
        'email',
        'password'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getBreadcrumbAttribute()
    {

        $usuario = $this->id;

        $bread = '<a href="' . route('usuario.index') . '">Lista Usuários</a>';
        $bread .= ' > ';
        $bread .= '<a href="' . route('usuario.show', compact('usuario')) . '">' . $this->nome . '</a>';

        return $bread;
    }


    public function getDataAlteracaoAttribute()
    {
        return date('d/m/Y H:i:s', strtotime($this->updated_at));
    }


    public function getPerfilIdAttribute()
    {
        $perfil = $this->rolesAll();

        return $perfil->first()->id;
    }


    public function getPerfilAttribute()
    {
        $perfil = $this->rolesAll();

        return $perfil->first()->name;
    }


    public function getSituacaoAttribute()
    {
        $situacao = $this->rolesAll()
                       ->withPivot('status');

        return $situacao->first()->pivot;
    }


    public function getAvatarAttribute()
    {
        return ($this->path_avatar) ? asset('images/avatar').'/'.$this->path_avatar : asset('images/avatar') .'/avatar.png';
    }


    public function getNomeAbreviadoAttribute(){
        return Str::limit($this->nome, 12, '...');
    }

    public function getPrimeiroNomeAttribute()
    {
        $primeiro_nome = strtok($this->nome, " ");

        return $primeiro_nome;
    }


    public function getDataNascimentoAjustadaAttribute()
    {
        return ($this->data_nascimento) ? date('Y-m-d', strtotime($this->data_nascimento)): '';
    }

    public function empresa_createds(){
        return $this->hasMany('App\Models\Empresa');
    }

    public function empresa_updateds(){
        return $this->hasMany('App\Models\Empresa');
    }

    public function empresa_funcionario_createds(){
        return $this->hasMany('App\Models\Empresa');
    }

    public function empresa_funcionario_updateds(){
        return $this->hasMany('App\Models\Empresa');
    }

    public function funcionario(){
        return $this->hasOne('App\Models\Funcionario');
    }

    public function consultor(){
        return $this->hasOne('App\Models\Consultor');
    }

    public function rolesAll(){
        return $this->belongsToMany('App\Models\Role')
                    ->withTimestamps();
    }

    public function roles(){
        return $this->belongsToMany('App\Models\Role')
                        ->withPivot('status')
                        ->wherePivot('status', 'A')
                        ->withTimestamps();
    }


    public function hasPermission(Permission $permission){

        return $this->hasAnyRoles($permission->roles);
    }


    public function hasAnyRoles($roles){

        if(is_array($roles) || is_object($roles)){

            $return = false;
            foreach($roles as $role){

                if($this->roles->contains('name', $role->name))
                {
                    $return = true;
                    continue;
                }
            }
            return $return;
        }

        return $this->roles->contains('name', $roles);
    }



}
