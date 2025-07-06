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

<small style="color: mediumpurple">{!! $empresa->breadcrumb !!}</small>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
            <!-- FORMULÁRIO - INICIO -->

            <h4 class="card-title">Formulário de Atualização - Empresa {{$empresa->nome}}</h4>
            <p class="card-title-desc">A Empresa cadastrada poderá acessar ao sistema e realizar as ações necessárias através de um dos seus consultores vinculados.</p>
            <form name="edit_empresa" method="POST" action="{{route('empresa.update', compact('empresa'))}}"  class="needs-validation" accept-charset="utf-8" enctype="multipart/form-data" novalidate>
                @csrf
                @method('PUT')

                <!-- Dados Pessoais - INI -->
                <div class="bg-soft-primary p-3 rounded" style="margin-bottom:10px;">
                    <h5 class="text-primary font-size-14" style="margin-bottom: 0px;">Dados Empresa</h5>
                </div>

                    <div class="row">
                        <div class="col-md-2">
                            <label for="cnpj">CNPJ</label>
                            <img src="{{asset('images/loading.gif')}}" id="img-loading-cnpj" style="display:none;max-width: 17%; margin-left: 26px;">
                            <input type="text" name="cnpj" id="cnpj" class="form-control mask_cnpj dynamic_cnpj" value="{{$empresa->cnpj}}" placeholder="99.999.999/9999-99" required>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="nome">Nome</label>
                                <input type="text" class="form-control" id="nome" name="nome" value="{{$empresa->nome}}" placeholder="Nome" required>
                                <div class="valid-feedback">ok!</div>
                                <div class="invalid-feedback">Inválido!</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="email">E-mail</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{$empresa->email}}" placeholder="E-mail">
                                <div class="valid-feedback">ok!</div>
                                <div class="invalid-feedback">Inválido!</div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="telefone">Telefone</label>
                            <input type="text" name="telefone" id="telefone" class="form-control mask_telefone" value="{{$empresa->telefone}}" placeholder="(99) 99999-9999">
                        </div>
                    </div>
                    <p></p>
                    <div class="row">
                        <div class="col-md-4">
                            <label for="responsavel_nome">Nome Responsável</label>
                            <input type="text" name="responsavel_nome" id="responsavel_nome" class="form-control" value="{{$empresa->responsavel_nome}}" placeholder="">
                        </div>
                        <div class="col-md-2">
                            <label for="responsavel_telefone">Telefone Responsável</label>
                            <input type="text" name="responsavel_telefone" id="responsavel_telefone" class="form-control mask_telefone" value="{{$empresa->responsavel_telefone}}" placeholder="(99) 99999-9999">
                        </div>
                        <div class="col-md-2">
                            <label for="inscricao_estadual">Inscrição Estadual</label>
                            <input type="text" name="inscricao_estadual" id="inscricao_estadual" class="form-control" value="{{$empresa->inscricao_estadual}}">
                        </div>
                        <div class="col-md-2">
                            <label for="qtd_funcionario">Qtd Funcionários</label>
                            <input type="number" name="qtd_funcionario" id="qtd_funcionario" class="form-control" value="{{$empresa->qtd_funcionario}}" placeholder="">
                        </div>
                        <div class="col-md-2">
                            <label for="data_abertura">Data Abertura</label>
                            <input type="date" name="data_abertura" id="data_abertura" class="form-control" value="{{$empresa->data_abertura}}">
                        </div>
                    </div>
                    <p></p>
                    <div class="row">
                        <div class="col-md-5">
                            <label for="atividade_principal">Atividade Principal</label>
                            <textarea rows="1" name="atividade_principal" id="atividade_principal" class="form-control">{{$empresa->atividade_principal}}</textarea>
                        </div>
                        <div class="col-md-3">
                            <label for="site">Site</label>
                            <input type="url" name="site" id="site" class="form-control" value="{{$empresa->site}}" placeholder="">
                        </div>
                        <div class="col-md-2">
                            <label for="num_contrato">Número Contrato</label>
                            <input type="text" name="num_contrato" id="num_contrato" class="form-control" value="{{$empresa->num_contrato}}">
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="situacao">Situação</label>
                                <select id="situacao" name="situacao" class="form-control" required>
                                    <option value="">---</option>
                                    <option value="A" {{($empresa->status == 'A') ? 'selected' : '' }}>Ativo</option>
                                    <option value="I" {{($empresa->status == 'I') ? 'selected' : '' }}>Inativo</option>
                                </select>
                                <div class="valid-feedback">ok!</div>
                                <div class="invalid-feedback">Inválido!</div>
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
                            <img src="{{asset('images/loading.gif')}}" id="img-loading-cep" style="display:none;max-width: 17%; margin-left: 26px;">
                            <input type="text" name="end_cep" id="end_cep" class="form-control dynamic_cep mask_cep" value="{{$empresa->end_cep}}" placeholder="99.999-999">
                        </div>

                        <div class="col-md-4">
                            <label for="end_cidade">Cidade</label>
                            <input type="text" name="end_cidade" id="end_cidade" class="form-control" value="{{$empresa->end_cidade}}">
                        </div>

                        <div class="col-md-2">
                            <label for="end_uf">Estado</label>
                            <input type="text" name="end_uf" id="end_uf" class="form-control" value="{{$empresa->end_uf}}">
                        </div>

                        <div class="col-md-4">
                            <label for="end_bairro">Bairro</label>
                            <input type="text" name="end_bairro" id="end_bairro" class="form-control" value="{{$empresa->end_bairro}}">
                        </div>
                    </div>
                    <p></p>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="end_endereco">Endereço</label>
                            <input type="text" name="end_logradouro" id="end_logradouro" class="form-control" value="{{$empresa->end_logradouro}}">
                        </div>

                        <div class="col-md-2">
                            <label for="end_numero">Número</label>
                            <input type="text" name="end_numero" id="end_numero" value="{{$empresa->end_numero}}" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label for="end_complemento">Complemento </label>
                            <input type="text" name="end_complemento" id="end_complemento" class="form-control" value="{{$empresa->end_complemento}}">
                        </div>
                    </div>
                    <p></p>
                    <div class="row">
                        <div class="col-md-4">
                            <label for="path_imagem">Imagem</label>
                                <div class="form-group custom-file">
                                <input type="file" class="custom-file-input" id="path_imagem" name="path_imagem"
                                    accept="image/*">
                                <label class="custom-file-label" for="path_imagem">Selecionar Imagem</label>
                                <br><br>
                                <a class="image-popup-no-margins imagem-edicao-interna" href="{{$empresa->imagem}}">
                                    <img class="avatar-sm mr-3" alt="200x200" width="200" src="{{$empresa->imagem}}" data-holder-rendered="true">
                                </a>
                                <br><br>
                                <div class="valid-feedback">ok!</div>
                                <div class="invalid-feedback">Inválido!</div>
                            </div>
                        </div>
                    </div>
                <!-- Dados Endereço - FIM -->

                <button class="btn btn-primary" type="submit">Salvar Cadastro</button>
            </form>

            <div class="bg-soft-primary p-3 rounded" style="margin-top:30px;margin-bottom:10px;">
                <h5 class="text-primary font-size-14" style="margin-bottom: 0px;">Consultores e Funcionários vinculados à Empresa</h5>
            </div>

            <!-- Nav tabs - LISTA AULA/BANNER/AVALIAÇÃO - INI -->
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#consultores" role="tab">
                        <span class="d-block d-sm-none"><i class="ri-checkbox-circle-line"></i></span>
                        <span class="d-none d-sm-block">
                            @can('create_empresa_consultor')
                            <i onClick="location.href='{{route('empresa.consultor_create', compact('empresa'))}}';" class="fa fa-plus-square" style="color: goldenrod; margin-right:5px;" title="Novo Consultor"></i>
                            @endcan
                            Consultores ( <code class="highlighter-rouge">{{ $consultor_empresas->count() }}</code> )
                        </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#funcionarios" role="tab">
                        <span class="d-block d-sm-none"><i class="ri-checkbox-circle-line"></i></span>
                        <span class="d-none d-sm-block">
                            Funcionários ( <code class="highlighter-rouge">{{ $empresa_funcionarios->count() }}</code> )
                        </span>
                    </a>
                </li>
           </ul>
            <!-- Nav tabs - LISTA AULA/BANNER/AVALIAÇÃO - FIM -->

            <!-- Tab panes - INI -->
            <div class="tab-content p-3 text-muted">
                <!-- Nav tabs - LISTA AULA - INI -->
                <div class="tab-pane active" id="consultores" role="tabpanel">
                    <table id="dt_consultores" class="table table-striped table-bordered dt-responsive nowrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Consultor</th>
                                <th>E-mail</th>
                                @can('view_empresa_consultor')
                                <th style="text-align:center;">Ações</th>
                                @endcan
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($consultor_empresas as $consultor_empresa)
                                <tr>
                                    <td>{{ $consultor_empresa->id }}</td>
                                    <td>{{ $consultor_empresa->consultor->user->nome }}</td>
                                    <td>{{ $consultor_empresa->consultor->user->email }}</td>
                                    @can('create_empresa_consultor')
                                    <td style="text-align:center;">
                                        @can('delete_empresa_consultor')
                                            <a href="javascript:;" data-toggle="modal"
                                            onclick="deleteData('consultor', '{{$consultor_empresa->empresa->id}}', '{{$consultor_empresa->id}}');"
                                                data-target="#modal-delete"><i class="fa fa-minus-circle"
                                                    style="color: crimson" title="Excluir o Consultor Vinculado"></i></a>
                                        @endcan

                                        @can('create_empresa_consultor')
                                            <a href="javascript:;" data-toggle="modal"
                                                onclick="statusData('{{$consultor_empresa->empresa->id}}', '{{$consultor_empresa->id}}');"
                                                    data-target="#modal-status">
                                                @if($consultor_empresa->status == 'A')
                                                    <i class="fas fa-user" style="color: rgb(8, 179, 16)" title="Desativar consultor ?"></i>&nbsp;
                                                @else
                                                    <i class="fas fa-user-times" style="color: rgb(218, 53, 53)" title="Ativar consultor ?"></i>&nbsp;
                                                @endif
                                            </a>
                                        @endcan
                                    </td>
                                    @endcan
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">Nenhum registro encontrado</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <!-- Nav tabs - LISTA AULA - FIM -->
                </div>

                <div class="tab-pane" id="funcionarios" role="tabpanel">
                    <div>Quantidade de funcionários ativos: {{ $empresa_funcionarios->where('status', 'A')->count() }}</div>
                    <div>Quantidade de funcionários inativos: {{ $empresa_funcionarios->where('status', 'I')->count() }}</div>
            </div>
            <!-- FORMULÁRIO - FIM -->

            @can('create_empresa_consultor')

            @section('modal_target')"formSubmit();"@endsection
            @section('modal_type')@endsection
            @section('modal_name')"modal-delete"@endsection
            @section('modal_msg_title')Deseja excluir o registro ? @endsection
            @section('modal_msg_description')O registro selecionado será excluído definitivamente. @endsection
            @section('modal_close')Fechar @endsection
            @section('modal_save')Excluir @endsection

            <form action="" id="deleteForm" method="post">
                @csrf
                @method('DELETE')
            </form>


            <div class="modal fade" id="modal-status" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog " role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Deseja alterar o status do Consultor ?</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>O consultor terá seu status alterado. </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light waves-effect" data-dismiss="modal">Fechar </button>
                            <button type="button" onclick="statusFormSubmit();" class="btn btn-primary waves-effect waves-light">Alterar Status </button>
                        </div>
                    </div>
                </div>
            </div>

            <form action="" id="statusForm" method="post">
                @csrf
                @method('PUT')
            </form>

            @endcan

            <!-- FORMULÁRIO - FIM -->
            </div>
        </div>
    </div>
