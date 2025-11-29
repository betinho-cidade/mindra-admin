@extends('painel.layout.index')


@section('content')

    <div class="max-w-4xl mx-auto pb-10">

        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-blue-800 mb-2">{{ $checklist->titulo }}</h1>
            <p class="text-gray-600 px-4"> {{ $checklist->descricao ?? 'Prévia das questões que serão exibidas para os consutlores no prenchimento do checklist!' }}</p>
        </div>

        <form id="assessmentForm">
            @php $cont = -1 @endphp
            @foreach($checklist->checklist_etapas->sortBy('ordem') as $checklist_etapa)
                @php $cont++; @endphp
               
                <div class="mb-10">
                    <div class="flex items-center mb-6 border-b-2 border-blue-800 pb-2">
                        <div class="bg-blue-800 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3 font-bold">1</div>
                        <h2 class="text-2xl font-bold text-gray-800">{{ ($checklist->visivel_formulario == 'S' ) ? $checklist_etapa->titulo : 'Responda as questões abaixo' }}</h2>
                    </div>

                    @foreach($checklist_etapa->checklist_perguntas->sortBy('ordem') as $checklist_pergunta)
                    <div class="bg-white p-5 rounded-lg shadow-md mb-6 border border-gray-100">
                        <p class="text-lg font-medium text-gray-800 mb-4">
                           {{ $checklist_pergunta->titulo }}
                        </p>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-{{$checklist->resposta->resposta_indicadors->count()}} gap-3">
                             
                            @foreach($checklist->resposta->resposta_indicadors->sortBy('ordem') as $resposta_indicador)
                            <label class="relative w-full">
                                <input type="radio" name="pergunta_181" value="12" class="option-input" required>
                                <div class="option-label">
                                    <div class="check-indicator"></div>
                                    <span>{{$resposta_indicador->titulo}}</span>
                                </div>
                            </label>
                            @endforeach

                        </div>
                    </div>
                    @endforeach

                </div>

            @endforeach

            <div class="mt-8 text-center">
                <button type="submit" id="submitButton" class="bg-green-600 hover:bg-green-700 text-white font-bold py-4 px-10 rounded-lg shadow-lg transform transition hover:scale-105 text-lg w-full sm:w-auto" disabled>
                    Enviar Avaliação
                </button>
            </div>

        </form>
    </div>

@endsection

@section('script-js')
    <script src="https://cdn.tailwindcss.com"></script>
@endsection

@section('head-css')

    <style>
        /* Esconde o input radio original */
        .option-input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }

        /* Estilo do botão quando NÃO selecionado */
        .option-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 0.75rem;
            background-color: #f3f4f6; /* cinza claro */
            border: 2px solid #e5e7eb;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.875rem;
            font-weight: 500;
            color: #4b5563;
            text-align: center;
            height: 100%;
        }

        /* Hover no Desktop */
        .option-label:hover {
            background-color: #e0e7ff;
            border-color: #a5b4fc;
        }

        /* Estilo do botão quando SELECIONADO (a mágica acontece aqui via CSS :checked) */
        .option-input:checked + .option-label {
            background-color: #1e40af; /* Azul escuro do seu tema */
            border-color: #1e40af;
            color: white;
            box-shadow: 0 4px 6px -1px rgba(30, 64, 175, 0.3);
            font-weight: bold;
        }

        /* Círculo visual para parecer radio button (opcional, ajuda na affordance) */
        .check-indicator {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            border: 2px solid currentColor;
            margin-bottom: 4px;
            position: relative;
        }
        
        .option-input:checked + .option-label .check-indicator {
            background-color: white;
            border-color: white;
        }
        
        .option-input:checked + .option-label .check-indicator::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 6px;
            height: 6px;
            background-color: #1e40af;
            border-radius: 50%;
        }
    </style>

@endsection

