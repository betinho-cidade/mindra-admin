@extends('painel.layout.index')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0">Campanhas do Sistema</h4>
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

    <div class="col-md-12" style="padding:0;">
        <div class="card" style="margin-bottom: 0;">
            <div class="card-body" style="padding:0;">
                <!-- Right Sidebar -->
                    <div class="card" style="margin-bottom: 0;">
                        <div class="card-body">

                            <span class="float-right">
                                @can('create_campanha')
                                    <a href="{{route("campanha.create")}}" class="btn btn-outline-secondary waves-effect">Nova campanha</a>
                                @endcan
                            </span>

                            <div class="tab-content py-4">
                                <div class="tab-pane show active" id="pendente">
                                    <div>
                                        <h5 class="px-3 mb-3" style="text-align: left; margin-top: -15px; padding-left: 0 !important; margin-bottom: 25px !important;">Listagem de campanhas</h1>

                                        <ul class="nav nav-tabs" role="tablist">
                                            <li class="nav-item" title="Lista de campanhas Ativas">
                                                <a class="nav-link active" data-toggle="tab" href="#ativas" role="tab">
                                                    <span class="d-sm-block">Campanhas Ativas( <code class="highlighter-rouge">{{$campanhas_AT->count()}}</code> )</span>
                                                </a>
                                            </li>
                                            <li class="nav-item" title="Lista de campanhas Inativas">
                                                <a class="nav-link" data-toggle="tab" href="#inativas" role="tab">
                                                    <span class="d-sm-block">Campanhas Inativas( <code class="highlighter-rouge">{{$campanhas_IN->count()}}</code> )</span>
                                                </a>
                                            </li>
                                        </ul>

                                        <!-- Tab panes -->
                                        <div class="tab-content p-3 text-muted">

                                            <!-- Lista campanhas - INI -->
                                            <div class="tab-pane active" id="ativas" role="tabpanel">
                                                <ul class="list-unstyled chat-list" data-simplebar>
                                                    <table id="dt_ativas" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                                        <thead>
                                                        <tr>
                                                            <th>Título</th>
                                                            <th>Formulário</th>
                                                            <th>Início</th>
                                                            <th>Fim</th>
                                                            <th style="text-align:center;">Ações</th>
                                                        </tr>
                                                        </thead>

                                                        <tbody>
                                                        @forelse($campanhas_AT as $campanha)
                                                            <tr>
                                                                <td>{{$campanha->titulo}}</td>
                                                                <td><a href="javascript:;" onclick="preview_formulario('{{ $campanha->formulario->id }}')">{{$campanha->formulario->titulo}}</a></td>
                                                                <td>{{$campanha->data_inicio_formatada}}</td>
                                                                <td>{{$campanha->data_fim_formatada}}</td>
                                                                <td style="text-align:center;">
                                                                    @can('edit_campanha')
                                                                        <a href="{{route('campanha.show', compact('campanha'))}}"><i class="fa fa-edit" style="color: goldenrod" title="Editar campanha"></i></a>
                                                                    @endcan

                                                                    @can('delete_campanha')
                                                                        <a href="javascript:;" data-toggle="modal" onclick="deleteData({{$campanha->id}})"
                                                                            data-target="#modal-delete-campanha"><i class="fa fa-minus-circle" style="color: crimson" title="Excluir a campanha"></i></a>
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
                                            <!-- Lista campanhas - FIM -->

                                            <!-- Lista campanhas - INI -->
                                            <div class="tab-pane" id="inativas" role="tabpanel">
                                                <ul class="list-unstyled chat-list" data-simplebar>
                                                    <table id="dt_inativas" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                                        <thead>
                                                        <tr>
                                                            <th>Título</th>
                                                            <th>Formulário</th>
                                                            <th>Início</th>
                                                            <th>Fim</th>
                                                            <th style="text-align:center;">Ações</th>
                                                        </tr>
                                                        </thead>

                                                        <tbody>
                                                        @forelse($campanhas_IN as $campanha)
                                                            <tr>
                                                                <td>{{$campanha->titulo}}</td>
                                                                <td><a href="javascript:;" onclick="preview_formulario('{{ $campanha->formulario->id }}')">{{$campanha->formulario->titulo}}</a></td>
                                                                <td>{{$campanha->data_inicio_formatada}}</td>
                                                                <td>{{$campanha->data_fim_formatada}}</td>
                                                                <td style="text-align:center;">
                                                                    @can('edit_campanha')
                                                                        <a href="{{route('campanha.show', compact('campanha'))}}"><i class="fa fa-edit" style="color: goldenrod" title="Editar campanha"></i></a>
                                                                    @endcan

                                                                    @can('delete_campanha')
                                                                        <a href="javascript:;" data-toggle="modal" onclick="deleteData({{$campanha->id}})"
                                                                            data-target="#modal-delete-campanha"><i class="fa fa-minus-circle" style="color: crimson" title="Excluir a campanha"></i></a>
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
                                            <!-- Lista campanhas - FIM -->
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


    <form action="" id="deleteForm" method="post">
        @csrf
        @method('DELETE')
    </form>

    <form action="" id="previewForm" method="post" target="_blank">
        @csrf
    </form>

    @section('modal_target')"formSubmit();"@endsection
    @section('modal_type')@endsection
    @section('modal_name')"modal-delete-campanha"@endsection
    @section('modal_msg_title')Deseja excluir o registro ? @endsection
    @section('modal_msg_description')O registro selecionado será excluído definitivamente, BEM COMO TODOS seus relacionamentos. @endsection
    @section('modal_close')Fechar @endsection
    @section('modal_save')Excluir @endsection


</div>

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
       function deleteData(id)
       {
           var id = id;
           var url = '{{ route("campanha.destroy", ":id") }}';
           url = url.replace(':id', id);
           $("#deleteForm").attr('action', url);
       }

       function formSubmit()
       {
           $("#deleteForm").submit();
       }

        function preview_formulario(formulario){
            if(formulario){
                var url = '{{ route('campanha.preview_formulario', [':formulario']) }}';
                url = url.replace(':formulario', formulario);
                $("#previewForm").attr('action', url);
                $("#previewForm").submit();
            }
        }

    </script>

    @if($campanhas_AT->count() > 0)
        <script>
            var table_AT = $('#dt_ativas').DataTable({
                language: {
                    url: '{{asset('nazox/assets/localisation/pt_br.json')}}'
                },
                "order": [[ 1, "asc" ]]
            });
    </script>
    @endif

    @if($campanhas_IN->count() > 0)
        <script>
            var table_IN = $('#dt_inativas').DataTable({
                language: {
                    url: '{{asset('nazox/assets/localisation/pt_br.json')}}'
                },
                "order": [[ 1, "asc" ]]
            });
        </script>
    @endif

@endsection