</div>

@endsection

@section('head-css')
    <link href="{{asset('nazox/assets/libs/magnific-popup/magnific-popup.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('script-js')
    <script src="{{asset('nazox/assets/js/pages/form-validation.init.js')}}"></script>
    <script src="{{asset('nazox/assets/libs/bs-custom-file-input/bs-custom-file-input.min.js')}}"></script>
    <script src="{{asset('nazox/assets/js/pages/form-element.init.js')}}"></script>
    <!-- form mask -->
    <script src="{{asset('nazox/assets/libs/inputmask/jquery.inputmask.min.js')}}"></script>

    <script src="{{asset('nazox/assets/libs/magnific-popup/jquery.magnific-popup.min.js')}}"></script>
    <script src="{{asset('nazox/assets/js/pages/lightbox.init.js')}}"></script>

    <script>
		$(document).ready(function(){
			$('.mask_cep').inputmask('99.999-999');
            $('.mask_cnpj').inputmask('99.999.999/9999-99');
            $('.mask_telefone').inputmask('(99) 99999-9999');
            $('.select2').select2();
		});
	</script>

    <script type='text/javascript'>
        $(document).ready(function(){
            $('.dynamic_cep').change(function(){

                if ($(this).val() != ''){
                    document.getElementById("img-loading-cep").style.display = '';

                    var cep = $('#end_cep').val();
                    var _token = $('input[name="_token"]').val();

                    $('#end_logradouro').val('');
                    $('#end_complemento').val('');
                    $('#end_numero').val('');
                    $('#end_bairro').val('');
                    $('#end_cidade').val('');
                    $('#end_uf').val('');

                    $.ajax({
                        url: "{{route('painel.js_viacep')}}",
                        method: "POST",
                        data: {_token:_token, cep:cep},
                        success:function(result){
                            dados = JSON.parse(result);
                            if(dados==null || dados['error'] == 'true'){
                                    console.log(dados);
                            } else{
                                    $('#end_logradouro').val(dados['logradouro']);
                                    $('#end_complemento').val(dados['complemento']);
                                    $('#end_bairro').val(dados['bairro']);
                                    $('#end_cidade').val(dados['localidade']);
                                    $('#end_uf').val(dados['uf']);
                            }
                            document.getElementById("img-loading-cep").style.display = 'none';
                        },
                        error:function(erro){
                            document.getElementById("img-loading-cep").style.display = 'none';
                        }
                    })
                }
            });
        });

        $('.dynamic_cnpj').change(function(){

            if ($(this).val() != ''){
                var cnpj = $('#cnpj').val();
                var _token = $('input[name="_token"]').val();

                $('#nome').val('');
                $('#email').val('');
                $('#telefone').val('');
                $('#end_cep').val('');
                $('#end_cidade').val('');
                $('#end_uf').val('');
                $('#end_bairro').val('');
                $('#end_logradouro').val('');
                $('#end_numero').val('');
                $('#end_complemento').val('');

                document.getElementById("img-loading-cnpj").style.display = '';

                $.ajax({
                    url: "{{route('painel.js_cnpj')}}",
                    method: "POST",
                    data: {_token:_token, cnpj:cnpj},
                    success:function(result){
                        dados = JSON.parse(result);

                        if(dados==null || dados['status'] == 'ERROR'){
                                console.log(dados);
                        } else{
                                $('#nome').val(dados['nome']);
                                $('#email').val(dados['email']);

                                data_abertura = dados['abertura'];

                                if(data_abertura){
                                    data_array = data_abertura.split('/');
                                    new_data_abertura = data_array[2] + '-' + data_array[1] + '-' + data_array[0];
                                    $('#data_abertura').val(new_data_abertura);
                                }

                                telefone = dados['telefone'];

                                if(telefone.includes("/")){
                                    telefone = telefone.substring(0, telefone.indexOf("/"));
                                    $('#telefone').val(telefone.replace(/[^0-9]+/g, ""));
                                }else{
                                    $('#telefone').val(telefone.replace(/[^0-9]+/g, ""));
                                }

                                if(dados['atividade_principal'][0]){
                                    $('#atividade_principal').val(dados['atividade_principal'][0]['text']);
                                }

                                socios = dados['qsa'];

                                if(socios){
                                    for(let i = 0; i < socios.length; i = i + 1 ) {
                                        if(socios[i]['nome_rep_legal']){
                                            $('#responsavel_nome').val(socios[i]['nome_rep_legal']);
                                            break;
                                        }
                                    }
                                }

                                $('#end_cep').val(dados['cep'].replace(/[^0-9]+/g, ""));
                                $('#end_cidade').val(dados['municipio']);
                                $('#end_uf').val(dados['uf']);
                                $('#end_bairro').val(dados['bairro']);
                                $('#end_logradouro').val(dados['logradouro']);
                                $('#end_numero').val(dados['numero']);
                                $('#end_complemento').val(dados['complemento']);
                        }
                        document.getElementById("img-loading-cnpj").style.display = 'none';
                    },
                    error:function(erro){
                        document.getElementById("img-loading-cnpj").style.display = 'none';
                    }
                });
            }
        });
    </script>

    @can('create_empresa_consultor')
    <script>
        function formSubmit() {
            $("#deleteForm").submit();
        }

        function deleteData(origem, empresa, origem_empresa) {
            var origem = origem;
            var empresa = empresa;

            if(origem == 'consultor'){
                var consultor_empresa = origem_empresa;
                var url = '{{ route('empresa.consultor_destroy', [':empresa', ':consultor_empresa']) }}';
                url = url.replace(':empresa', empresa);
                url = url.replace(':consultor_empresa', consultor_empresa);
                $("#deleteForm").attr('action', url);
            }else if(origem == 'usuario'){
                var parceiro_usuario = origem_parceiro;
                var url = '';
                url = url.replace(':parceiro', parceiro);
                url = url.replace(':parceiro_usuario', parceiro_usuario);
                $("#deleteForm").attr('action', url);
            }
        }


        function statusData(empresa, origem_empresa) {
            var empresa = empresa;
            var consultor_empresa = origem_empresa;
            var url = '{{ route('empresa.consultor_status', [':empresa', ':consultor_empresa']) }}';
            url = url.replace(':empresa', empresa);
            url = url.replace(':consultor_empresa', consultor_empresa);
            $("#statusForm").attr('action', url);
        }


        function statusFormSubmit() {
            $("#statusForm").submit();
        }
    </script>
    @endcan

    @if ($consultor_empresas->count() > 0)
        <script>
            var table = $('#dt_consultores').DataTable({
                language: {
                    url: '{{ asset('nazox/assets/localisation/pt_br.json') }}'
                },
                "order": [
                    [1, "asc"]
                ]
            });
        </script>
    @endif

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

@endsection
