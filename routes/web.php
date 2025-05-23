<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'App\Http\Controllers'], function(){

    Route::middleware(['auth'])->group(function () {

        Route::get('/logout', 'HomeController@logout')->name('logout');
        Route::get('/home', 'HomeController@index')->name('home');

        Route::group(['namespace' => 'Painel'], function(){

            Route::get('/', 'PainelController@index')->name('painel');
            Route::post('/preview_formulario/{formulario}', 'PainelController@preview_formulario')->name('painel.preview_formulario');
            Route::post('/js_viacep', 'PainelController@js_viacep')->name('painel.js_viacep');
            Route::post('/js_cnpj', 'PainelController@js_cnpj')->name('painel.js_cnpj');
            Route::post('/js_menu_aberto', 'PainelController@js_menu_aberto')->name('painel.js_menu_aberto');

            Route::group(['namespace' => 'Cadastro'], function(){

                Route::group(['namespace' => 'Usuario'], function(){
                    Route::get('/usuario', 'UsuarioController@index')->name('usuario.index');
                    Route::get('/usuario/search', 'UsuarioController@search')->name('usuario.search');
                    Route::get('/usuario/create', 'UsuarioController@create')->name('usuario.create');
                    Route::post('/usuario/store', 'UsuarioController@store')->name('usuario.store');
                    Route::get('/usuario/{usuario}', 'UsuarioController@show')->name('usuario.show');
                    Route::put('/usuario/{usuario}/update', 'UsuarioController@update')->name('usuario.update');
                    Route::delete('/usuario/{usuario}/destroy', 'UsuarioController@destroy')->name('usuario.destroy');
                });

                Route::group(['namespace' => 'UsuarioLogado'], function(){
                    Route::get('/usuario_logado/{user}', 'UsuarioLogadoController@show')->name('usuario_logado.show');
                    Route::put('/usuario_logado/{usuario_logado}/update', 'UsuarioLogadoController@update')->name('usuario_logado.update');
                });

                Route::group(['namespace' => 'Home'], function(){

                    Route::group(['namespace' => 'Empresa'], function(){
                        Route::get('/empresa', 'EmpresaController@index')->name('empresa.index');
                        Route::get('/empresa/create', 'EmpresaController@create')->name('empresa.create');
                        Route::post('/empresa/store', 'EmpresaController@store')->name('empresa.store');
                        Route::get('/empresa/{empresa}', 'EmpresaController@show')->name('empresa.show');
                        Route::put('/empresa/{empresa}/update', 'EmpresaController@update')->name('empresa.update');
                        Route::delete('/empresa/{empresa}/destroy', 'EmpresaController@destroy')->name('empresa.destroy');
                        Route::get('/empresa/{empresa}/consultor_create', 'EmpresaController@consultor_create')->name('empresa.consultor_create');
                        Route::put('/empresa/{empresa}/consultor_store', 'EmpresaController@consultor_store')->name('empresa.consultor_store');
                        Route::delete('/empresa/{empresa}/consultor_destroy/{consultor_empresa}', 'EmpresaController@consultor_destroy')->name('empresa.consultor_destroy');
                        Route::put('/empresa/{empresa}/consultor_status/{consultor_empresa}', 'EmpresaController@consultor_status')->name('empresa.consultor_status');
                    });

                    Route::group(['namespace' => 'Campanha'], function(){
                        Route::get('/campanha', 'CampanhaController@index')->name('campanha.index');
                        Route::get('/campanha/create', 'CampanhaController@create')->name('campanha.create');
                        Route::post('/campanha/store', 'CampanhaController@store')->name('campanha.store');
                        Route::get('/campanha/{campanha}', 'CampanhaController@show')->name('campanha.show');
                        Route::put('/campanha/{campanha}/update', 'CampanhaController@update')->name('campanha.update');
                        Route::delete('/campanha/{campanha}/destroy', 'CampanhaController@destroy')->name('campanha.destroy');
                    });

                });
            });

            Route::group(['namespace' => 'Gestao'], function(){

                Route::group(['namespace' => 'Empresa'], function(){
                    Route::get('/gestao/empresa', 'EmpresaController@index')->name('empresa_funcionario.index');
                    Route::get('/gestao/empresa/{empresa}', 'EmpresaController@show')->name('empresa_funcionario.show');
                    Route::get('/gestao/empresa/{empresa}/create', 'EmpresaController@create')->name('empresa_funcionario.create');
                    Route::post('/gestao/empresa/{empresa}/store', 'EmpresaController@store')->name('empresa_funcionario.store');
                    Route::post('/gestao/empresa/{empresa}/import', 'EmpresaController@import')->name('empresa_funcionario.import');
                    Route::put('/gestao/empresa/{empresa}/invite', 'EmpresaController@invite')->name('empresa_funcionario.invite');
                    Route::get('/gestao/empresa/{empresa}/logImport', 'EmpresaController@logImport')->name('empresa_funcionario.logImport');
                    Route::get('/gestao/empresa/{empresa}/logInvite', 'EmpresaController@logInvite')->name('empresa_funcionario.logInvite');
                    Route::get('/gestao/empresa/empresa_funcionario/{empresa_funcionario}', 'EmpresaController@show_funcionario')->name('empresa_funcionario.show_funcionario');
                    Route::put('/gestao/empresa/empresa_funcionario/{empresa_funcionario}/update', 'EmpresaController@update_funcionario')->name('empresa_funcionario.update_funcionario');
                    Route::delete('/gestao/empresa/empresa_funcionario/{empresa_funcionario}/destroy', 'EmpresaController@destroy_funcionario')->name('empresa_funcionario.destroy_funcionario');
                });

                Route::group(['namespace' => 'CampanhaEmpresa'], function(){
                    Route::get('/campanha_empresa/create/{empresa}', 'CampanhaEmpresaController@create')->name('campanha_empresa.create');
                    Route::put('/campanha_empresa/store/{empresa}', 'CampanhaEmpresaController@store')->name('campanha_empresa.store');
                    Route::get('/campanha_empresa/logAvaliacao/{campanha_empresa}', 'CampanhaEmpresaController@logAvaliacao')->name('campanha_empresa.logAvaliacao');
                    Route::get('/campanha_empresa/avaliacaos/{campanha_empresa}', 'CampanhaEmpresaController@avaliacaos')->name('campanha_empresa.avaliacaos');
                    Route::delete('/campanha_empresa/campanha_funcionario/{campanha_funcionario}/destroy_funcionario', 'CampanhaEmpresaController@destroy_funcionario')->name('campanha_empresa.destroy_funcionario');
                    Route::delete('/campanha_empresa/{campanha}/destroy/{campanha_empresa}', 'CampanhaEmpresaController@destroy')->name('campanha_empresa.destroy');
                    Route::put('/campanha_empresa/{campanha}/libera_funcionario/{campanha_empresa}', 'CampanhaEmpresaController@libera_funcionario')->name('campanha_empresa.libera_funcionario');
                });

            });



            Route::group(['namespace' => 'Relatorio'], function(){

                Route::group(['namespace' => 'Dashboard'], function(){
                    Route::get('/relatorio/dashboard', 'DashboardController@index')->name('dashboard.index');
                    Route::get('/relatorio/compras', 'DashboardController@compras')->name('dashboard.compras');
                    Route::get('/relatorio/download', 'DashboardController@download')->name('dashboard.download');
                    Route::get('/relatorio/download_pg', 'DashboardController@download_pg')->name('dashboard.download_pg');
                    Route::get('/relatorio/download_aa', 'DashboardController@download_aa')->name('dashboard.download_aa');
                    Route::get('/relatorio/download_vd', 'DashboardController@download_vd')->name('dashboard.download_vd');
                });

            });

        });
    });

});

require __DIR__.'/auth.php';
