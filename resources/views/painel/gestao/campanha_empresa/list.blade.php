@extends('painel.layout.index')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0">Avaliações dos Funcionários e Consultores</h4>
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

<small style="color: mediumpurple">{!! $campanha->empresa->breadcrumb_gestao !!}</small>

    <div class="col-md-12" style="padding:0;">
        <div class="card" style="margin-bottom: 0;">
            <div class="card-body" style="padding:0;">
                <!-- Right Sidebar -->
                    <div class="card" style="margin-bottom: 0;">
                        <div class="card-body">

                            <div class="tab-content py-4">
                                <div class="tab-pane show active" id="ativas">
                                    <div>
                                        <h5 class="px-3 mb-3" style="text-align: left; margin-top: -15px; padding-left: 0 !important; margin-bottom: 0px !important;">{{ strtoupper($campanha->titulo) }}</h5>
                                        <p style="margin-top: 5px;">Formulário: <a href="javascript:;" onclick="preview_formulario('{{ $campanha->formulario->id }}')">{{ $campanha->formulario->titulo }}</a>
                                        <br>Checklist: <a href="javascript:;" onclick="preview_checklist('{{ $campanha->checklist->id }}')">{{ $campanha->checklist->titulo }}</a></p>
                                        <p style="margin-top: -10px;">Período {{ $campanha->periodo }}</p>

                                        <ul class="nav nav-tabs" role="tablist">
                                            <li class="nav-item" title="Lista de Funcionários participantes da Campanha">
                                                <a class="nav-link active" data-toggle="tab" href="#funcionarios" role="tab">
                                                    <span class="d-sm-block">Funcionários ( <code class="highlighter-rouge">{{$campanha->campanha_funcionarios->count()}}</code> )</span>
                                                </a>
                                            </li>
                                            <li class="nav-item" title="Lista de Consultores participantes do Checklist">
                                                <a class="nav-link" data-toggle="tab" href="#consultores" role="tab">
                                                    <span class="d-sm-block">Consultores ( <code class="highlighter-rouge">{{$campanha->checklist_consultors->count()}}</code> )</span>
                                                </a>
                                            </li>                                            
                                        </ul>

                                        <!-- Tab panes -->
                                        <div class="tab-content p-3 text-muted">

                                            <!-- Lista Funcionários - INI -->
                                            <div class="tab-pane active" id="funcionarios" role="tabpanel">
                                                <ul class="list-unstyled chat-list" data-simplebar>
                                                    <table id="dt_funcionarios" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                                        <thead>
                                                        <tr>
                                                            <th>Nome</th>
                                                            <th style="text-align:center;">Liberado</th>
                                                            <th style="text-align:center;">Iniciado</th>
                                                            <th style="text-align:center;">Realizado</th>
                                                            <th style="text-align:center;">Ações</th>
                                                        </tr>
                                                        </thead>

                                                        <tbody>
                                                        @forelse($campanha->campanha_funcionarios as $campanha_funcionario)
                                                            <tr>
                                                                <td>@if($campanha_funcionario->empresa_funcionario->status == 'A')
                                                                        <i class="fas fa-user" style="font-size:10px; color: rgb(8, 179, 16)" title="Ativo na Empresa"></i>&nbsp;
                                                                    @else
                                                                        <i class="fas fa-user-times" style="font-size:10px;color: rgb(218, 53, 53)" title="Inativo na Empresa"></i>&nbsp;
                                                                    @endif
                                                                    {{$campanha_funcionario->empresa_funcionario->funcionario->user->nome}}</td>
                                                                <td style="text-align:center;">{{$campanha_funcionario->data_liberado_formatada}}</td>
                                                                <td style="text-align:center;">{{$campanha_funcionario->data_iniciado_formatada}}</td>
                                                                <td style="text-align:center;">{{$campanha_funcionario->data_realizado_formatada}}</td>
                                                                <td style="text-align:center;">

                                                                    @can('release_campanha_funcionario')
                                                                        <a href="javascript:;" data-toggle="modal"
                                                                        onclick="deleteFuncionario('{{$campanha->id}}', '{{$campanha_funcionario->id}}');"
                                                                            data-target="#modal-delete-funcionario"><i class="fa fa-minus-circle"
                                                                                style="color: crimson" title="Excluir o Funcionário da Avaliação na Empresa"></i></a>
                                                                    @endcan

                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <td colspan="5">Nenhum registro encontrado</td>
                                                        @endforelse
                                                        </tbody>
                                                    </table>
                                                </ul>
                                            </div>
                                            <!-- Lista Funcionários - FIM -->

                                            <!-- Lista Consultores - INI -->
                                            <div class="tab-pane" id="consultores" role="tabpanel">
                                                <ul class="list-unstyled chat-list" data-simplebar>
                                                    <table id="dt_consultores" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                                        <thead>
                                                        <tr>
                                                            <th>Nome</th>
                                                            <th style="text-align:center;">Liberado</th>
                                                            <th style="text-align:center;">Iniciado</th>
                                                            <th style="text-align:center;">Realizado</th>
                                                            <th style="text-align:center;">Ações</th>
                                                        </tr>
                                                        </thead>

                                                        <tbody>
                                                        @forelse($campanha->checklist_consultors as $checklist_consultor)
                                                            <tr>
                                                                <td>@if($checklist_consultor->consultor_empresa->status == 'A')
                                                                        <i class="fas fa-user" style="font-size:10px; color: rgb(8, 179, 16)" title="Ativo na Empresa"></i>&nbsp;
                                                                    @else
                                                                        <i class="fas fa-user-times" style="font-size:10px;color: rgb(218, 53, 53)" title="Inativo na Empresa"></i>&nbsp;
                                                                    @endif
                                                                    {{$checklist_consultor->consultor_empresa->consultor->user->nome}}</td>
                                                                <td style="text-align:center;">{{$checklist_consultor->data_liberado_formatada}}</td>
                                                                <td style="text-align:center;">{{$checklist_consultor->data_iniciado_formatada}}</td>
                                                                <td style="text-align:center;">{{$checklist_consultor->data_realizado_formatada}}</td>
                                                                <td style="text-align:center;">

                                                                    @can('release_checklist_consultor')
                                                                        <a href="javascript:;" data-toggle="modal"
                                                                        onclick="deleteConsultor('{{$campanha->id}}', '{{$checklist_consultor->id}}');"
                                                                            data-target="#modal-delete-consultor"><i class="fa fa-minus-circle"
                                                                                style="color: crimson" title="Excluir o Consultor do Checklist na Empresa"></i></a>
                                                                    @endcan

                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <td colspan="5">Nenhum registro encontrado</td>
                                                        @endforelse
                                                        </tbody>
                                                    </table>
                                                </ul>
                                            </div>
                                            <!-- Lista Consultores - FIM -->

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
            @section('modal_msg_description')Caso o funcionário não tenha iniciado a avaliação, ele será excluído desta campanha.@endsection
            @section('modal_close')Fechar @endsection
            @section('modal_save')Excluir @endsection

            <form action="" id="deleteFuncionarioForm" method="post">
                @csrf
                @method('DELETE')
            </form>

            <form action="" id="previewForm" method="post" target="_blank">
                @csrf
            </form>

            <form action="" id="previewCheckForm" method="post" target="_blank">
                @csrf
            </form>            

            <div class="modal fade" id="modal-delete-consultor" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog " role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Deseja excluir o Consultor do Checklist ?</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>Caso o Consultor não tenha iniciado o Checklist, ele será excluído desta campanha.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light waves-effect" data-dismiss="modal">Fechar </button>
                            <button type="button" onclick="deleteConsultorSubmit();" class="btn btn-primary waves-effect waves-light">Excluir </button>
                        </div>
                    </div>
                </div>
            </div>

            <form action="" id="deleteConsultorForm" method="post">
                @csrf
                @method('DELETE')
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

        function preview_checklist(checklist){
            if(checklist){
                var url = '{{ route('painel.preview_checklist', [':checklist']) }}';
                url = url.replace(':checklist', checklist);
                $("#previewCheckForm").attr('action', url);
                $("#previewCheckForm").submit();
            }
        }        

        function deleteFuncionario(campanha, campanha_funcionario) {
            var campanha = campanha;
            var campanha_funcionario = campanha_funcionario;
            var url = '{{ route('campanha_empresa.destroy_funcionario', [':campanha', ':campanha_funcionario']) }}';
            url = url.replace(':campanha', campanha);
            url = url.replace(':campanha_funcionario', campanha_funcionario);
            $("#deleteFuncionarioForm").attr('action', url);
        }

        function deleteFuncionarioSubmit() {
            $("#deleteFuncionarioForm").submit();
        }

        function deleteConsultor(campanha, checklist_consultor) {
            var campanha = campanha;
            var checklist_consultor = checklist_consultor;
            var url = '{{ route('campanha_empresa.destroy_consultor', [':campanha', ':checklist_consultor']) }}';
            url = url.replace(':campanha', campanha);
            url = url.replace(':checklist_consultor', checklist_consultor);
            $("#deleteConsultorForm").attr('action', url);
        }

        function deleteConsultorSubmit() {
            $("#deleteConsultorForm").submit();
        }        


    </script>

    @if($campanha->campanha_funcionarios->count() > 0)
        <script>
            var table_AT = $('#dt_funcionarios').DataTable({
                language: {
                    url: '{{asset('nazox/assets/localisation/pt_br.json')}}'
                },
                "order": [[ 1, "asc" ]]
            });
    </script>
    @endif

    @if($campanha->checklist_consultors->count() > 0)
        <script>
            var table_AT = $('#dt_consultores').DataTable({
                language: {
                    url: '{{asset('nazox/assets/localisation/pt_br.json')}}'
                },
                "order": [[ 1, "asc" ]]
            });
    </script>
    @endif    

@endsection
