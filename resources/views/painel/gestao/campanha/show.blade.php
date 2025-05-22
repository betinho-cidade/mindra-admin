@extends('painel.layout.index')


@section('content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0">Informações da Campanha</h4>
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

<small style="color: mediumpurple">{!! $campanha->breadcrumb !!}</small>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
            <!-- FORMULÁRIO - INICIO -->

            <h4 class="card-title">Formulário de Atualização - Campanha {{$campanha->nome}}</h4>
            <p class="card-title-desc">A Campanha cadastrada será utilizada para acesso pelos funcionários no preenchimento do formulário de avaliação.</p>
            <form name="edit_campanha" method="POST" action="{{route('campanha.update', compact('campanha'))}}"  class="needs-validation" accept-charset="utf-8" enctype="multipart/form-data" novalidate>
                @csrf
                @method('PUT')

                <!-- Dados Pessoais - INI -->
                <div class="bg-soft-primary p-3 rounded" style="margin-bottom:10px;">
                    <h5 class="text-primary font-size-14" style="margin-bottom: 0px;">Dados Campanha</h5>
                </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="titulo">Título</label>
                                <input type="text" class="form-control" id="titulo" name="titulo" value="{{$campanha->titulo}}" placeholder="Título" required>
                                <div class="valid-feedback">ok!</div>
                                <div class="invalid-feedback">Inválido!</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="descricao">Descrição</label>
                                <textarea rows="2" class="form-control" id="descricao" name="descricao" placeholder="Descrição">{{$campanha->descricao}}</textarea>
                                <div class="valid-feedback">ok!</div>
                                <div class="invalid-feedback">Inválido!</div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="formulario">Formulário</label>
                                <select class="form-control">
                                    <option>{{ $campanha->formulario->titulo }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="data_inicio">Data Abertura</label>
                            <input type="date" name="data_inicio" id="data_inicio" class="form-control" value="{{$campanha->data_inicio_ajustada}}" required>
                        </div>
                        <div class="col-md-3">
                            <label for="data_fim">Data Fim</label>
                            <input type="date" name="data_fim" id="data_fim" class="form-control" value="{{$campanha->data_fim_ajustada}}" required>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="situacao">Situação</label>
                                <select id="situacao" name="situacao" class="form-control" required>
                                    <option value="">---</option>
                                    <option value="A" {{($campanha->status == 'A') ? 'selected' : '' }}>Ativo</option>
                                    <option value="I" {{($campanha->status == 'I') ? 'selected' : '' }}>Inativo</option>
                                </select>
                                <div class="valid-feedback">ok!</div>
                                <div class="invalid-feedback">Inválido!</div>
                            </div>
                        </div>
                    </div>
                    <p></p>
                <!-- Dados Pessoais - FIM -->

                <button class="btn btn-primary" type="submit">Salvar Cadastro</button>
            </form>

        @can('join_campanha_empresa')
            <div class="bg-soft-primary p-3 rounded" style="margin-top:60px;margin-bottom:10px;">
                <h5 class="text-primary font-size-14" style="margin-bottom: 0px;">Empresas vinculadas à campanha</h5>
            </div>

            <!-- Nav tabs - LISTA AULA/BANNER/AVALIAÇÃO - INI -->
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#empresas" role="tab">
                        <span class="d-block d-sm-none"><i class="ri-checkbox-circle-line"></i></span>
                        <span class="d-none d-sm-block">
                            @can('join_campanha_empresa')
                            <i onClick="location.href='{{route('campanha.empresa_create', compact('campanha'))}}';" class="fa fa-plus-square" style="color: goldenrod; margin-right:5px;" title="Nova Empresa"></i>
                            @endcan
                            Empresas ( <code class="highlighter-rouge">{{ $campanha_empresas->count() }}</code> )
                        </span>
                    </a>
                </li>
           </ul>
            <!-- Nav tabs - LISTA AULA/BANNER/AVALIAÇÃO - FIM -->

            <!-- Tab panes - INI -->
            <div class="tab-content p-3 text-muted">
                <!-- Nav tabs - LISTA AULA - INI -->
                <div class="tab-pane active" id="empresas" role="tabpanel">
                    <table id="dt_empresas" class="table table-striped table-bordered dt-responsive nowrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Empresa</th>
                                <th style="text-align:center;">Ações</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($campanha_empresas as $campanha_empresa)
                                <tr>
                                    <td>{{ $campanha_empresa->id }}</td>
                                    <td>{{ $campanha_empresa->empresa->nome }}</td>
                                    <td style="text-align:center;">
                                        @can('join_campanha_empresa')
                                            <a href="javascript:;" data-toggle="modal"
                                            onclick="deleteData('empresa', '{{$campanha_empresa->campanha->id}}', '{{$campanha_empresa->id}}');"
                                                data-target="#modal-delete"><i class="fa fa-minus-circle"
                                                    style="color: crimson" title="Excluir a Empresa Vinculada"></i></a>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">Nenhum registro encontrado</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <!-- Nav tabs - LISTA AULA - FIM -->
                </div>

            <!-- FORMULÁRIO - FIM -->

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

        @endcan

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
        function formSubmit() {
            $("#deleteForm").submit();
        }

        function deleteData(origem, campanha, origem_campanha) {
            var origem = origem;
            var campanha = campanha;

            if(origem == 'empresa'){
                var campanha_empresa = origem_campanha;
                var url = '{{ route('campanha.empresa_destroy', [':campanha', ':campanha_empresa']) }}';
                url = url.replace(':campanha', campanha);
                url = url.replace(':campanha_empresa', campanha_empresa);
                $("#deleteForm").attr('action', url);
            }
        }
    </script>

    @if ($campanha_empresas->count() > 0)
        <script>
            var table = $('#dt_empresas').DataTable({
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
