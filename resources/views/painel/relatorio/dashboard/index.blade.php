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

        <div class="report-container" style="margin-bottom: 30px">

                <div id="chart_resultado_empresa" class="chart-div">
                    <div class="chart-header-and-filters">
                        <h2>Resultado por empresa</h2>
                    </div>
                    <div id="chart_resultado_empresa_chart_area" class="chart-area"></div>
                </div>

                <div id="chart_evolucao_empresa" class="chart-div">
                    <div class="chart-header-and-filters">
                        <h2>Evolução da Empresa</h2>
                    </div>
                    <div class="filter-row">
                        <div class="form-group">
                            <form id="form_evolucao_empresa">
                                @csrf
                            <select id="selectEmpresaEvolucao" onchange="drawEvolucaoEmpresa()" class="filter-select">
                                @foreach ($empresas as $empresa)
                                    <option value="{{ $empresa->id }}">{{ $empresa->nome }}</option>
                                @endforeach
                            </select>
                            <img src="{{asset('images/loading.gif')}}" id="img-loading-evolucao" style="display:none;max-width: 3%; margin-left: 4px;">
                            </form>
                        </div>
                    </div>
                    <div id="chart_evolucao_empresa_chart_area" class="chart-area"></div>
                </div>

                <div id="chart_departamento" class="chart-div">
                    <div class="chart-header-and-filters">
                        <h2>Departamento</h2>
                    </div>
                    <div class="filter-row">
                        <div class="form-group">
                            <select id="selectEmpresaDepartamento" onchange="handleDepartmentCompanyChange()" class="filter-select">
                                <option value="">Carregando Empresas...</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <select id="selectCampanhaDepartamento" onchange="drawDepartmentChartWithSelectedData()" disabled class="filter-select">
                                <option value="">Selecione uma Empresa</option>
                            </select>
                        </div>
                    </div>
                    <div class="loading-spinner" id="spinner_departamento"></div>
                    <div id="chart_departamento_chart_area" class="chart-area"></div>
                </div>

                <div id="chart_risco_dimensao" class="chart-div">
                    <div class="chart-header-and-filters">
                        <h2>Médio do Risco por Dimensão</h2>
                    </div>
                    <div class="filter-row">
                        <div class="form-group">
                            <select id="selectEmpresaRiscoDimensao" onchange="handleDimensionRiskCompanyChange()" class="filter-select">
                                <option value="">Carregando Empresas...</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <select id="selectCampanhaRiscoDimensao" onchange="handleDimensionRiskCampaignChange()" disabled class="filter-select">
                                <option value="">Selecione a Empresa</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <select id="selectDepartamentoRiscoDimensao" onchange="drawDimensionRiskChartWithSelectedData()" disabled class="filter-select"> <!-- <<< CLASSE ADICIONADA -->
                                <option value="">Selecione a Campanha</option>
                            </select>
                        </div>
                    </div>
                    <div class="loading-spinner" id="spinner_risco_dimensao"></div>
                    <div id="chart_risco_dimensao_chart_area" class="chart-area"></div>
                </div>


            </div>


    </div>
</div>
<!-- end page title -->

@endsection

