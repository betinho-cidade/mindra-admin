<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

use Illuminate\Support\Facades\Gate;
use App\Models\User;


class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */

     public function boot()
     {
         $this->registerPolicies();

         try {
 
             $permissions = \App\Models\Permission::with('roles')->get();
 
             foreach($permissions as $permission){
                 Gate::define($permission->name, function(User $user) use ($permission) {
                     return $user->hasPermission($permission);
                 });
             }
     
         } catch (\Exception $e) {
             return [];
         }
 
     }     

}
