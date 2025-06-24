<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'App\Http\Controllers'], function(){

    Route::group(['namespace' => 'Guest'], function(){

        Route::group(['namespace' => 'ResetPassword'], function(){
            Route::get('/forget-password', 'ForgotPasswordController@getEmail')->name('forgot.password');
            Route::post('/forget-password', 'ForgotPasswordController@postEmail')->name('forgot.reset');
            Route::get('/reset-password/{token}', 'ResetPasswordController@getPassword')->name('reset.password');
            Route::post('/reset-password', 'ResetPasswordController@updatePassword')->name('reset');
            Route::get('/relatorio/dashboard', 'DashboardController@index')->name('dashboard.index');
            //Route::get('/create-password/{token}', 'ResetPasswordController@createPassword')->name('create.password');
        });

    });


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
                    Route::get('/campanha_empresa/{campanha}/logAvaliacao', 'CampanhaEmpresaController@logAvaliacao')->name('campanha_empresa.logAvaliacao');
                    Route::get('/campanha_empresa/{campanha}/avaliacaos', 'CampanhaEmpresaController@avaliacaos')->name('campanha_empresa.avaliacaos');
                    Route::put('/campanha_empresa/{campanha}/analisar_hse', 'CampanhaEmpresaController@analisar_hse')->name('campanha_empresa.analisar_hse');
                    Route::put('/campanha_empresa/{campanha}/libera_funcionario', 'CampanhaEmpresaController@libera_funcionario')->name('campanha_empresa.libera_funcionario');
                    Route::delete('/campanha_empresa/{campanha}/campanha_funcionario/{campanha_funcionario}/destroy_funcionario', 'CampanhaEmpresaController@destroy_funcionario')->name('campanha_empresa.destroy_funcionario');
                });

            });


            Route::group(['namespace' => 'Relatorio'], function(){

                Route::group(['namespace' => 'Dashboard'], function(){
                    Route::get('/relatorio/dashboard', 'DashboardController@index')->name('dashboard.index');
                });

                Route::group(['namespace' => 'Avaliacao'], function(){
                    Route::get('/relatorio/avaliacao', 'AvaliacaoController@index')->name('avaliacao.index');
                    Route::post('/relatorio/avaliacao/{campanha_funcionario}/start', 'AvaliacaoController@start')->name('avaliacao.start');
                    Route::post('/relatorio/avaliacao/{campanha_funcionario}/store', 'AvaliacaoController@store')->name('avaliacao.store');
                });

            });

        });
    });

});

require __DIR__.'/auth.php';