@section('head-css')
    <style>
        .report-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }
        .chart-div {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 10px;
            flex: 1 1 45%; /* Permite dois gráficos por linha, ajusta para telas menores */
            box-sizing: border-box;
            min-width: 450px; /* Largura mínima para a div do gráfico */
            height: 400px; /* Altura fixa para consistência */
            position: relative; /* Para posicionar o formulário e spinner internamente */
            display: flex;
            flex-direction: column;
            justify-content: space-between; /* Para empurrar o gráfico para o fundo se houver formulário */
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        .chart-div h2 {
            text-align: center;
            color: #333;
            margin-bottom: 10px; /* Ajuste para dar espaço ao formulário */
        }
        /* Estilo para o cabeçalho do gráfico com filtros (seção de título) */
        .chart-header-and-filters {
            display: flex;
            justify-content: space-between; /* Alinha título à esquerda e filtros à direita */
            align-items: center; /* Centraliza verticalmente */
            margin-bottom: 2px; /* Espaço abaixo do cabeçalho */
            flex-wrap: wrap; /* Permite quebrar linha em telas menores */
            gap: 10px; /* Espaçamento entre os itens */
        }
        .chart-header-and-filters h2 {
            margin: 0; /* Remove margem padrão do h2 para controle total */
            font-size: 1.4em; /* Tamanho da fonte do título dentro da div */
        }
        /* Novo estilo para a linha de filtros */
        .filter-row { /* <<< NOVO ESTILO */
            display: flex;
            flex-wrap: wrap;
            justify-content: left; /* Centraliza os selects na linha */
            gap: 10px; /* Espaçamento entre os selects */
            margin-bottom: 2px; /* Espaço abaixo da linha de filtros */
        }
        .form-group {
            display: flex;
            align-items: center;
        }
        .form-container label { /* Este estilo não será mais usado para os selects que estamos movendo */
            margin-right: 5px;
            font-weight: bold;
            font-size: 0.9em;
        }
        .filter-select { /* <<< ESTILO EXISTENTE, AGORA APLICADO DIRETAMENTE */
            padding: 3px 6px;
            border-radius: 4px;
            border: 1px solid #ccc;
            font-size: 0.9em; /* Reduz a fonte dos selects */
            max-width: 150px; /* Limita a largura do select para caber mais na linha */
        }
        .loading-spinner {
            display: none; /* Inicia oculto */
            border: 4px solid #f3f3f3; /* Light grey */
            border-top: 4px solid #3498db; /* Blue */
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
            position: absolute;
            top: 50%;
            left: 50%;
            margin-top: -10px; /* Centralizar verticalmente */
            margin-left: -10px; /* Centralizar horizontalmente */
            z-index: 10;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .chart-area {
            flex-grow: 1; /* Permite que o gráfico ocupe o espaço restante */
            width: 100%;
        }
    </style>

@endsection

@section('script-js')
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart', 'bar']});
        google.charts.setOnLoadCallback(drawAllCharts);

        // --- Dados de Simulação de Backend (substitua por suas APIs reais) ---
        // Você pode remover estes mocks quando integrar com seu backend.
        // Eles servem apenas para simular as respostas das suas rotas.

        const mockCompanies = [
            { id: 'emp1', name: 'Empresa Alpha' },
            { id: 'emp2', name: 'Empresa Beta' },
            { id: 'emp3', name: 'Empresa Gamma' },
            { id: 'emp4', name: 'Empresa Delta' }
        ];

        const mockCampaigns = {
            'emp1': [
                { id: 'camp1_e1', name: 'Campanha 2024-Q1 (Alpha)' },
                { id: 'camp2_e1', name: 'Campanha 2024-Q2 (Alpha)' }
            ],
            'emp2': [
                { id: 'camp1_e2', name: 'Campanha de Verão (Beta)' },
                { id: 'camp2_e2', name: 'Campanha de Inverno (Beta)' }
            ],
            'emp3': [
                { id: 'camp1_e3', name: 'Projeto A (Gamma)' },
                { id: 'camp2_e3', name: 'Projeto B (Gamma)' }
            ],
            'emp4': [
                { id: 'camp1_e4', name: 'Inovação (Delta)' }
            ]
        };

        const mockDepartments = {
            'camp1_e1': [
                { id: 'dep_mark', name: 'Marketing' },
                { id: 'dep_fin', name: 'Finanças' }
            ],
            'camp2_e1': [
                { id: 'dep_ti', name: 'TI' },
                { id: 'dep_vendas', name: 'Vendas' }
            ],
            'camp1_e2': [
                { id: 'dep_rh', name: 'RH' },
                { id: 'dep_log', name: 'Logística' }
            ],
             'camp2_e2': [
                { id: 'dep_rh', name: 'RH' },
                { id: 'dep_prod', name: 'Produção' }
            ],
            'camp1_e3': [
                { id: 'dep_pd', name: 'P&D' },
                { id: 'dep_eng', name: 'Engenharia' }
            ],
            'camp1_e4': [
                { id: 'dep_suporte', name: 'Suporte' },
                { id: 'dep_dev', name: 'Desenvolvimento' }
            ]
        };

        const mockDepartmentChartData = {
            'emp1_camp1_e1': [
                ['Departamento', 'Média de risco por departamento', '% Formulários respondidos'],
                ['Marketing', 8, 85],
                ['Finanças', 7, 90]
            ],
            'emp1_camp2_e1': [
                ['Departamento', 'Média de risco por departamento', '% Formulários respondidos'],
                ['TI', 7, 90],
                ['Vendas', 8, 80]
            ],
            'emp2_camp1_e2': [
                ['Departamento', 'Média de risco por departamento', '% Formulários respondidos'],
                ['RH', 9, 60],
                ['Logística', 8, 70]
            ],
            'emp2_camp2_e2': [
                ['Departamento', 'Média de risco por departamento', '% Formulários respondidos'],
                ['RH', 8, 75],
                ['Produção', 7, 85]
            ],
            'emp3_camp1_e3': [
                ['Departamento', 'Média de risco por departamento', '% Formulários respondidos'],
                ['P&D', 7, 95],
                ['Engenharia', 6, 90]
            ],
            'emp4_camp1_e4': [
                ['Departamento', 'Média de risco por departamento', '% Formulários respondidos'],
                ['Suporte', 5, 100],
                ['Desenvolvimento', 6, 98]
            ]
        };

        const mockDimensionRiskData = {
            'emp1_camp1_e1_dep_mark': [
                ['Dimensão', 'Média de risco', { role: 'annotation' }],
                ['Relacionamentos', 7, 7],
                ['Papel', 6, 6],
                ['Mudanças', 5, 5],
                ['Demandas', 8, 8],
                ['Controle', 6, 6],
                ['Apoio dos colegas', 7, 7],
                ['Apoio do gestor', 6, 6]
            ],
            'emp1_camp1_e1_dep_fin': [
                ['Dimensão', 'Média de risco', { role: 'annotation' }],
                ['Relacionamentos', 8, 8],
                ['Papel', 7, 7],
                ['Mudanças', 6, 6],
                ['Demandas', 9, 9],
                ['Controle', 7, 7],
                ['Apoio dos colegas', 8, 8],
                ['Apoio do gestor', 7, 7]
            ],
            'emp1_camp2_e1_dep_ti': [
                ['Dimensão', 'Média de risco', { role: 'annotation' }],
                ['Relacionamentos', 6, 6],
                ['Papel', 5, 5],
                ['Mudanças', 7, 7],
                ['Demandas', 6, 6],
                ['Controle', 5, 5],
                ['Apoio dos colegas', 6, 6],
                ['Apoio do gestor', 5, 5]
            ],
            'emp1_camp2_e1_dep_vendas': [
                ['Dimensão', 'Média de risco', { role: 'annotation' }],
                ['Relacionamentos', 7, 7],
                ['Papel', 6, 6],
                ['Mudanças', 7, 7],
                ['Demandas', 8, 8],
                ['Controle', 6, 6],
                ['Apoio dos colegas', 7, 7],
                ['Apoio do gestor', 7, 7]
            ],
            // Adicione mais dados para outras combinações empresa_campanha_departamento
        };

        // Dados de exemplo para as diferentes empresas na Evolução (mantido do exemplo anterior)
        const evolutionData = {
            'emp1': [
                ['Data', 'Média de risco por mês', '% Formulários respondidos'],
                ['01/03/2024', 8, 85],
                ['01/07/2024', 7, 90],
                ['01/11/2024', 6, 95],
                ['01/03/2025', 5, 100],
                ['01/07/2025', 4, 100]
            ],
            'emp2': [
                ['Data', 'Média de risco por mês', '% Formulários respondidos'],
                ['01/03/2024', 7, 75],
                ['01/07/2024', 6, 80],
                ['01/11/2024', 6, 85],
                ['01/03/2025', 5, 90],
                ['01/07/2025', 4, 95]
            ],
            'emp3': [
                ['Data', 'Média de risco por mês', '% Formulários respondidos'],
                ['01/03/2024', 9, 60],
                ['01/07/2024', 8, 70],
                ['01/11/2024', 7, 80],
                ['01/03/2025', 6, 90],
                ['01/07/2025', 5, 95]
            ],
            'emp4': [
                ['Data', 'Média de risco por mês', '% Formulários respondidos'],
                ['01/03/2024', 6, 90],
                ['01/07/2024', 5, 95],
                ['01/11/2024', 5, 98],
                ['01/03/2025', 5, 100],
                ['01/07/2025', 4, 100]
            ]
        };


        // --- Funções AJAX Reais (com simulação de rota) ---
        // IMPORTANTE: Substitua estas implementações pelas suas chamadas 'fetch' reais para as rotas do seu backend.
        // O `route(...)` será interpretado pelo seu framework (ex: Laravel) no momento em que a página é gerada.
        // Aqui no JS, simulamos o resultado dessas rotas.

        async function fetchCompanies() {
            // No seu ambiente, você teria algo como:
            // const response = await fetch('/api/companies');
            // const data = await response.json();
            // return data;
            return new Promise(resolve => {
                setTimeout(() => {
                    resolve(mockCompanies);
                }, 300);
            });
        }

        async function fetchCampaigns(empresaId) {
            // Substitua esta linha pela sua chamada real:
            // const url = `route('dashboard.busca_campanhas', ['empresa' => 'PLACEHOLDER_EMPRESA_ID'])`.replace('PLACEHOLDER_EMPRESA_ID', empresaId);
            // const response = await fetch(url);
            // const data = await response.json();
            // return data; // Sua API deve retornar um array de objetos {id: '...', name: '...'}

            return new Promise(resolve => {
                setTimeout(() => {
                    resolve(mockCampaigns[empresaId] || []);
                }, 500); // Simula atraso de rede
            });
        }

        async function fetchDepartments(campanhaId) {
            // Substitua esta linha pela sua chamada real:
            // const url = `route('dashboard.busca_departamentos', ['campanha' => 'PLACEHOLDER_CAMPANHA_ID'])`.replace('PLACEHOLDER_CAMPANHA_ID', campanhaId);
            // const response = await fetch(url);
            // const data = await response.json();
            // return data; // Sua API deve retornar um array de objetos {id: '...', name: '...'}

            return new Promise(resolve => {
                setTimeout(() => {
                    resolve(mockDepartments[campanhaId] || []);
                }, 500); // Simula atraso de rede
            });
        }

        async function fetchDepartmentChartData(empresaId, campanhaId) {
            // Se você tiver uma API específica para estes dados, chame-a aqui.
            // Ex: const response = await fetch(`/api/departamento_data?empresa=${empresaId}&campanha=${campanhaId}`);
            // const data = await response.json();
            // return data;

            return new Promise(resolve => {
                setTimeout(() => {
                    const key = `${empresaId}_${campanhaId}`;
                    resolve(mockDepartmentChartData[key] || [['Departamento', 'Média de risco por departamento', '% Formulários respondidos']]);
                }, 700);
            });
        }

        async function fetchDimensionRiskChartData(empresaId, campanhaId, departamentoId) {
            // Se você tiver uma API específica para estes dados, chame-a aqui.
            // Ex: const response = await fetch(`/api/risco_dimensao_data?empresa=${empresaId}&campanha=${campanhaId}&departamento=${departamentoId}`);
            // const data = await response.json();
            // return data;

            return new Promise(resolve => {
                setTimeout(() => {
                    const key = `${empresaId}_${campanhaId}_${departamentoId}`;
                    resolve(mockDimensionRiskData[key] || [['Dimensão', 'Média de risco', { role: 'annotation' }]]);
                }, 800);
            });
        }


        // --- Funções de Desenho dos Gráficos ---

        function drawAllCharts() {
            drawResultadoEmpresa();
            drawEvolucaoEmpresa();
            populateDepartmentSelectsAndDrawChart(); // Inicia o processo para Departamento (Empresa, Campanha)
            populateDimensionRiskSelectsAndDrawChart(); // Inicia o processo para Média de Risco por Dimensão (Empresa, Campanha, Departamento)
        }


        function drawResultadoEmpresa() {
            var data = google.visualization.arrayToDataTable([
                        ['Empresa', 'Média de risco por empresa', { role: 'tooltip', type: 'string', p: { html: true } }, '% Formulários respondidos'],

                        @foreach ( $dash_empresa as $key => $value )
                                [{!! str_replace('"', "'", json_encode($value['Empresa'])) !!},
                                {!! str_replace('"', "'", json_encode($value['risco_medio'])) !!},
                                {!! str_replace('"', "'", json_encode($value['campanha'])) !!},
                                {!! str_replace('"', "'", json_encode($value['percentual_respondido'])) !!}],
                        @endforeach

                        ]);

            var view = new google.visualization.DataView(data);
            view.setColumns([
                0, // Coluna da Empresa (eixo X)
                1, // Coluna da Média de risco por empresa (dados das barras)
                {
                    calc: "stringify",
                    sourceColumn: 1,
                    type: "string",
                    role: "annotation"
                },
                // NOVO: Coluna de tooltip (índice 2 na nova dataTable)
                2, // Coluna de tooltip
                3, // Coluna de % Formulários respondidos (dados da linha - agora índice 3)
                {
                    calc: function(dataTable, rowNum) {
                        return dataTable.getValue(rowNum, 3) + '%'; // Ajustado para nova coluna 3
                    },
                    sourceColumn: 3, // Ajustado para nova coluna 3
                    type: "string",
                    role: "annotation"
                }
            ]);

            var options = {
                seriesType: 'bars',
                series: {
                    0: { // Série 0: Barras (Média de risco por empresa)
                        targetAxisIndex: 0,
                        color: '#4285F4', /* Cor azul padrão do Google Charts, como na imagem */
                        annotations: {
                            textStyle: {
                                fontSize: 12,
                                color: '#000', // Anotações das barras em preto
                                auraColor: 'none'
                            },
                            // Posiciona a anotação na base da barra
                            position: 'bottom',
                            // O offset pode precisar de ajuste fino dependendo do tamanho das barras e fontes
                            // Um valor negativo move para cima, positivo para baixo
                            stem: { length: 0, vAlign: 'bottom' }
                        }
                    },
                    1: { // Série 1: Linha (% Formulários respondidos)
                        type: 'line',
                        targetAxisIndex: 1,
                        pointShape: 'circle', /* Pontos visíveis na linha */
                        pointSize: 7, /* Tamanho dos pontos */
                        lineWidth: 2,
                        color: '#ea4335', /* Cor da linha é VERMELHA */
                        annotations: {
                             stem: { length: 0 },
                             textStyle: {
                                fontSize: 12,
                                color: '#ea4335', /* Anotações dos percentuais em VERMELHO */
                                auraColor: 'none'
                             },
                             alwaysOutside: true
                        }
                    }
                },
                vAxes: {
                    0: { // Eixo Y esquerdo: Média de risco
                        title: 'Média de risco', /* */
                        minValue: 0,
                        // maxValue: 10,
                        format: '#',
                        gridlines: { count: 5 },
                        viewWindow: { min: 0 } //, max: 10 }
                    },
                    1: { // Eixo Y direito: % Respondidos
                        title: '% Respondidos', /* */
                        minValue: 0,
                        maxValue: 100,
                        format: '#\'%\'',
                        gridlines: { count: 5 }, /* Linhas de grade também para o eixo secundário */
                        viewWindow: { min: 0, max: 100 }
                    }
                },
                legend: { position: 'bottom' }, /* */
                chartArea: {left: '10%', top: '10%', right: '10%', bottom: '20%'},
                bar: { groupWidth: '60%' },
                tooltip: { isHtml: true } // MUITO IMPORTANTE: Habilita tooltips HTML para formatar o conteúdo
            };

            var chart = new google.visualization.ComboChart(document.getElementById('chart_resultado_empresa_chart_area'));
            chart.draw(view, options);
        }

        function drawEvolucaoEmpresa() {
            const selectedEmpresa = document.getElementById('selectEmpresaEvolucao').value;
            console.log(selectedEmpresa);
            let dados, dataEvolucao;

            if (selectedEmpresa != ''){
                const chartArea = document.getElementById('chart_evolucao_empresa_chart_area');
                chartArea.innerHTML = "";

                document.getElementById("img-loading-evolucao").style.display = ''
                var empresa = selectedEmpresa;
                var _token = $('input[name="_token"]').val();

                $.ajax({
                    url: "{{route('dashboard.js_evolucao_empresa')}}",
                    method: "POST",
                    data: {_token:_token, empresa:empresa},
                    success:function(result){
                        dados = JSON.parse(result);

                        if(dados == '' || dados==null || dados['error'] == 'true'){
                            const chartArea = document.getElementById('chart_evolucao_empresa_chart_area');
                            chartArea.innerHTML = "<p style='text-align:center; padding-top:50px;'>Dados não disponíveis para esta empresa.</p>";
                            document.getElementById("img-loading-evolucao").style.display = 'none';
                            return;
                        } else {
                            dataEvolucao = [['Empresa', 'Média de risco por mês', { role: 'tooltip', type: 'string', p: { html: true } }, '% Formulários respondidos']];
                            dados.forEach(item => {
                                // Cria o conteúdo HTML personalizado para o tooltip
                                const tooltipHtml = `<div style='padding:5px; font-size:14px;'><span style='font-size:12px'>${item.mes}</font><br><b>${item.campanha}</b></div>`;
                                // Adiciona a linha formatada ao dataArray
                                dataEvolucao.push([
                                    item.data_campanha, // Nome da empresa/campanha
                                    item.risco_medio, // Média de risco
                                    tooltipHtml,
                                    item.percentual_respondido // Percentual de formulários respondidos
                                ]);
                            });

                            var data1 = google.visualization.arrayToDataTable(dataEvolucao);

                            var view1 = new google.visualization.DataView(data1);
                            view1.setColumns([
                                0, // Coluna da Empresa (eixo X)
                                1, // Coluna da Média de risco por empresa (dados das barras)
                                {
                                    calc: "stringify",
                                    sourceColumn: 1,
                                    type: "string",
                                    role: "annotation"
                                },
                                // NOVO: Coluna de tooltip (índice 2 na nova dataTable)
                                2, // Coluna de tooltip
                                3, // Coluna de % Formulários respondidos (dados da linha - agora índice 3)
                                {
                                    calc: function(dataTable, rowNum) {
                                        return dataTable.getValue(rowNum, 3) + '%'; // Ajustado para nova coluna 3
                                    },
                                    sourceColumn: 3, // Ajustado para nova coluna 3
                                    type: "string",
                                    role: "annotation"
                                }
                            ]);

                            var options1 = {
                                seriesType: 'bars',
                                series: {
                                    0: { // Série 0: Barras (Média de risco por empresa)
                                        targetAxisIndex: 0,
                                        color: '#4285F4', /* Cor azul padrão do Google Charts, como na imagem */
                                        annotations: {
                                            textStyle: {
                                                fontSize: 12,
                                                color: '#000', // Anotações das barras em preto
                                                auraColor: 'none'
                                            },
                                            // Posiciona a anotação na base da barra
                                            position: 'bottom',
                                            // O offset pode precisar de ajuste fino dependendo do tamanho das barras e fontes
                                            // Um valor negativo move para cima, positivo para baixo
                                            stem: { length: 0, vAlign: 'bottom' }
                                        }
                                    },
                                    1: { // Série 1: Linha (% Formulários respondidos)
                                        type: 'line',
                                        targetAxisIndex: 1,
                                        pointShape: 'circle', /* Pontos visíveis na linha */
                                        pointSize: 7, /* Tamanho dos pontos */
                                        lineWidth: 2,
                                        color: '#ea4335', /* Cor da linha é VERMELHA */
                                        annotations: {
                                            stem: { length: 0 },
                                            textStyle: {
                                                fontSize: 12,
                                                color: '#ea4335', /* Anotações dos percentuais em VERMELHO */
                                                auraColor: 'none'
                                            },
                                            alwaysOutside: true
                                        }
                                    }
                                },
                                vAxes: {
                                    0: { // Eixo Y esquerdo: Média de risco
                                        title: 'Média de risco', /* */
                                        minValue: 0,
                                        // maxValue: 10,
                                        format: '#',
                                        gridlines: { count: 5 },
                                        viewWindow: { min: 0 } //, max: 10 }
                                    },
                                    1: { // Eixo Y direito: % Respondidos
                                        title: '% Respondidos', /* */
                                        minValue: 0,
                                        maxValue: 100,
                                        format: '#\'%\'',
                                        gridlines: { count: 5 }, /* Linhas de grade também para o eixo secundário */
                                        viewWindow: { min: 0, max: 100 }
                                    }
                                },
                                legend: { position: 'bottom' }, /* */
                                chartArea: {left: '10%', top: '10%', right: '10%', bottom: '20%'},
                                bar: { groupWidth: '60%' },
                                tooltip: { isHtml: true } // MUITO IMPORTANTE: Habilita tooltips HTML para formatar o conteúdo
                            };

                            var chart1 = new google.visualization.ComboChart(document.getElementById('chart_evolucao_empresa_chart_area'));
                            chart1.draw(view1, options1);
                        }
                        document.getElementById("img-loading-evolucao").style.display = 'none';
                    },
                    error:function(erro){
                        document.getElementById("img-loading-evolucao").style.display = 'none';
                    }
                })
            }
        }

        // --- Funções para a Div Departamento (Empresa, Campanha) ---

        async function populateDepartmentSelectsAndDrawChart() {
            const companySelect = document.getElementById('selectEmpresaDepartamento');
            const campaignSelect = document.getElementById('selectCampanhaDepartamento');
            const spinner = document.getElementById('spinner_departamento');

            companySelect.innerHTML = '<option value="">Carregando Empresas...</option>';
            campaignSelect.innerHTML = '<option value="">Selecione uma Empresa</option>';
            campaignSelect.disabled = true;
            spinner.style.display = 'block';

            try {
                const companies = await fetchCompanies();
                companySelect.innerHTML = '<option value="">Selecione a Empresa</option>';
                companies.forEach(company => {
                    const option = document.createElement('option');
                    option.value = company.id;
                    option.textContent = company.name;
                    companySelect.appendChild(option);
                });

                if (companies.length > 0) {
                    companySelect.value = companies[0].id; // Seleciona a primeira por padrão
                    await handleDepartmentCompanyChange(); // Carrega campanhas e desenha
                } else {
                    drawDepartamento([]); // Desenha gráfico vazio
                }

            } catch (error) {
                console.error('Erro ao carregar empresas para Departamento:', error);
                companySelect.innerHTML = '<option value="">Erro ao carregar</option>';
                drawDepartamento([]);
            } finally {
                 spinner.style.display = 'none';
            }
        }

        async function handleDepartmentCompanyChange() {
            const companySelect = document.getElementById('selectEmpresaDepartamento');
            const campaignSelect = document.getElementById('selectCampanhaDepartamento');
            const selectedCompanyId = companySelect.value;
            const spinner = document.getElementById('spinner_departamento');

            campaignSelect.innerHTML = '<option value="">Carregando Campanhas...</option>';
            campaignSelect.disabled = true;
            spinner.style.display = 'block';

            if (!selectedCompanyId) {
                campaignSelect.innerHTML = '<option value="">Selecione uma Empresa</option>';
                drawDepartamento([]);
                spinner.style.display = 'none';
                return;
            }

            try {
                const campaigns = await fetchCampaigns(selectedCompanyId); // Chama a rota de campanhas
                campaignSelect.innerHTML = '';
                if (campaigns.length === 0) {
                    campaignSelect.innerHTML = '<option value="">Nenhuma Campanha Encontrada</option>';
                    campaignSelect.disabled = true;
                    drawDepartamento([]);
                    return;
                }

                campaigns.forEach(campaign => {
                    const option = document.createElement('option');
                    option.value = campaign.id;
                    option.textContent = campaign.name;
                    campaignSelect.appendChild(option);
                });
                campaignSelect.disabled = false;

                // Agora que as campanhas foram carregadas, desenhe o gráfico
                drawDepartmentChartWithSelectedData();

            } catch (error) {
                console.error('Erro ao carregar campanhas para Departamento:', error);
                campaignSelect.innerHTML = '<option value="">Erro ao carregar</option>';
                campaignSelect.disabled = true;
                drawDepartamento([]);
            } finally {
                spinner.style.display = 'none';
            }
        }

        async function drawDepartmentChartWithSelectedData() {
            const companySelect = document.getElementById('selectEmpresaDepartamento');
            const campaignSelect = document.getElementById('selectCampanhaDepartamento');
            const selectedCompanyId = companySelect.value;
            const selectedCampaignId = campaignSelect.value;

            const chartAreaDiv = document.getElementById('chart_departamento_chart_area');
            const spinner = document.getElementById('spinner_departamento');

            spinner.style.display = 'block';
            chartAreaDiv.style.opacity = '0.5';

            if (!selectedCompanyId || !selectedCampaignId) {
                drawDepartamento([]);
                spinner.style.display = 'none';
                chartAreaDiv.style.opacity = '1';
                return;
            }

            try {
                const dataFromAjax = await fetchDepartmentChartData(selectedCompanyId, selectedCampaignId);
                drawDepartamento(dataFromAjax, selectedCompanyId, selectedCampaignId);
            } catch (error) {
                console.error('Erro ao carregar dados do departamento:', error);
                drawDepartamento([]);
            } finally {
                spinner.style.display = 'none';
                chartAreaDiv.style.opacity = '1';
            }
        }

        function drawDepartamento(dataArray, companyId = '', campaignId = '') {
            let data3;
            if (dataArray && dataArray.length > 1) { // Verifica se há mais do que apenas os cabeçalhos
                data3 = google.visualization.arrayToDataTable(dataArray);
            } else {
                data3 = google.visualization.arrayToDataTable([
                    ['Departamento', 'Média de risco por departamento', '% Formulários respondidos'],
                    ['Nenhum dado disponível', 0, 0]
                ]);
            }

            let titleText = 'Departamento';
            if (companyId && campaignId) {
                const companyName = mockCompanies.find(c => c.id === companyId)?.name || companyId;
                const campaignName = mockCampaigns[companyId]?.find(c => c.id === campaignId)?.name || campaignId;
                titleText = `Departamento: ${companyName} - ${campaignName}`;
            }

            var view1 = new google.visualization.DataView(data3);
            view1.setColumns([
                0, // Coluna da Empresa (eixo X)
                1, // Coluna da Média de risco por empresa (dados das barras)
                {
                    calc: "stringify",
                    sourceColumn: 1,
                    type: "string",
                    role: "annotation"
                },
                2, // Coluna de % Formulários respondidos (dados da linha)
                {
                    calc: function(dataTable, rowNum) {
                        return dataTable.getValue(rowNum, 2) + '%';
                    },
                    sourceColumn: 2,
                    type: "string",
                    role: "annotation"
                }
            ]);

            var options1 = {
                //title: 'Evolução por empresa', /* */
                //titleTextStyle: { fontSize: 16, bold: true }, /* */
                seriesType: 'bars',
                series: {
                    0: { // Série 0: Barras (Média de risco por empresa)
                        targetAxisIndex: 0,
                        color: '#34A853', /* Cor azul padrão do Google Charts, como na imagem */
                        /* colors: ['#34A853', '#EA4335'], */
                        annotations: {
                            textStyle: {
                                fontSize: 12,
                                color: '#000', // Anotações das barras em preto
                                auraColor: 'none'
                            },
                            position: 'bottom',
                            // Removido 'alwaysOutside: true' para que a anotação apareça dentro da barra
                            // Adicionado 'highContrast: true' para legibilidade
                            highContrast: true,
                            stem: { length: 0, vAlign: 'bottom' } // <<< LINHA ALTERADA: Alinha verticalmente no pé
                        }
                    },
                    1: { // Série 1: Linha (% Formulários respondidos)
                        type: 'line',
                        targetAxisIndex: 1,
                        pointShape: 'circle', /* Pontos visíveis na linha */
                        pointSize: 7, /* Tamanho dos pontos */
                        lineWidth: 2,
                        color: '#ea4335', /* Cor da linha é VERMELHA */
                        annotations: {
                             stem: { length: 0 },
                             textStyle: {
                                fontSize: 12,
                                color: '#ea4335', /* Anotações dos percentuais em VERMELHO */
                                auraColor: 'none'
                             },
                             alwaysOutside: true
                        }
                    }
                },
                vAxes: {
                    0: { // Eixo Y esquerdo: Média de risco
                        title: 'Média de risco', /* */
                        minValue: 0,
                        //maxValue: 10,
                        format: '#',
                        gridlines: { count: 5 },
                        viewWindow: { min: 0 /*, max: 10*/ }
                    },
                    1: { // Eixo Y direito: % Respondidos
                        title: '% Respondidos', /* */
                        minValue: 0,
                        maxValue: 100,
                        format: '#\'%\'',
                        gridlines: { count: 5 }, /* Linhas de grade também para o eixo secundário */
                        viewWindow: { min: 0, max: 100 }
                    }
                },
                legend: { position: 'bottom' }, /* */
                chartArea: {left: '10%', top: '10%', right: '10%', bottom: '20%'},
                bar: { groupWidth: '60%' }
            };

            var chart1 = new google.visualization.ComboChart(document.getElementById('chart_departamento_chart_area'));
            chart1.draw(view1, options1);
        }

        // --- Funções para a Div Média de Risco por Dimensão (Empresa, Campanha, Departamento) ---

        async function populateDimensionRiskSelectsAndDrawChart() {
            const companySelect = document.getElementById('selectEmpresaRiscoDimensao');
            const campaignSelect = document = document.getElementById('selectCampanhaRiscoDimensao');
            const departmentSelect = document.getElementById('selectDepartamentoRiscoDimensao');
            const spinner = document.getElementById('spinner_risco_dimensao');

            // Limpa e desabilita todos os selects
            companySelect.innerHTML = '<option value="">Carregando Empresas...</option>';
            campaignSelect.innerHTML = '<option value="">Selecione a Empresa</option>';
            campaignSelect.disabled = true;
            departmentSelect.innerHTML = '<option value="">Selecione a Campanha</option>';
            departmentSelect.disabled = true;
            spinner.style.display = 'block';

            try {
                const companies = await fetchCompanies();
                companySelect.innerHTML = '<option value="">Selecione a Empresa</option>';
                companies.forEach(company => {
                    const option = document.createElement('option');
                    option.value = company.id;
                    option.textContent = company.name;
                    companySelect.appendChild(option);
                });

                if (companies.length > 0) {
                    companySelect.value = companies[0].id; // Seleciona a primeira por padrão
                    await handleDimensionRiskCompanyChange(); // Carrega campanhas e departamentos e desenha
                } else {
                    drawRiscoDimensao([]); // Desenha gráfico vazio
                }

            } catch (error) {
                console.error('Erro ao carregar empresas para Risco por Dimensão:', error);
                companySelect.innerHTML = '<option value="">Erro ao carregar</option>';
                drawRiscoDimensao([]);
            } finally {
                spinner.style.display = 'none';
            }
        }

        async function handleDimensionRiskCompanyChange() {
            const companySelect = document.getElementById('selectEmpresaRiscoDimensao');
            const campaignSelect = document.getElementById('selectCampanhaRiscoDimensao');
            const departmentSelect = document.getElementById('selectDepartamentoRiscoDimensao');
            const selectedCompanyId = companySelect.value;
            const spinner = document.getElementById('spinner_risco_dimensao');

            campaignSelect.innerHTML = '<option value="">Carregando Campanhas...</option>';
            campaignSelect.disabled = true;
            departmentSelect.innerHTML = '<option value="">Selecione a Campanha</option>';
            departmentSelect.disabled = true;
            spinner.style.display = 'block';

            if (!selectedCompanyId) {
                campaignSelect.innerHTML = '<option value="">Selecione a Empresa</option>';
                departmentSelect.innerHTML = '<option value="">Selecione a Campanha</option>';
                drawRiscoDimensao([]);
                spinner.style.display = 'none';
                return;
            }

            try {
                const campaigns = await fetchCampaigns(selectedCompanyId); // Chama a rota de campanhas
                campaignSelect.innerHTML = '';
                if (campaigns.length === 0) {
                    campaignSelect.innerHTML = '<option value="">Nenhuma Campanha Encontrada</option>';
                    campaignSelect.disabled = true;
                    departmentSelect.innerHTML = '<option value="">Nenhum Departamento</option>';
                    departmentSelect.disabled = true;
                    drawRiscoDimensao([]);
                    return;
                }

                campaigns.forEach(campaign => {
                    const option = document.createElement('option');
                    option.value = campaign.id;
                    option.textContent = campaign.name;
                    campaignSelect.appendChild(option);
                });
                campaignSelect.disabled = false;

                // Carrega os departamentos para a primeira campanha e desenha o gráfico
                await handleDimensionRiskCampaignChange(); // Chama a próxima etapa para carregar departamentos e o gráfico

            } catch (error) {
                console.error('Erro ao carregar campanhas para Risco por Dimensão:', error);
                campaignSelect.innerHTML = '<option value="">Erro ao carregar</option>';
                campaignSelect.disabled = true;
                departmentSelect.innerHTML = '<option value="">Erro</option>';
                departmentSelect.disabled = true;
                drawRiscoDimensao([]);
            } finally {
                spinner.style.display = 'none';
            }
        }

        async function handleDimensionRiskCampaignChange() {
            const campaignSelect = document.getElementById('selectCampanhaRiscoDimensao');
            const departmentSelect = document.getElementById('selectDepartamentoRiscoDimensao');
            const selectedCampaignId = campaignSelect.value;
            const spinner = document.getElementById('spinner_risco_dimensao');

            departmentSelect.innerHTML = '<option value="">Carregando Departamentos...</option>';
            departmentSelect.disabled = true;
            spinner.style.display = 'block';

            if (!selectedCampaignId) {
                departmentSelect.innerHTML = '<option value="">Selecione a Campanha</option>';
                drawRiscoDimensao([]);
                spinner.style.display = 'none';
                return;
            }

            try {
                const departments = await fetchDepartments(selectedCampaignId); // Chama a rota de departamentos
                departmentSelect.innerHTML = '';
                if (departments.length === 0) {
                    departmentSelect.innerHTML = '<option value="">Nenhum Departamento Encontrado</option>';
                    departmentSelect.disabled = true;
                    drawRiscoDimensao([]);
                    return;
                }

                departments.forEach(department => {
                    const option = document.createElement('option');
                    option.value = department.id;
                    option.textContent = department.name;
                    departmentSelect.appendChild(option);
                });
                departmentSelect.disabled = false;

                // Agora que os departamentos foram carregados, desenhe o gráfico
                drawDimensionRiskChartWithSelectedData();

            } catch (error) {
                console.error('Erro ao carregar departamentos para Risco por Dimensão:', error);
                departmentSelect.innerHTML = '<option value="">Erro ao carregar</option>';
                departmentSelect.disabled = true;
                drawRiscoDimensao([]);
            } finally {
                spinner.style.display = 'none';
            }
        }

        async function drawDimensionRiskChartWithSelectedData() {
            const companySelect = document.getElementById('selectEmpresaRiscoDimensao');
            const campaignSelect = document.getElementById('selectCampanhaRiscoDimensao');
            const departmentSelect = document.getElementById('selectDepartamentoRiscoDimensao');

            const selectedCompanyId = companySelect.value;
            const selectedCampaignId = campaignSelect.value;
            const selectedDepartmentId = departmentSelect.value;

            const chartAreaDiv = document.getElementById('chart_risco_dimensao_chart_area');
            const spinner = document.getElementById('spinner_risco_dimensao');

            spinner.style.display = 'block';
            chartAreaDiv.style.opacity = '0.5';

            if (!selectedCompanyId || !selectedCampaignId || !selectedDepartmentId) {
                drawRiscoDimensao([]);
                spinner.style.display = 'none';
                chartAreaDiv.style.opacity = '1';
                return;
            }

            try {
                const dataFromAjax = await fetchDimensionRiskChartData(selectedCompanyId, selectedCampaignId, selectedDepartmentId);
                drawRiscoDimensao(dataFromAjax, selectedCompanyId, selectedCampaignId, selectedDepartmentId);
            } catch (error) {
                console.error('Erro ao carregar dados de risco por dimensão:', error);
                drawRiscoDimensao([]);
            } finally {
                spinner.style.display = 'none';
                chartAreaDiv.style.opacity = '1';
            }
        }

        function drawRiscoDimensao(dataArray, companyId = '', campaignId = '', departmentId = '') {
            let data4;
            if (dataArray && dataArray.length > 1) { // Verifica se há mais do que apenas os cabeçalhos
                data4 = google.visualization.arrayToDataTable(dataArray);
            } else {
                data4 = google.visualization.arrayToDataTable([
                    ['Dimensão', 'Média de risco', { role: 'annotation' }],
                    ['Nenhum dado disponível', 0, 0]
                ]);
            }

            let titleText = 'Média de risco por dimensão';
            if (companyId && campaignId && departmentId) {
                const companyName = mockCompanies.find(c => c.id === companyId)?.name || companyId;
                const campaignName = mockCampaigns[companyId]?.find(c => c.id === campaignId)?.name || campaignId;
                const departmentName = mockDepartments[campaignId]?.find(d => d.id === departmentId)?.name || departmentId;
                titleText = `Risco por Dimensão: ${companyName} - ${campaignName} - ${departmentName}`;
            }

            var options4 = {
                chartArea: {left: '30%', top: '10%', right: '10%', bottom: '20%'},
                hAxis: {
                    title: 'Média de risco',
                    minValue: 0,
                    maxValue: 10
                },
                bar: { groupWidth: '80%' },
                legend: { position: 'none' },
                colors: ['#D896FF'] /* Cor roxa */
            };

            var chart4 = new google.visualization.BarChart(document.getElementById('chart_risco_dimensao_chart_area'));
            chart4.draw(data4, options4);
        }
    </script>

@endsection


