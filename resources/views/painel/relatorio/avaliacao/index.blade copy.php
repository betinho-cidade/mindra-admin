@extends('painel.layout.index')


@section('content')

 <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold text-center mb-8 text-gray-800">Campanhas de Avaliação Comportamental</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Coluna de Campanhas Ativas/Em Andamento -->
            <div class="bg-cadet-blue rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-700">Campanhas Ativas/Em Andamento</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Card 1 -->
                    <div class="bg-green-50 rounded-lg p-4 shadow hover:shadow-md transition-shadow">
                        <h3 class="text-sm font-semibold text-green-600">Empresa: Tech Innovate</h3>
                        <p class="text-xs text-gray-600"><strong class="font-extrabold text-sm">Campanha:</strong> Avaliação Anual 2025</p>
                        <p class="text-xs text-gray-600"><strong class="font-extrabold text-sm">Título:</strong> Competências de Liderança</p>
                        <p class="text-xs text-gray-500"><strong class="font-extrabold text-sm">Período:</strong> 01/05/2025 - 30/06/2025</p>
                    </div>
                    <!-- Card 2 -->
                    <div class="bg-green-50 rounded-lg p-4 shadow hover:shadow-md transition-shadow">
                        <h3 class="text-sm font-semibold text-green-600">Empresa: Global Solutions</h3>
                        <p class="text-xs text-gray-600"><strong class="font-extrabold text-sm">Campanha:</strong> Feedback 360º</p>
                        <p class="text-xs text-gray-600"><strong class="font-extrabold text-sm">Título:</strong> Habilidades Interpessoais</p>
                        <p class="text-xs text-gray-500"><strong class="font-extrabold text-sm">Período:</strong> 15/04/2025 - 15/07/2025</p>
                    </div>
                    <!-- Card 3 -->
                    <div class="bg-green-50 rounded-lg p-4 shadow hover:shadow-md transition-shadow">
                        <h3 class="text-sm font-semibold text-green-600">Empresa: Visionary Labs</h3>
                        <p class="text-xs text-gray-600"><strong class="font-extrabold text-sm">Campanha:</strong> Avaliação Trimestral</p>
                        <p class="text-xs text-gray-600"><strong class="font-extrabold text-sm">Título:</strong> Colaboração em Equipe</p>
                        <p class="text-xs text-gray-500"><strong class="font-extrabold text-sm">Período:</strong> 01/03/2025 - 31/05/2025</p>
                    </div>
                    <!-- Card 4 -->
                    <div class="bg-green-50 rounded-lg p-4 shadow hover:shadow-md transition-shadow">
                        <h3 class="text-sm font-semibold text-green-600">Empresa: NextGen Tech</h3>
                        <p class="text-xs text-gray-600"><strong class="font-extrabold text-sm">Campanha:</strong> Desenvolvimento Pessoal</p>
                        <p class="text-xs text-gray-600"><strong class="font-extrabold text-sm">Título:</strong> Gestão de Tempo</p>
                        <p class="text-xs text-gray-500"><strong class="font-extrabold text-sm">Período:</strong> 10/04/2025 - 10/08/2025</p>
                    </div>
                    <!-- Card 5 -->
                    <div class="bg-green-50 rounded-lg p-4 shadow hover:shadow-md transition-shadow">
                        <h3 class="text-sm font-semibold text-green-600">Empresa: Innovate Corp</h3>
                        <p class="text-xs text-gray-600"><strong class="font-extrabold text-sm">Campanha:</strong> Cultura Organizacional</p>
                        <p class="text-xs text-gray-600"><strong class="font-extrabold text-sm">Título:</strong> Alinhamento Estratégico</p>
                        <p class="text-xs text-gray-500"><strong class="font-extrabold text-sm">Período:</strong> 20/05/2025 - 20/07/2025</p>
                    </div>
                </div>
            </div>
            <!-- Coluna de Campanhas Finalizadas -->
            <div class="bg-light-coral rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-700">Campanhas Finalizadas</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Card 1 -->
                    <div class="bg-red-50 rounded-lg p-4 shadow hover:shadow-md transition-shadow">
                        <h3 class="text-sm font-semibold text-red-600">Empresa: Tech Innovate</h3>
                        <p class="text-xs text-gray-600"><strong class="font-extrabold text-sm">Campanha:</strong> Avaliação Semestral 2024</p>
                        <p class="text-xs text-gray-600"><strong class="font-extrabold text-sm">Título:</strong> Desempenho em Equipe</p>
                        <p class="text-xs text-gray-500"><strong class="font-extrabold text-sm">Período:</strong> 01/07/2024 - 31/12/2024</p>
                    </div>
                    <!-- Card 2 -->
                    <div class="bg-red-50 rounded-lg p-4 shadow hover:shadow-md transition-shadow">
                        <h3 class="text-sm font-semibold text-red-600">Empresa: Future Corp</h3>
                        <p class="text-xs text-gray-600"><strong class="font-extrabold text-sm">Campanha:</strong> Avaliação de Cultura</p>
                        <p class="text-xs text-gray-600"><strong class="font-extrabold text-sm">Título:</strong> Alinhamento de Valores</p>
                        <p class="text-xs text-gray-500"><strong class="font-extrabold text-sm">Período:</strong> 01/01/2024 - 30/06/2024</p>
                    </div>
                    <!-- Card 3 -->
                    <div class="bg-red-50 rounded-lg p-4 shadow hover:shadow-md transition-shadow">
                        <h3 class="text-sm font-semibold text-red-600">Empresa: Global Solutions</h3>
                        <p class="text-xs text-gray-600"><strong class="font-extrabold text-sm">Campanha:</strong> Feedback Anual 2024</p>
                        <p class="text-xs text-gray-600"><strong class="font-extrabold text-sm">Título:</strong> Competências Técnicas</p>
                        <p class="text-xs text-gray-500"><strong class="font-extrabold text-sm">Período:</strong> 01/03/2024 - 31/08/2024</p>
                    </div>
                    <!-- Card 4 -->
                    <div class="bg-red-50 rounded-lg p-4 shadow hover:shadow-md transition-shadow">
                        <h3 class="text-sm font-semibold text-red-600">Empresa: Visionary Labs</h3>
                        <p class="text-xs text-gray-600"><strong class="font-extrabold text-sm">Campanha:</strong> Avaliação de Liderança</p>
                        <p class="text-xs text-gray-600"><strong class="font-extrabold text-sm">Título:</strong> Tomada de Decisão</p>
                        <p class="text-xs text-gray-500"><strong class="font-extrabold text-sm">Período:</strong> 15/02/2024 - 15/05/2024</p>
                    </div>
                    <!-- Card 5 -->
                    <div class="bg-red-50 rounded-lg p-4 shadow hover:shadow-md transition-shadow">
                        <h3 class="text-sm font-semibold text-red-600">Empresa: NextGen Tech</h3>
                        <p class="text-xs text-gray-600"><strong class="font-extrabold text-sm">Campanha:</strong> Avaliação de Competências</p>
                        <p class="text-xs text-gray-600"><strong class="font-extrabold text-sm">Título:</strong> Resolução de Conflitos</p>
                        <p class="text-xs text-gray-500"><strong class="font-extrabold text-sm">Período:</strong> 01/09/2023 - 31/12/2023</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
    </script>
@endsection

