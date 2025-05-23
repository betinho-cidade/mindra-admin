@extends('painel.layout.index')

@section('content')

    @if(session()->has('message.level'))
        <div class="row">
            <div class="col-12">
                <div class="alert alert-{{ session('message.level') }}">
                    {!! session('message.content') !!}
                </div>
            </div>
        </div>
    @endif

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0">Painel - Dashboard</h4>
        </div>

        @if($empresa_funcionarios)

            @foreach($empresa_funcionarios as $empresa_funcionario)

                @foreach($empresa_funcionario->campanha_funcionarios as $campanha_funcionario)
                    <div style="width: 250px; height: 100px; border: 2px solid #3498db; border-radius: 10px; padding: 10px; box-sizing: border-box; text-align: center; background-color: #f9f9f9; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
                        <h3 style="color: #2c3e50; font-size: 16px; margin: 5px 0; font-family: Arial, sans-serif;">{{ $campanha_funcionario->campanha_empresa->campanha->titulo }}</h3>
                        <p style="color: #7f8c8d; font-size: 12px; margin: 5px 0; font-family: Arial, sans-serif;"><strong>Empresa:</strong> {{ $campanha_funcionario->empresa_funcionario->empresa->nome }}</p>
                        <p style="color: #7f8c8d; font-size: 12px; margin: 5px 0; font-family: Arial, sans-serif;"><strong>Período:</strong> {{ $campanha_funcionario->campanha_empresa->campanha->periodo }}</p>
                    </div>
                @endforeach

            @endforeach

        @endif


    </div>
</div>
<!-- end page title -->

@endsection

@section('head-css')
@endsection

@section('script-js')
@endsection

