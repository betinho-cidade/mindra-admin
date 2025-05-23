@extends('painel.layout.index')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0">Avaliações dos funcionários</h4>
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

<small style="color: mediumpurple">{!! $campanha_empresa->empresa->breadcrumb_gestao !!}</small>

    <div class="col-md-12" style="padding:0;">
        <div class="card" style="margin-bottom: 0;">
            <div class="card-body" style="padding:0;">
                <!-- Right Sidebar -->
                    <div class="card" style="margin-bottom: 0;">
                        <div class="card-body">

                            <div class="tab-content py-4">
                                <div class="tab-pane show active" id="ativas">
                                    <div>
                                        <h5 class="px-3 mb-3" style="text-align: left; margin-top: -15px; padding-left: 0 !important; margin-bottom: 0px !important;">{{ strtoupper($campanha_empresa->campanha->titulo) }}</h5>
                                        <p style="margin-top: 5px;"><a href="javascript:;" onclick="preview_formulario('{{ $campanha_empresa->campanha->formulario->id }}')">{{ $campanha_empresa->campanha->formulario->titulo }}</a></p>
                                        <p style="margin-top: -10px;">Período {{ $campanha_empresa->campanha->periodo }}</p>

                                        <ul class="nav nav-tabs" role="tablist">
                                            <li class="nav-item" title="Lista de Empresas Ativas">
                                                <a class="nav-link active" data-toggle="tab" href="#ativas" role="tab">
                                                    <span class="d-sm-block">Funcionários ( <code class="highlighter-rouge">{{$campanha_empresa->campanha_funcionarios->count()}}</code> )</span>
                                                </a>
                                            </li>
                                        </ul>

                                        <!-- Tab panes -->
                                        <div class="tab-content p-3 text-muted">

                                            <!-- Lista Empresas - INI -->
                                            <div class="tab-pane active" id="ativas" role="tabpanel">
                                                <ul class="list-unstyled chat-list" data-simplebar>
                                                    <table id="dt_ativas" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                                        <thead>
                                                        <tr>
                                                            <th>Nome</th>
                                                            <th style="text-align:center;">Liberado</th>
                                                            <th style="text-align:center;">Realizado</th>
                                                            <th style="text-align:center;">Ações</th>
                                                        </tr>
                                                        </thead>

                                                        <tbody>
                                                        @forelse($campanha_empresa->campanha_funcionarios as $campanha_funcionario)
                                                            <tr>
                                                                <td>@if($campanha_funcionario->empresa_funcionario->status == 'A')
                                                                        <i class="fas fa-user" style="font-size:10px; color: rgb(8, 179, 16)" title="Ativo na Empresa"></i>&nbsp;
                                                                    @else
                                                                        <i class="fas fa-user-times" style="font-size:10px;color: rgb(218, 53, 53)" title="Inativo na Empresa"></i>&nbsp;
                                                                    @endif
                                                                    {{$campanha_funcionario->empresa_funcionario->funcionario->user->nome}}</td>
                                                                <td style="text-align:center;">{{$campanha_funcionario->data_liberacao_formatada}}</td>
                                                                <td style="text-align:center;">{{$campanha_funcionario->data_realizacao_formatada}}</td>
                                                                <td style="text-align:center;">

                                                                    @can('release_campanha_funcionario')
                                                                        <a href="javascript:;" data-toggle="modal"
                                                                        onclick="deleteFuncionario('{{$campanha_funcionario->id}}');"
                                                                            data-target="#modal-delete-funcionario"><i class="fa fa-minus-circle"
                                                                                style="color: crimson" title="Excluir o Funcionário da Avaliação na Empresa"></i></a>
                                                                    @endcan

                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <td colspan="4">Nenhum registro encontrado</td>
                                                        @endforelse
                                                        </tbody>
                                                    </table>
                                                </ul>
                                            </div>
                                            <!-- Lista Empresas - FIM -->

                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                <!-- Right Sidebar -->
            </div>
        </div>
    </div>

</div>

            @section('modal_target')"deleteFuncionarioSubmit();"@endsection
            @section('modal_type')@endsection
            @section('modal_name')"modal-delete-funcionario"@endsection
            @section('modal_msg_title')Deseja excluir o Funcionário da Avaliação ? @endsection
            @section('modal_msg_description')Caso o funcionário não tenha iniciado a avaliação, ele será excluído desta campanha.</p>@endsection
            @section('modal_close')Fechar @endsection
            @section('modal_save')Excluir @endsection

            <form action="" id="deleteFuncionarioForm" method="post">
                @csrf
                @method('DELETE')
            </form>

            <form action="" id="previewForm" method="post" target="_blank">
                @csrf
            </form>


@endsection


@section('script-js')
    <!-- Required datatable js -->
    <script src="{{asset('nazox/assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('nazox/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
    <!-- Responsive examples -->
    <script src="{{asset('nazox/assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js')}}"></script>
    <script src="{{asset('nazox/assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js')}}"></script>
    <!-- Datatable init js -->
    <script src="{{asset('nazox/assets/js/pages/datatables.init.js')}}"></script>
    <!-- form mask -->
    <script src="{{asset('nazox/assets/libs/inputmask/jquery.inputmask.min.js')}}"></script>

    <script>
        function preview_formulario(formulario){
            if(formulario){
                var url = '{{ route('painel.preview_formulario', [':formulario']) }}';
                url = url.replace(':formulario', formulario);
                $("#previewForm").attr('action', url);
                $("#previewForm").submit();
            }
        }

        function deleteFuncionario(campanha_funcionario) {
            var campanha_funcionario = campanha_funcionario;
            var url = '{{ route('campanha_empresa.destroy_funcionario', [':campanha_funcionario']) }}';
            url = url.replace(':campanha_funcionario', campanha_funcionario);
            $("#deleteFuncionarioForm").attr('action', url);
        }

        function deleteFuncionarioSubmit() {
            $("#deleteFuncionarioForm").submit();
        }


    </script>

    @if($campanha_empresa->campanha_funcionarios->count() > 0)
        <script>
            var table_AT = $('#dt_ativas').DataTable({
                language: {
                    url: '{{asset('nazox/assets/localisation/pt_br.json')}}'
                },
                "order": [[ 1, "asc" ]]
            });
    </script>
    @endif

@endsection
