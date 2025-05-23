@extends('painel.layout.index')


@section('content')

    <div class="container mx-auto p-6 bg-white rounded-lg shadow-lg" style="margin-bottom: 30px;">
        <h1 class="main-title">{{ $formulario->titulo }}</h1>
        <p class="description">
            {{ $formulario->descricao ?? 'Prévia das questões que serão exibidas para os funcionários no prenchimento do formulário!' }}
        </p>

        <form id="assessmentForm">
            @foreach($formulario->formulario_etapas->sortBy('ordem') as $formulario_etapa)

                <h2 class="topic-header">{{ $formulario_etapa->titulo ?? 'Responda as questões abaixo' }}</h2>

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
                    @foreach($formulario_etapa->formulario_perguntas->sortBy('ordem') as $formulario_pergunta)
                        <tr class="border-b">
                            <td class="p-3 text-left">{{ $formulario_pergunta->titulo }}</td>
                            @foreach($formulario->resposta->resposta_indicadors->sortBy('ordem') as $resposta_indicador)
                                <td class="p-3 radio-group">
                                    <input type="radio" id="pergunta_{{ $formulario_pergunta->id }}:{{ $resposta_indicador->id }}" name="pergunta_{{ $formulario_pergunta->id }}" value="{{ $resposta_indicador->id }}" required class="hidden">
                                    <label for="pergunta_{{ $formulario_pergunta->id }}:{{ $resposta_indicador->id }}" class="block">✔</label>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endforeach
{{--
            <div class="mt-6 text-center">
                <button type="submit" id="submitButton" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 opacity-50 cursor-not-allowed" disabled>Enviar Avaliação</button>
            </div>  --}}

        </form>
    </div>


@endsection

@section('script-js')
    <script src="https://cdn.tailwindcss.com"></script>

    {{--  <script type='text/javascript'>
        // Função para validar se todas as perguntas foram respondidas
        function validateForm() {
            const form = document.getElementById('assessmentForm');
            const radioGroups = form.querySelectorAll('input[type="radio"][name]');
            const uniqueGroups = [...new Set(Array.from(radioGroups).map(input => input.name))]; // Lista de nomes únicos (q1, q2, ...)
            const submitButton = document.getElementById('submitButton');

            console.log(uniqueGroups);

            // Verifica se cada grupo de radio buttons tem uma opção selecionada
            const allAnswered = uniqueGroups.every(name => {
                return form.querySelector(`input[name="${name}"]:checked`);
            });

            // Habilita ou desabilita o botão de submit
            if (allAnswered) {
                submitButton.removeAttribute('disabled');
                submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
                submitButton.classList.add('hover:bg-blue-600');
            } else {
                submitButton.setAttribute('disabled', 'true');
                submitButton.classList.add('opacity-50', 'cursor-not-allowed');
                submitButton.classList.remove('hover:bg-blue-600');
            }
        }

        // Adiciona evento de change a todos os radio buttons
        document.querySelectorAll('input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', validateForm);
        });

        // Valida o formulário na inicialização
        validateForm();
    </script>  --}}

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

