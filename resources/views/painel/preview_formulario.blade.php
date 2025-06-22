@extends('painel.layout.index')


@section('content')

    <div class="container mx-auto p-6 bg-white rounded-lg shadow-lg" style="margin-bottom: 30px;">
        <h1 class="main-title">{{ $formulario->titulo }}</h1>
        <p class="description">
            {{ $formulario->descricao ?? 'Prévia das questões que serão exibidas para os funcionários no prenchimento do formulário!' }}
        </p>

        <form id="assessmentForm">
            @php $cont = -1 @endphp
            @foreach($formulario->formulario_etapas->sortBy('ordem') as $formulario_etapa)
                @php $cont++; @endphp
                @if($formulario->visivel_formulario == 'S' || $cont == 0)
                    <h2 class="topic-header">{{ ($formulario->visivel_formulario == 'S' ) ? $formulario_etapa->titulo : 'Responda as questões abaixo' }}</h2>

                    <table class="w-full border-collapse mt-4">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="p-3 text-left">Pergunta</th>
                                @foreach($formulario->resposta->resposta_indicadors->sortBy('ordem') as $resposta_indicador)
                                    <th class="p-3">{{ $resposta_indicador->titulo }}</th>
                                @endforeach
                            </tr>
                        </thead>
                    <tbody>
                @endif
                    @foreach($formulario_etapa->formulario_perguntas->sortBy('ordem') as $formulario_pergunta)
                        <tr class="border-b">
                            <td class="p-3 text-left">{{ $formulario_pergunta->titulo }}</td>
                            @foreach($formulario->resposta->resposta_indicadors->sortBy('ordem') as $resposta_indicador)
                                <td class="p-3 radio-group">
                                    <input type="radio" id="pergunta_{{ $formulario_pergunta->id }}:{{ $resposta_indicador->id }}" name="pergunta_{{ $formulario_pergunta->id }}" value="{{ $resposta_indicador->id }}" required class="hidden">
                                    <label for="pergunta_{{ $formulario_pergunta->id }}:{{ $resposta_indicador->id }}" class="block">◉</label>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach

                    @if($formulario->visivel_formulario == 'S')
                            </tbody>
                        </table>
                    @endif
            @endforeach

            @if($formulario->visivel_formulario == 'N')
                    </tbody>
                </table>
            @endif

        </form>
    </div>


@endsection

@section('script-js')
    <script src="https://cdn.tailwindcss.com"></script>
@endsection

@section('head-css')

    <style>
        /* Estilização adicional para personalização */
        .radio-group input:checked + label {
            background-color: #3b82f6;
            color: white;
            font-weight: bold;
        }
        .radio-group label {
            transition: all 0.2s ease-in-out;
            cursor: pointer;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
        }
        .radio-group label:hover {
            background-color: #e5e7eb;
        }
        th, td {
            text-align: center;
            vertical-align: middle;
        }
        .topic-header {
            background-color: #1f2937;
            color: white;
            font-size: 1.25rem;
            padding: 1rem;
            margin-top: 2rem;
            border-radius: 0.375rem;
        }
        .main-title {
            background-color: #1e40af;
            color: white;
            padding: 1.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            font-size: 2rem;
            font-weight: bold;
            text-align: center;
        }
        .description {
            color: #4b5563;
            font-size: 1rem;
            text-align: center;
            margin-top: 1rem;
            margin-bottom: 2rem;
        }
        tbody tr:nth-child(odd) {
            background-color: #f9fafb; /* Cinza bem claro */
        }
        tbody tr:nth-child(even) {
            background-color: #ffffff; /* Branco */
        }
        #submitButton:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        @media (max-width: 640px) {
            table {
                font-size: 0.875rem;
            }
            th, td {
                padding: 0.5rem;
            }
            .main-title {
                font-size: 1.5rem;
                padding: 1rem;
            }
        }
    </style>
@endsection

