@extends('painel.layout.index')


@section('content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0">Informações da Empresa</h4>
        </div>
    </div>
</div>

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

<small style="color: mediumpurple">{!! $empresa->breadcrumb_gestao !!}</small>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
            <!-- FORMULÁRIO - INICIO -->

            <h4 class="card-title">{{$empresa->nome}}</h4>

                <!-- Dados Pessoais - INI -->
                <div class="bg-soft-primary p-3 rounded" style="margin-bottom:10px;">
                    <h5 class="text-primary font-size-14" style="margin-bottom: 0px;">Dados Empresa</h5>
                </div>

                    <div class="row">
                        <div class="col-md-2">
                            <label for="cnpj">CNPJ</label>
                            <input type="text" class="form-control mask_cnpj" value="{{$empresa->cnpj}}" disabled>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="nome">Nome</label>
                                <input type="text" class="form-control" value="{{$empresa->nome}}" disabled>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="email">E-mail</label>
                                <input type="email" class="form-control" value="{{$empresa->email}}" disabled>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="telefone">Telefone</label>
                            <input type="text" class="form-control mask_telefone" value="{{$empresa->telefone}}" disabled>
                        </div>
                    </div>
                    <p></p>
                    <div class="row">
                        <div class="col-md-4">
                            <label for="responsavel_nome">Nome Responsável</label>
                            <input type="text" class="form-control" value="{{$empresa->responsavel_nome}}" disabled>
                        </div>
                        <div class="col-md-2">
                            <label for="responsavel_telefone">Telefone Responsável</label>
                            <input type="text" class="form-control mask_telefone" value="{{$empresa->responsavel_telefone}}" disabled>
                        </div>
                        <div class="col-md-2">
                            <label for="inscricao_estadual">Inscrição Estadual</label>
                            <input type="text" class="form-control" value="{{$empresa->inscricao_estadual}}" disabled>
                        </div>
                        <div class="col-md-2">
                            <label for="qtd_funcionario">Qtd Funcionários</label>
                            <input type="number" class="form-control" value="{{$empresa->qtd_funcionario}}" disabled>
                        </div>
                        <div class="col-md-2">
                            <label for="data_abertura">Data Abertura</label>
                            <input type="date" class="form-control" value="{{$empresa->data_abertura}}" disabled>
                        </div>
                    </div>
                    <p></p>
                    <div class="row">
                        <div class="col-md-5">
                            <label for="atividade_principal">Atividade Principal</label>
                            <textarea rows="1" class="form-control" disabled>{{$empresa->atividade_principal}}</textarea>
                        </div>
                        <div class="col-md-3">
                            <label for="site">Site</label>
                            <input type="url" class="form-control" value="{{$empresa->site}}" disabled>
                        </div>
                        <div class="col-md-2">
                            <label for="num_contrato">Número Contrato</label>
                            <input type="text" class="form-control" value="{{$empresa->num_contrato}}" disabled>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="situacao">Situação</label>
                                <select class="form-control" disabled>
                                    <option value="">---</option>
                                    <option value="A" {{($empresa->status == 'A') ? 'selected' : '' }}>Ativo</option>
                                    <option value="I" {{($empresa->status == 'I') ? 'selected' : '' }}>Inativo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <p></p>
                <!-- Dados Pessoais - FIM -->

                <!-- Dados Endereço - INI -->
                <div class="bg-soft-primary p-3 rounded" style="margin-bottom:10px;">
                    <h5 class="text-primary font-size-14" style="margin-bottom: 0px;">Dados Endereço</h5>
                </div>
                    <div class="row">
                        <div class="col-md-2">
                            <label for="end_cep">CEP</label>
                            <input type="text" class="form-control mask_cep" value="{{$empresa->end_cep}}" disabled>
                        </div>

                        <div class="col-md-4">
                            <label for="end_cidade">Cidade</label>
                            <input type="text" class="form-control" value="{{$empresa->end_cidade}}" disabled>
                        </div>

                        <div class="col-md-2">
                            <label for="end_uf">Estado</label>
                            <input type="text" class="form-control" value="{{$empresa->end_uf}}" disabled>
                        </div>

                        <div class="col-md-4">
                            <label for="end_bairro">Bairro</label>
                            <input type="text" class="form-control" value="{{$empresa->end_bairro}}" disabled>
                        </div>
                    </div>
                    <p></p>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="end_endereco">Endereço</label>
                            <input type="text" class="form-control" value="{{$empresa->end_logradouro}}" disabled>
                        </div>

                        <div class="col-md-2">
                            <label for="end_numero">Número</label>
                            <input type="text" value="{{$empresa->end_numero}}" class="form-control" disabled>
                        </div>

                        <div class="col-md-4">
                            <label for="end_complemento">Complemento </label>
                            <input type="text" class="form-control" value="{{$empresa->end_complemento}}" disabled>
                        </div>
                    </div>
                    <p></p>
                <!-- Dados Endereço - FIM -->

            <div class="bg-soft-primary p-3 rounded" style="margin-top:60px;margin-bottom:10px;">
                <h5 class="text-primary font-size-14" style="margin-bottom: 0px;">Funcionários da Empresa</h5>
            </div>

            <!-- Nav tabs - LISTA AULA/BANNER/AVALIAÇÃO - INI -->
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link @if($aba == '') active @endif" data-toggle="tab" href="#funcionarios" role="tab">
                        <span class="d-block d-sm-none"><i class="ri-checkbox-circle-line"></i></span>
                        <span class="d-none d-sm-block">
                            @can('invite_empresa_funcionario')
                                <i class="fa fa-envelope"  data-toggle="modal" onclick="inviteData('funcionario', '{{$empresa->id}}');"
                                    data-target="#modal-invite" style="color: goldenrod" title="Enviar e-mail em lote para ativação do Funcionário"></i>
                            @endcan
                            @can('create_empresa_funcionario')
                                <i onClick="location.href='{{ route('empresa_funcionario.create', compact('empresa')) }}';" class="fa fa-plus-square" style="color: goldenrod; margin-right:5px;" title="Novo Funcionário"></i>
                            @endcan
                            Funcionários ( <code class="highlighter-rouge">{{ $empresa_funcionarios->count() }}</code> )
                        </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if($aba == 'Imports') active @endif" data-toggle="tab" href="#imports" role="tab">
                        <span class="d-block d-sm-none"><i class="ri-checkbox-circle-line"></i></span>
                        <span class="d-none d-sm-block">
                            Importação de Funcionários
                        </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if($aba == 'Campanhas') active @endif" data-toggle="tab" href="#campanhas" role="tab">
                        <span class="d-block d-sm-none"><i class="ri-checkbox-circle-line"></i></span>
                        <span class="d-none d-sm-block">
                            @can('join_campanha_empresa')
                                <i onClick="location.href='{{ route('campanha_empresa.create', compact('empresa')) }}';" class="fa fa-plus-square" style="color: goldenrod; margin-right:5px;" title="Adicionar Campanha"></i>
                            @endcan
                            Campanhas ( <code class="highlighter-rouge">{{ $campanha_empresas->count() }}</code> )
                        </span>
                    </a>
                </li>
           </ul>
           @if($resultado_import && $resultado_import['log_file'])
           <span class="float-right resultado_importacao">
                <div class="row">
                    <div class="sucesso">Registros Sucesso: {{ $resultado_import['success_count'] }}</div>
                    <div class="erro">Registros Erro: {{ $resultado_import['errors_count'] }}</div>
                    <div class="log" ><a href="{{ $resultado_import['log_file'] }}"><i class="ri-download-2-line"></i></a></div>
                </div>
           </span>
           @endif
           @if($resultado_invite && $resultado_invite['log_file'])
           <span class="float-right resultado_importacao">
                <div class="row">
                    <div class="sucesso">Emails Sucesso: {{ $resultado_invite['success_count'] }}</div>
                    <div class="erro">Emails Erro: {{ $resultado_invite['errors_count'] }}</div>
                    <div class="log"><a href="{{ $resultado_invite['log_file'] }}"><i class="ri-download-2-line"></i></a></div>
                </div>
           </span>
           @endif
            <!-- Nav tabs - LISTA AULA/BANNER/AVALIAÇÃO - FIM -->

            <!-- Tab panes - INI -->
            <div class="tab-content p-3 text-muted">
                <!-- Nav tabs - LISTA AULA - INI -->
                <div class="tab-pane @if($aba == '') active @endif" id="funcionarios" role="tabpanel">
                    <table id="dt_funcionarios" class="table table-striped table-bordered dt-responsive nowrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>CPF</th>
                                <th>E-mail</th>
                                <th>Cargo</th>
                                <th style="text-align:center;">Ações</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($empresa_funcionarios as $empresa_funcionario)
                                <tr>
                                    <td>{{ $empresa_funcionario->id }}</td>
                                    <td class="icone_ativacao">
                                        @if($empresa_funcionario->status == 'A')
                                            <i class="fas fa-user" style="font-size:10px; color: rgb(8, 179, 16)" title="Ativo na Empresa"></i>&nbsp;
                                        @else
                                            <i class="fas fa-user-times" style="font-size:10px;color: rgb(218, 53, 53)" title="Inativo na Empresa"></i>&nbsp;
                                        @endif
                                        {{ $empresa_funcionario->funcionario->user->nome }}
                                    </td>
                                    <td class="mask_cpf">{{ $empresa_funcionario->funcionario->user->cpf }}</td>
                                    <td>{{ $empresa_funcionario->funcionario->user->email }}</td>
                                    <td>{{ $empresa_funcionario->cargo }}</td>
                                    <td style="text-align:center;">
                                        @can('edit_empresa_funcionario')
                                            <a href="{{ route('empresa_funcionario.show_funcionario', compact('empresa_funcionario')) }}"><i class="fa fa-edit" style="color: goldenrod" title="Editar Funcionário da Empresa"></i></a>
                                        @endcan

                                        @can('delete_empresa_funcionario')
                                            <a href="javascript:;" data-toggle="modal"
                                            onclick="deleteFuncionario('{{$empresa_funcionario->id}}');"
                                                data-target="#modal-delete-funcionario"><i class="fa fa-minus-circle"
                                                    style="color: crimson" title="Excluir o Funcionário da Empresa"></i></a>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">Nenhum registro encontrado</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <!-- Nav tabs - LISTA AULA - FIM -->
                </div>

                <div class="tab-pane @if($aba == 'Imports') active @endif" id="imports" role="tabpanel">

                    <div class="instructions">
                        <h4 style="margin: 10px 0 2px 0;">Instruções para Upload do Arquivo</h4>
                        <p>Para importar funcionários, siga os passos abaixo:</p>
                        <ul class="instrucao_upload">
                            <li>Prepare um arquivo no formato <strong>CSV</strong> ou <strong>TXT</strong> com os campos listados abaixo.</li>
                            <li><strong>Campos obrigatórios</strong>: nome, email, cpf, sexo, data_nascimento, telefone, cargo.</li>
                            <li><strong>Campos únicos</strong>: email e cpf (não podem estar duplicados no arquivo ou no banco de dados).</li>
                            <li><strong>Formato do arquivo</strong>: Utilize vírgula (,) como delimitador. A primeira linha deve conter o cabeçalho exato: <code>nome,email,cpf,rg,data_nascimento,telefone,sexo,end_cep,end_cidade,end_uf,end_logradouro,end_numero,end_bairro,end_complemento,matricula,cargo,departamento,data_admissao</code>.</li>
                            <li><strong>Exemplo de linha</strong>: <code>João Silva,joao@example.com,221.286.790-52,1234567,1990-01-01,11987654321,M,12345678,São Paulo,SP,Rua Exemplo,123,Centro,Apt 101,MAT123,Desenvolvedor,Tecnologia,2023-06-01</code></li>
                            <li>Campos opcionais podem ser deixados em branco (ex.: <code>,,</code>).</li>
                            <li>Tamanho máximo do arquivo: <strong>1MB</strong>.</li>
                            <li>Baixe o <a style="border: 1px solid #484848; padding: 0 7px; border-radius: 5px;" href="{{ asset('nazox/assets/files/funcionarios_modelo.csv') }}">modelo de arquivo CSV</a> para usar como referência.</li>
                        </ul>
                    </div>
                    <span class="float-left">
                        <form style="border: 1px solid #e9e9e9; padding: 20px; border-radius: 10px" name="import_empresa_funcionario" action="{{ route('empresa_funcionario.import', compact('empresa')) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="file" name="file" accept=".txt,.csv" required>
                            <button type="submit" style="display: block; margin-top: 10px; background: #0b0b0b; color: #fff; border: none; padding: 5px 20px; border-radius: 3px;">Importar</button>
                        </form>
                    </span>
                </div>

                <div class="tab-pane @if($aba == 'Campanhas') active @endif" id="campanhas" role="tabpanel">
                    <table id="dt_campanhas" class="table table-striped table-bordered dt-responsive nowrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Campanha</th>
                                <th>Formulário</th>
                                <th style="text-align:center;">Período</th>
                                <th style="text-align:center;">Qtd. Ativos</th>
                                <th style="text-align:center;">Qtd. Liberados</th>
                                <th style="text-align:center;">Qtd. Avaliados</th>
                                <th style="text-align:center;">Ações</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($campanha_empresas as $campanha_empresa)
                                <tr>
                                    <td>{{ $campanha_empresa->id }}</td>
                                    <td>{{ $campanha_empresa->campanha->titulo }}</td>
                                    <td><a href="javascript:;" onclick="preview_formulario('{{ $campanha_empresa->campanha->formulario->id }}')">{{$campanha_empresa->campanha->formulario->titulo}}</a></td>
                                    <td style="text-align:center;">{{ $campanha_empresa->campanha->periodo }}</td>
                                    <td style="text-align:center;">{{$campanha_empresa->empresa->empresa_funcionarios->whereIn('status', ['A'])->count()}}</td>
                                    <td style="text-align:center;">{{$campanha_empresa->campanha_funcionarios->count()}}</td>
                                    <td style="text-align:center;">{{$campanha_empresa->campanha_funcionarios->whereNotNull('data_realizacao')->count()}}</td>
                                    <td style="text-align:center;">

                                        @can('release_campanha_funcionario')
                                            <a href="javascript:;" data-toggle="modal"
                                            onclick="releaseData('{{$campanha_empresa->campanha->id}}', '{{$campanha_empresa->id}}');"
                                                data-target="#modal-release"><i class="fas fa-mail-bulk"
                                                    style="color: goldenrod" title="Liberar a avaliação da Campanha"></i></a>
                                        @endcan

                                        @can('view_empresa_funcionario')
                                            <a href="{{ route('campanha_empresa.avaliacaos', compact('campanha_empresa')) }}"><i class="fas fa-users"
                                                    style="color: goldenrod" title="Visualizar as avaliações"></i></a>
                                        @endcan

                                        @can('join_campanha_empresa')
                                            <a href="javascript:;" data-toggle="modal"
                                            onclick="deleteCampanha('{{$campanha_empresa->campanha->id}}', '{{$campanha_empresa->id}}');"
                                                data-target="#modal-delete-campanha"><i class="fa fa-minus-circle"
                                                    style="color: crimson" title="Excluir a Campanha Vinculada"></i></a>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8">Nenhum registro encontrado</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <!-- Nav tabs - LISTA AULA - FIM -->
                </div>

            <!-- FORMULÁRIO - FIM -->

            @section('modal_target')"deleteFuncionarioSubmit();"@endsection
            @section('modal_type')@endsection
            @section('modal_name')"modal-delete-funcionario"@endsection
            @section('modal_msg_title')Deseja excluir o Funcionário ? @endsection
            @section('modal_msg_description')O funcionário será excluído da empresa e também da sua conta de usuário. <p>Caso ele esteje vinculado em outra empresa e não puder ser excluído, ele pode ser inativado nessa empresa.</p>@endsection
            @section('modal_close')Fechar @endsection
            @section('modal_save')Excluir @endsection

            <form action="" id="deleteFuncionarioForm" method="post">
                @csrf
                @method('DELETE')
            </form>


            <div class="modal fade" id="modal-invite" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog " role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Deseja enviar em lote o e-mail para liberação do funcionário ?</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>O funcionário terá ser registro de usuário ativado e receberá um e-mail para acessar o sistema com uma senha gerada de forma aleatória. Todos os funcionários que estão Inativos em seu registro de usuário receberão o e-mail.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light waves-effect" data-dismiss="modal">Fechar </button>
                            <button type="button" onclick="inviteFormSubmit();" class="btn btn-primary waves-effect waves-light">Enviar e-mail em lote </button>
                        </div>
                    </div>
                </div>
            </div>

            <form action="" id="inviteForm" method="post">
                @csrf
                @method('PUT')
            </form>

            <div class="modal fade" id="modal-delete-campanha" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog " role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Deseja remover a Campanha Vinculada ?</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>A Campanha será desvinculada da Empresa.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light waves-effect" data-dismiss="modal">Fechar </button>
                            <button type="button" onclick="deleteCampanhaFormSubmit();" class="btn btn-primary waves-effect waves-light">Excluir Campanha </button>
                        </div>
                    </div>
                </div>
            </div>

            <form action="" id="deleteCampanhaForm" method="post">
                @csrf
                @method('DELETE')
            </form>

            <div class="modal fade" id="modal-release" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog " role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Deseja liberar para os funcionários ativos a Avaliação da Campanha ?</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>O funcionário terá seu acesso liberado para a realização da avaliação da campanha. </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light waves-effect" data-dismiss="modal">Fechar </button>
                            <button type="button" onclick="releaseFormSubmit();" class="btn btn-primary waves-effect waves-light">Liberar Avaliação </button>
                        </div>
                    </div>
                </div>
            </div>

            <form action="" id="releaseForm" method="post">
                @csrf
                @method('PUT')
            </form>

            <form action="" id="previewForm" method="post" target="_blank">
                @csrf
            </form>

            <!-- FORMULÁRIO - FIM -->
            </div>
        </div>
    </div>
</div>

@endsection


@section('script-js')
    <script src="{{asset('nazox/assets/js/pages/form-validation.init.js')}}"></script>
    <script src="{{asset('nazox/assets/libs/bs-custom-file-input/bs-custom-file-input.min.js')}}"></script>
    <script src="{{asset('nazox/assets/js/pages/form-element.init.js')}}"></script>
    <!-- form mask -->
    <script src="{{asset('nazox/assets/libs/inputmask/jquery.inputmask.min.js')}}"></script>

    <script>
		$(document).ready(function(){
			$('.mask_cep').inputmask('99.999-999');
            $('.mask_cnpj').inputmask('99.999.999/9999-99');
            $('.mask_telefone').inputmask('(99) 99999-9999');
            $('.mask_cpf').inputmask('999.999.999-99');
            $('.select2').select2();
		});
	</script>

    <script>

        function deleteFuncionario(empresa_funcionario) {
            var empresa_funcionario = empresa_funcionario;
            var url = '{{ route('empresa_funcionario.destroy_funcionario', [':empresa_funcionario']) }}';
            url = url.replace(':empresa_funcionario', empresa_funcionario);
            $("#deleteFuncionarioForm").attr('action', url);
        }

        function deleteFuncionarioSubmit() {
            $("#deleteFuncionarioForm").submit();
        }

        function deleteCampanha(campanha, campanha_empresa) {
            var campanha = campanha;
            var campanha_empresa = campanha_empresa;
            var url = '{{ route('campanha_empresa.destroy', [':campanha', ':campanha_empresa']) }}';
            url = url.replace(':campanha', campanha);
            url = url.replace(':campanha_empresa', campanha_empresa);
            $("#deleteCampanhaForm").attr('action', url);
        }

        function deleteCampanhaFormSubmit() {
            $("#deleteCampanhaForm").submit();
        }


        function inviteData(origem, empresa) {
            var empresa = empresa;
            var url = '{{ route('empresa_funcionario.invite', [':empresa']) }}';
            url = url.replace(':empresa', empresa);
            $("#inviteForm").attr('action', url);
        }

        function inviteFormSubmit() {
            $("#inviteForm").submit();
        }

        function releaseData(campanha, campanha_empresa) {
            var campanha = campanha;
            var campanha_empresa = campanha_empresa;
            var url = '{{ route('campanha_empresa.libera_funcionario', [':campanha', ':campanha_empresa']) }}';
            url = url.replace(':campanha', campanha);
            url = url.replace(':campanha_empresa', campanha_empresa);
            $("#releaseForm").attr('action', url);
        }

        function releaseFormSubmit() {
            $("#releaseForm").submit();
        }

        function preview_formulario(formulario){
            if(formulario){
                var url = '{{ route('painel.preview_formulario', [':formulario']) }}';
                url = url.replace(':formulario', formulario);
                $("#previewForm").attr('action', url);
                $("#previewForm").submit();
            }
        }

    </script>

    @if ($empresa_funcionarios->count() > 0)
        <script>
            var table = $('#dt_funcionarios').DataTable({
                language: {
                    url: '{{ asset('nazox/assets/localisation/pt_br.json') }}'
                },
                "order": [
                    [1, "asc"]
                ]
            });
        </script>
    @endif

    @if ($campanha_empresas->count() > 0)
        <script>
            var table = $('#dt_campanhas').DataTable({
                language: {
                    url: '{{ asset('nazox/assets/localisation/pt_br.json') }}'
                },
                "order": [
                    [1, "asc"]
                ]
            });
        </script>
    @endif


@endsection
