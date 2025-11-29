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

    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold text-center mb-8 text-gray-800">Checklists das Campanhas</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Coluna de Campanhas Ativas/Em Andamento -->
            <div class="bg-cadet-blue rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-700">Campanhas Ativas/Em Andamento</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($checklist_consultors->whereNull('data_realizado') as $checklist_consultor)
                        <a href="javascript:;" onclick="checklist('{{ $checklist_consultor->id }}')">
                        <div class="bg-{{ ($checklist_consultor->campanha->data_fim < \Carbon\Carbon::now()) ? 'gray-100' : 'green-50' }} rounded-lg p-4 shadow hover:shadow-md transition-shadow h-40 overflow-y-auto {{ ($checklist_consultor->campanha->data_fim < \Carbon\Carbon::now()) ? 'opacity-70' : '' }}">

                            @if($checklist_consultor->campanha->data_fim < \Carbon\Carbon::now())
                            <div class="flex justify-between items-start">
                                <h3 class="text-sm font-semibold text-green-600">{{ $checklist_consultor->campanha->checklist->titulo }}</h3>
                                <span class="bg-red-300 text-red-700 text-xs font-medium px-2 py-1 rounded">Expirado</span>
                            </div>
                            @else
                                <h3 class="text-sm font-semibold text-green-600">{{ $checklist_consultor->campanha->checklist->titulo }}</h3>
                            @endif

                            <p class="text-xs text-gray-600"><strong class="font-extrabold text-sm">Empresa:</strong> {{ $checklist_consultor->consultor_empresa->empresa->nome }}</p>
                            <p class="text-xs text-gray-600"><strong class="font-extrabold text-sm">Campanha:</strong> {{ $checklist_consultor->campanha->titulo }}</p>
                            <p class="text-xs text-gray-500"><strong class="font-extrabold text-sm">Período:</strong> {{ $checklist_consultor->campanha->periodo }}</p>
                        </div>
                        </a>
                    @endforeach
                </div>
            </div>
            <!-- Coluna de Campanhas Finalizadas -->
            <div class="bg-light-coral rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-700">Campanhas Finalizadas</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($checklist_consultors->whereNotNull('data_realizado') as $checklist_consultor)
                        <div class="bg-red-50 rounded-lg p-4 shadow hover:shadow-md transition-shadow h-40 overflow-y-auto">
                            <h3 class="text-sm font-semibold text-red-600">{{ $checklist_consultor->campanha->checklist->titulo }}</h3>
                            <p class="text-xs text-gray-600"><strong class="font-extrabold text-sm">Empresa:</strong> {{ $checklist_consultor->consultor_empresa->empresa->nome }}</p>
                            <p class="text-xs text-gray-600"><strong class="font-extrabold text-sm">Campanha:</strong> {{ $checklist_consultor->campanha->titulo }}</p>
                            <p class="text-xs text-gray-500"><strong class="font-extrabold text-sm">Período:</strong> {{ $checklist_consultor->campanha->periodo }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <form action="" id="startForm" method="post">
        @csrf
    </form>

@endsection

@section('script-js')
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'cadet-blue': '#5F9EA0',
                        'light-coral': '#F08080',
                    }
                }
            }
        }

        function checklist(checklist_consultor){
            if(checklist_consultor){
                var url = '{{ route('checklist.start', [':checklist_consultor']) }}';
                url = url.replace(':checklist_consultor', checklist_consultor);
                $("#startForm").attr('action', url);
                $("#startForm").submit();
            }
        }
    </script>


@endsection

