@extends('painel.layout.index')


@section('content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between"  style="margin-bottom: -20px;">
            <h4 class="mb-0">Preview Formulário - {{ $formulario->titulo }}</h4>
        </div>
        <div><code>{{ $formulario->descricao ?? '' }}</code></div>
    </div>
</div>

<div class="row">
    <div class="col-12">

        <form id="disc-form">

            <div class="header-row">
                <div class="header-col"></div>
                @foreach($formulario->resposta->resposta_indicadors as $resposta_indicador)
                    <div class="header-col">{{ $resposta_indicador->titulo }}</div>
                @endforeach
            </div>

            @foreach($formulario->formulario_etapas as $formulario_etapa)
                <div class="highlighted-card">
                    @if($formulario_etapa->titulo)
                        <h5 class="card-title">{{ $formulario_etapa->titulo }}</h5>
                    @endif
                    @foreach($formulario_etapa->formulario_perguntas as $formulario_pergunta)
                        <div class="radio-row">
                            <label class="label-col">{{ $formulario_pergunta->titulo }}</label>
                            @foreach($formulario->resposta->resposta_indicadors as $resposta_indicador)
                                <div class="radio-col">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="pergunta_{{ $formulario_pergunta->id }}" id="pergunta_{{ $formulario_pergunta->id }}:{{ $resposta_indicador->id }}" value="{{ $resposta_indicador->id }}" required>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            @endforeach

        </form>

    </div>
</div>

@endsection

@section('head-css')
<style>
        body {
            background-color: #e9ecef;
            font-family: 'Arial', sans-serif;
            // padding: 20px;
        }
        .form-container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #004aad;
            text-align: center;
            margin-bottom: 20px;
        }
        .section-title {
            color: #004aad;
            font-size: 18px;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        .header-row {
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            margin-bottom: 10px;
        }
        .header-col {
            flex: 1;
            font-weight: bold;
            min-height: 40px; /* Garante altura mínima para alinhamento */
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .radio-row {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .label-col {
            flex: 0 0 200px;
            text-align: left;
            display: flex;
            align-items: center;
        }
        .radio-col {
            flex: 1;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 40px; /* Alinha com a altura do header */
        }
        .form-check-input {
            margin: 0;
        }
        .form-check {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100%; /* Garante que o input fique centralizado verticalmente */
        }
        .highlighted-card {
            border-left: 5px solid #004aad;
            padding: 15px;
            margin-bottom: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .card-title {
            color: #004aad;
            font-weight: bold;
            font-size: 18px;
            padding: 10px;
            background-color: #e9ecef;
            border-radius: 3px;
            margin-bottom: 15px;
            text-align: left;
        }
        .btn-submit {
            background-color: #f5a623;
            color: #fff;
            border: none;
        }
        .btn-submit:hover {
            background-color: #004aad;
        }
    </style>
@endsection

