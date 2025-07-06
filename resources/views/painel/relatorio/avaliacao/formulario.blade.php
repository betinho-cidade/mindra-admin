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

    @if ($errors->any())
        <div class="row">
            <div class="col-12">
                <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
                </div>
            </div>
        </div>
    @endif

    @php $formulario = $campanha_funcionario->campanha->formulario;  @endphp
    <div class="container mx-auto p-6 bg-white rounded-lg shadow-lg" style="margin-bottom: 30px;">
        <h1 class="main-title">{{ $formulario->titulo }}</h1>
        <p class="description">
            {{ $formulario->descricao ?? 'Questões para serem respondidas!' }}
        </p>

        <form id="assessmentForm" name="assessmentForm" method="POST" action="{{route('avaliacao.store', compact('campanha_funcionario'))}}"  class="needs-validation"  accept-charset="utf-8" enctype="multipart/form-data" novalidate>
        @csrf
            @php $cont = -1 @endphp
            @foreach($formulario->formulario_etapas->sortBy('ordem') as $formulario_etapa)
                @php $cont++; @endphp

                @if($formulario->visivel_formulario == 'S' || $cont == 0)
                <h2 class="topic-header">{{ ($formulario->visivel_formulario == 'S' ) ? $formulario_etapa->titulo : 'Responda as questões abaixo' }}</h2>

                <table class="w-full border-collapse mt-4 table-container">
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

            <p class="description">
                <div class="form-group">
                    <label for="resumo">Observações</label>
                    <textarea class="form-control" name="observacao" id="observacao" rows="3" placeholder="Escreva aqui suas observações">{{old('observacao')}}</textarea>
                </div>
            </p>

            <div class="mt-6 text-center">
                <button type="submit" id="submitButton" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 opacity-50 cursor-not-allowed" disabled>Enviar Avaliação</button>
            </div>

        </form>
    </div>


@endsection

@section('script-js')
    <script src="https://cdn.tailwindcss.com"></script>

    <script type='text/javascript'>
        // Função para validar se todas as perguntas foram respondidas
        function validateForm() {
            const form = document.getElementById('assessmentForm');
            const radioGroups = form.querySelectorAll('input[type="radio"][name]');
            const uniqueGroups = [...new Set(Array.from(radioGroups).map(input => input.name))]; // Lista de nomes únicos (q1, q2, ...)
            const submitButton = document.getElementById('submitButton');

            // Verifica se cada grupo de radio buttons tem uma opção selecionada
            const allAnswered = uniqueGroups.every(name => {
                return form.querySelector(`input[name="${name}"]:checked`);
            });

            // Habilita ou desabilita o botão de submit
            if (allAnswered) {
                submitButton.removeAttribute('disabled');
                submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
                submitButton.classList.add('hover:bg-blue-600');
                submitButton.innerHTML = 'Enviar Avaliação';
                submitButton.innerText = 'Enviar Avaliação';
            } else {
                submitButton.setAttribute('disabled', 'true');
                submitButton.classList.add('opacity-50', 'cursor-not-allowed');
                submitButton.classList.remove('hover:bg-blue-600');
                submitButton.innerHTML = 'Necessário marcar todas as respostas para habilitar envio!';
                submitButton.innerText = 'Necessário marcar todas as respostas para habilitar envio!';
            }
        }

        // Adiciona evento de change a todos os radio buttons
        document.querySelectorAll('input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', validateForm);
        });

        // Valida o formulário na inicialização
        validateForm();
    </script>

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
        /* Container para a tabela */
        .table-container {
            max-height: 600px; /* Altura máxima para rolagem vertical */
            overflow-y: auto; /* Habilita rolagem vertical */
            overflow-x: auto; /* Habilita rolagem horizontal */
            display: block; /* Necessário para rolagem */
            width: 100%; /* Ocupa a largura disponível */
        }
        /* Estilo da tabela */
        table {
            width: 800px; /* Largura maior que a tela para rolagem horizontal */
            border-collapse: collapse;
        }
        /* Estilo do thead */
        thead {
            position: sticky; /* Fixa o thead verticalmente */
            top: 0; /* Cola no topo */
            background-color: #f8f8f8; /* Cor de fundo */
            z-index: 2; /* Fica acima do tbody e da primeira coluna */
        }
        /* Estilo da primeira coluna */
        th:first-child, td:first-child {
            position: sticky; /* Fixa a primeira coluna horizontalmente */
            left: 0; /* Cola à esquerda */
            /*background-color: #000;  Cor de fundo para destacar */
            z-index: 1; /* Fica acima do tbody, mas abaixo do thead */
        }
        /* Estilo das células */
        th, td {
            text-align: center;
            vertical-align: middle;
            padding: 8px;
            font-size: 14px;
            min-width: 150px; /* Largura mínima para colunas */
        }
        /* Ajuste específico para o canto superior esquerdo (thead da primeira coluna) */
        thead th:first-child {
            z-index: 3; /* Fica acima de tudo */
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

        /* Media query para dispositivos móveis */
        @media screen and (max-width: 600px) {

            .table-container {
                max-height: 300px; /* Altura menor no mobile */
            }
            th:first-child, td:first-child {
             background-color: #f4f5f7;;/*  Cor de fundo para destacar */
            }
            th, td {
                font-size: 12px; /* Fonte menor no mobile */
                padding: 6px; /* Menos padding no mobile */
                min-width: 120px; /* Ajuste de largura mínima para colunas */
            }
            table {
                width: 600px; /* Mantém largura maior que a tela para rolagem horizontal */
            }
            body[data-sidebar=dark] .navbar-brand-box {
                max-width: 70px;
            }
            .logo span.logo-sm {
                display: flex;
            }
            .main-title {
                font-size: 21px !important;
            }
    </style>
@endsection

