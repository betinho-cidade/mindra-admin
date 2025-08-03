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
                            </form>
                        </div>
                    </div>
                    <div class="loading-spinner" id="spinner_evolucao"></div>
                    <div id="chart_evolucao_empresa_chart_area" class="chart-area"></div>
                </div>

                <div id="chart_departamento" class="chart-div">
                    <div class="chart-header-and-filters">
                        <h2>Departamento</h2>
                    </div>
                    <div class="filter-row">
                        <form id="form_departamento">
                            @csrf                        
                            <div class="form-group">
                                <select id="selectEmpresaDepartamento" onchange="handleDepartmentCompanyChange()" class="filter-select">
                                    @foreach ($empresas as $empresa)
                                        <option value="{{ $empresa->id }}">{{ $empresa->nome }}</option>
                                    @endforeach
                                </select>                            

                                <select id="selectCampanhaDepartamento" onchange="drawDepartmentChartWithSelectedData()" disabled class="filter-select">
                                    <option value="">Selecione uma Empresa</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="loading-spinner" id="spinner_departamento"></div>
                    <div id="chart_departamento_chart_area" class="chart-area"></div>
                </div>

                <div id="chart_risco_dimensao" class="chart-div">
                    <div class="chart-header-and-filters">
                        <h2>Médio do Risco por Dimensão <span id="title-media-risco"></span></h2>
                    </div>
                    <div class="filter-row">
                        <form id="form_departamento">
                            @csrf                                             
                            <div class="form-group">
                                <select id="selectEmpresaRiscoDimensao" onchange="handleDimensionRiskCompanyChange()" class="filter-select">
                                    @foreach ($empresas as $empresa)
                                        <option value="{{ $empresa->id }}">{{ $empresa->nome }}</option>
                                    @endforeach
                                </select>

                                <select id="selectCampanhaRiscoDimensao" onchange="handleDimensionRiskCampaignChange()" disabled class="filter-select">
                                    <option value="">Selecione a Empresa</option>
                                </select>

                                <select id="selectDepartamentoRiscoDimensao" onchange="drawDimensionRiskChartWithSelectedData()" disabled class="filter-select"> <!-- <<< CLASSE ADICIONADA -->
                                    <option value="">Selecione a Campanha</option>
                                </select>
                            </div>
                        </form>
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
            font-size: 0.7em; /* Reduz a fonte dos selects */
            max-width: 150px; /* Limita a largura do select para caber mais na linha */
            margin-right: 10px;
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

        async function fetchCampaigns(empresaId) {
            const url = `{{route('dashboard.js_busca_campanhas', ['empresa' => 'PLACEHOLDER_EMPRESA_ID'])}}`.replace('PLACEHOLDER_EMPRESA_ID', empresaId);
            const response = await fetch(url);
            return await response.json();    
        }

        async function fetchDepartments(empresaId, campanhaId) {
            const url = `{!! route('dashboard.js_busca_departamentos', ['empresa' => 'PLACEHOLDER_EMPRESA_ID', 'campanha' => 'PLACEHOLDER_CAMPANHA_ID']) !!}`.replace('PLACEHOLDER_EMPRESA_ID', empresaId).replace('PLACEHOLDER_CAMPANHA_ID', campanhaId);
            const response = await fetch(url);
            return await response.json();   
        }

        async function fetchDepartmentChartData(empresaId, campanhaId) {
            var url = `{!! route('dashboard.js_departamento', ['empresa' => 'PLACEHOLDER_EMPRESA_ID', 'campanha' => 'PLACEHOLDER_CAMPANHA_ID']) !!}`.replace('PLACEHOLDER_EMPRESA_ID', empresaId).replace('PLACEHOLDER_CAMPANHA_ID', campanhaId);
            const response = await fetch(url);
            return await response.json();               
        }

        async function fetchDimensionRiskChartData(empresaId, campanhaId, departamentoText) {
            const url = `{!! route('dashboard.js_risco', ['empresa' => 'PLACEHOLDER_EMPRESA_ID', 'campanha' => 'PLACEHOLDER_CAMPANHA_ID', 'departamento' => 'PLACEHOLDER_DEPARTAMENTO_TEXT']) !!}`.replace('PLACEHOLDER_EMPRESA_ID', empresaId).replace('PLACEHOLDER_CAMPANHA_ID', campanhaId).replace('PLACEHOLDER_DEPARTAMENTO_TEXT', departamentoText);
            const response = await fetch(url);
            return await response.json();   
        }

        // --- Funções de Desenho dos Gráficos ---

        function drawAllCharts() {
            drawResultadoEmpresa();
            drawEvolucaoEmpresa();
            //populateDepartmentSelectsAndDrawChart(); // Inicia o processo para Departamento (Empresa, Campanha)
            handleDepartmentCompanyChange();
            //populateDimensionRiskSelectsAndDrawChart(); // Inicia o processo para Média de Risco por Dimensão (Empresa, Campanha, Departamento)
            handleDimensionRiskCompanyChange();
        }

        // -- EMPRESA
        function drawResultadoEmpresa() {

            @if(!$dash_empresa)
                const chartArea = document.getElementById('chart_resultado_empresa_chart_area');
                chartArea.innerHTML = "<p style='text-align:center; padding-top:50px;'>Dados não disponíveis</p>";
                document.getElementById("img-loading-evolucao").style.display = 'none';
                return;
            @endif

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
                        color: '#1E6E8C', /* Cor azul padrão do Google Charts, como na imagem */
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
                        // title: 'Média de risco', /* */
                        textPosition: 'none',
                        minValue: 0,
                        // maxValue: 10,
                        format: '#',
                        gridlines: { count: 5, color: 'none' },
                        viewWindow: { min: 0 } //, max: 10 }
                    },
                    1: { // Eixo Y direito: % Respondidos
                        // title: '% Respondidos', /* */
                        textPosition: 'none',
                        minValue: 0,
                        maxValue: 100,
                        format: '#\'%\'',
                        gridlines: { count: 5, color: 'none' }, /* Linhas de grade também para o eixo secundário */
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

        // -- EVOLUÇÃO EMPRESA
        function drawEvolucaoEmpresa() {
            const selectedEmpresa = document.getElementById('selectEmpresaEvolucao').value;
            let dados, dataEvolucao;

            if (selectedEmpresa != ''){
                const chartArea = document.getElementById('chart_evolucao_empresa_chart_area');
                const spinner = document.getElementById('spinner_evolucao');

                spinner.style.display = 'block';
                chartArea.style.opacity = '0.5';

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
                            spinner.style.display = 'none';
                            chartArea.style.opacity = '1';
                            return;
                        } else {
                            dataEvolucao = [['Empresa', 'Média de risco por mês', { role: 'tooltip', type: 'string', p: { html: true } }, '% Formulários respondidos']];
                            dados.forEach(item => {
                                // Cria o conteúdo HTML personalizado para o tooltip
                                const tooltipHtml = `<div style='padding:5px; font-size:14px;'><span style='font-size:14px'>${item.mes}</font><br><b>${item.campanha}</b></div>`;
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
                                        color: '#F7A984', /* Cor azul padrão do Google Charts, como na imagem */
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
                                        //title: 'Média de risco', /* */
                                        textPosition: 'none',
                                        minValue: 0,
                                        // maxValue: 10,
                                        format: '#',
                                        gridlines: { count: 5, color: 'none' },
                                        viewWindow: { min: 0 } //, max: 10 }
                                    },
                                    1: { // Eixo Y direito: % Respondidos
                                        //title: '% Respondidos', /* */
                                        textPosition: 'none',
                                        minValue: 0,
                                        maxValue: 100,
                                        format: '#\'%\'',
                                        gridlines: { count: 5, color: 'none' }, /* Linhas de grade também para o eixo secundário */
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
                        spinner.style.display = 'none';
                        chartArea.style.opacity = '1';
                    },
                    error:function(erro){
                        spinner.style.display = 'none';
                        chartArea.style.opacity = '1';
                    }
                })
            }
        }
        
        // -- DEPARTAMENTO
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
                    option.textContent = campaign.data_inicio;
                    campaignSelect.appendChild(option);
                });
                campaignSelect.disabled = false;

                // Agora que as campanhas foram carregadas, desenhe o gráfico
                drawDepartmentChartWithSelectedData();

            } catch (error) {
                console.log('Erro ao carregar campanhas para Departamento:', error);
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

            if (companyId != '' && campaignId != ''){
                const chartArea = document.getElementById('chart_departamento_chart_area');
                const spinner = document.getElementById('spinner_departamento');

                spinner.style.display = 'block';
                chartArea.style.opacity = '0.5';

                if(dataArray == '' || dataArray==null || dataArray['error'] == 'true'){
                    chartArea.innerHTML = "<p style='text-align:center; padding-top:50px;'>Dados não disponíveis para esta campanha.</p>";
                    spinner.style.display = 'none';
                    chartArea.style.opacity = '1';
                    return;
                } else {
                    dataDepartamento = [['Empresa', 'Média de risco por departamento', { role: 'tooltip', type: 'string', p: { html: true } }, '% Formulários respondidos']];
                    dataArray.forEach(item => {
                        // Cria o conteúdo HTML personalizado para o tooltip
                        const tooltipHtml = `<div style='padding:5px; font-size:14px;'><span style='font-size:14px'>${item.mes}</font><br><b>${item.campanha}</b></div>`;
                        // Adiciona a linha formatada ao dataArray
                        dataDepartamento.push([
                            item.departamento, // Nome da empresa/campanha
                            item.risco_medio, // Média de risco
                            tooltipHtml,
                            item.percentual_respondido // Percentual de formulários respondidos
                        ]);
                    });

                    var data1 = google.visualization.arrayToDataTable(dataDepartamento);

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
                                color: '#669933', /* Cor azul padrão do Google Charts, como na imagem */
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
                                //title: 'Média de risco', /* */
                                textPosition: 'none',
                                minValue: 0,
                                // maxValue: 10,
                                format: '#',
                                gridlines: { count: 5, color: 'none' },
                                viewWindow: { min: 0 } //, max: 10 }
                            },
                            1: { // Eixo Y direito: % Respondidos
                                //title: '% Respondidos', /* */
                                textPosition: 'none',
                                minValue: 0,
                                maxValue: 100,
                                format: '#\'%\'',
                                gridlines: { count: 5, color: 'none' }, /* Linhas de grade também para o eixo secundário */
                                viewWindow: { min: 0, max: 100 }
                            }
                        },
                        legend: { position: 'bottom' }, /* */
                        chartArea: {left: '10%', top: '10%', right: '10%', bottom: '20%'},
                        bar: { groupWidth: '60%' },
                        tooltip: { isHtml: true } // MUITO IMPORTANTE: Habilita tooltips HTML para formatar o conteúdo
                    };

                    var chart1 = new google.visualization.ComboChart(document.getElementById('chart_departamento_chart_area'));
                    chart1.draw(view1, options1);
                }
                spinner.style.display = 'none';
                chartArea.style.opacity = '1';
            }
        }

        // -- RISCO
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
                    option.textContent = campaign.data_inicio;
                    campaignSelect.appendChild(option);
                });
                campaignSelect.disabled = false;

                // Carrega os departamentos para a primeira campanha e desenha o gráfico
                await handleDimensionRiskCampaignChange(); // Chama a próxima etapa para carregar departamentos e o gráfico

            } catch (error) {
                //console.error('Erro ao carregar campanhas para Risco por Dimensão:', error);
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
            const companySelect = document.getElementById('selectEmpresaRiscoDimensao');
            const campaignSelect = document.getElementById('selectCampanhaRiscoDimensao');
            const departmentSelect = document.getElementById('selectDepartamentoRiscoDimensao');
            const selectedCompanyId = companySelect.value;
            const selectedCampaignId = campaignSelect.value;
            const spinner = document.getElementById('spinner_risco_dimensao');

            departmentSelect.innerHTML = '<option value="">Carregando Departamentos...</option>';
            departmentSelect.disabled = true;
            spinner.style.display = 'block';

            if (!selectedCompanyId || !selectedCampaignId) {
                departmentSelect.innerHTML = '<option value="">Selecione a Campanha</option>';
                drawRiscoDimensao([]);
                spinner.style.display = 'none';
                return;
            }

            try {
                const departments = await fetchDepartments(selectedCompanyId, selectedCampaignId); // Chama a rota de departamentos
                departmentSelect.innerHTML = '';
                if (departments.length === 0) {
                    departmentSelect.innerHTML = '<option value="">Nenhum Departamento Encontrado</option>';
                    departmentSelect.disabled = true;
                    drawRiscoDimensao([]);
                    return;
                }

                departments.forEach(department => {
                    const option = document.createElement('option');
                    option.value = department.departamento;
                    option.textContent = department.departamento;
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
            const selectedDepartmentText = departmentSelect.value;

            const chartAreaDiv = document.getElementById('chart_risco_dimensao_chart_area');
            const spinner = document.getElementById('spinner_risco_dimensao');

            spinner.style.display = 'block';
            chartAreaDiv.style.opacity = '0.5';

            if (!selectedCompanyId || !selectedCampaignId) {
                drawRiscoDimensao([]);
                spinner.style.display = 'none';
                chartAreaDiv.style.opacity = '1';
                return;
            }

            try {
                const dataFromAjax = await fetchDimensionRiskChartData(selectedCompanyId, selectedCampaignId, selectedDepartmentText);
                drawRiscoDimensao(dataFromAjax, selectedCompanyId, selectedCampaignId, selectedDepartmentText);
            } catch (error) {
                console.error('Erro ao carregar dados de risco por dimensão:', error);
                drawRiscoDimensao([]);
            } finally {
                spinner.style.display = 'none';
                chartAreaDiv.style.opacity = '1';
            }
        }

        function drawRiscoDimensao(dataArray, companyId = '', campaignId = '', departmentText = '') {

            if (companyId != '' && campaignId != ''){
                const chartArea = document.getElementById('chart_risco_dimensao_chart_area');
                const spinner = document.getElementById('spinner_risco_dimensao');
                const tituloRisco = document.getElementById('title-media-risco');

                spinner.style.display = 'block';
                chartArea.style.opacity = '0.5';

                if(dataArray == '' || dataArray==null || dataArray['error'] == 'true'){
                    chartArea.innerHTML = "<p style='text-align:center; padding-top:50px;'>Dados não disponíveis para este departamento.</p>";
                    spinner.style.display = 'none';
                    chartArea.style.opacity = '1';
                    return;
                } else {
                    tituloRisco.innerHTML = (departmentText) ? ' - ' + departmentText : '';
                    let dataDimensao = [];
                    dataDimensao.push([
                                        'Dimensão',
                                        'Média de Risco',
                                        //{ role: 'tooltip', type: 'string', p: { html: true } }, // Para tooltips HTML personalizados
                                        { role: 'style' },
                                        { role: 'annotation' } // Para o valor dentro da barra
                                    ]);

                    dataArray.forEach(item => {
                        // Cria o conteúdo HTML personalizado para o tooltip
                        //const tooltipHtml = `<div style='padding:5px; font-size:14px;'><span style='font-size:12px'>${item.mes}</font><br><b>${item.campanha}</b></div>`;

                        // Adiciona a linha formatada ao dataDimensao
                        dataDimensao.push([
                            item.titulo_etapa, // Nome da dimensão/etapa
                            item.risco_medio, // Média de risco (valor numérico)
                            //tooltipHtml, // Conteúdo do tooltip
                            'color: #EE82EE', // Cor da barra
                            item.risco_medio.toString() // Valor para anotação dentro da barra (converte para string)
                        ]);
                    });

                    var data4 = google.visualization.arrayToDataTable(dataDimensao);

                    var options4 = {
                            //title: 'Média de risco por dimensão',
                            hAxis: {
                                minValue: 0,
                                textPosition: 'none',
                                gridlines: { color: 'none' },
                                textStyle: {
                                    color: '#333'
                                }
                            },
                            vAxis: {
                                title: '',
                                textStyle: {
                                    color: '#333'
                                }
                            },
                            legend: {
                                position: 'none'
                            },
                            chartArea: {
                                left: 150,
                                top: 20,
                                width: '70%',
                                height: '70%'
                            },
                            bars: 'horizontal',
                            series: {
                                0: { color: '#EE82EE' }
                            },
                            annotations: {
                                // Esta é a chave! Define para mostrar as anotações DENTRO das barras
                                alwaysOutside: false,
                                textStyle: {
                                    fontSize: 12,
                                    // Cor do texto da anotação. Pode ajustar para contrastar com a barra
                                    color: '#000' // Preto para melhor visibilidade
                                }
                            }
                        };

                        var chart = new google.visualization.BarChart(document.getElementById('chart_risco_dimensao_chart_area'));
                        chart.draw(data4, options4);                    

                        // var data4 = google.visualization.arrayToDataTable(dataDimensao);

                        // var options4 = {
                        //     chartArea: {left: '30%', top: '10%', right: '10%', bottom: '20%'},
                        //     hAxis: {
                        //         title: 'Média de risco',
                        //         minValue: 0,
                        //         //maxValue: 10
                        //     },
                        //     bar: { groupWidth: '80%' },
                        //     legend: { position: 'none' },
                        //     colors: ['#D896FF'] /* Cor roxa */
                        // };

                        // var chart4 = new google.visualization.BarChart(document.getElementById('chart_risco_dimensao_chart_area'));
                        // chart4.draw(data4, options4);
                    }
            }
        }

    </script>

@endsection


