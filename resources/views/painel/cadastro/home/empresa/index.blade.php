@extends('painel.layout.index')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0">Empresas do Sistema</h4>
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
                                @can('create_empresa')
                                    <a href="{{route("empresa.create")}}" class="btn btn-outline-secondary waves-effect">Nova Empresa</a>
                                @endcan
                            </span>

                            <div class="tab-content py-4">
                                <div class="tab-pane show active" id="pendente">
                                    <div>
                                        <h5 class="px-3 mb-3" style="text-align: left; margin-top: -15px; padding-left: 0 !important; margin-bottom: 25px !important;">Listagem de Empresas</h1>

                                        <ul class="nav nav-tabs" role="tablist">
                                            <li class="nav-item" title="Lista de Empresas Ativas">
                                                <a class="nav-link active" data-toggle="tab" href="#ativas" role="tab">
                                                    <span class="d-sm-block">Empresas Ativas( <code class="highlighter-rouge">{{$empresas_AT->count()}}</code> )</span>
                                                </a>
                                            </li>
                                            <li class="nav-item" title="Lista de Empresas Inativas">
                                                <a class="nav-link" data-toggle="tab" href="#inativas" role="tab">
                                                    <span class="d-sm-block">Empresas Inativas( <code class="highlighter-rouge">{{$empresas_IN->count()}}</code> )</span>
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
                                                            <th>CNPJ</th>
                                                            <th>Email</th>
                                                            <th style="text-align:center;">Qtd. Funcionários</th>
                                                            <th style="text-align:center;">Ações</th>
                                                        </tr>
                                                        </thead>

                                                        <tbody>
                                                        @forelse($empresas_AT as $empresa)
                                                            <tr>
                                                                <td class="icone_ativacao">
                                                                    @if($empresa->status == 'A')
                                                                        <i class="fas fa-user" style="font-size:10px; color: rgb(8, 179, 16)" title="Ativa"></i>&nbsp;
                                                                    @else
                                                                        <i class="fas fa-user-times" style="font-size:10px;color: rgb(218, 53, 53)" title="Inativa"></i>&nbsp;
                                                                    @endif
                                                                    {{$empresa->nome}}
                                                                </td>
                                                                <td class="mask_cnpj">{{$empresa->cnpj}}</td>
                                                                <td>{{$empresa->email}}</td>
                                                                <td style="text-align:center;">{{$empresa->empresa_funcionarios->count()}}</td>
                                                                <td style="text-align:center;">
                                                                    @can('edit_empresa')
                                                                        <a href="{{route('empresa.show', compact('empresa'))}}"><i class="fa fa-edit" style="color: goldenrod" title="Editar Empresa"></i></a>
                                                                    @endcan

                                                                    @can('delete_empresa')
                                                                        <a href="javascript:;" data-toggle="modal" onclick="deleteData({{$empresa->id}})"
                                                                            data-target="#modal-delete-empresa"><i class="fa fa-minus-circle" style="color: crimson" title="Excluir a Empresa"></i></a>
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
                                            <!-- Lista Empresas - FIM -->

                                            <!-- Lista Empresas - INI -->
                                            <div class="tab-pane" id="inativas" role="tabpanel">
                                                <ul class="list-unstyled chat-list" data-simplebar>
                                                    <table id="dt_inativas" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                                        <thead>
                                                        <tr>
                                                            <th>Nome</th>
                                                            <th>CNPJ</th>
                                                            <th>Email</th>
                                                            <th style="text-align:center;">Qtd. Funcionários</th>
                                                            <th style="text-align:center;">Ações</th>
                                                        </tr>
                                                        </thead>

                                                        <tbody>
                                                        @forelse($empresas_IN as $empresa)
                                                            <tr>
                                                                <td class="icone_ativacao">
                                                                    @if($empresa->status == 'A')
                                                                        <i class="fas fa-user" style="font-size:10px; color: rgb(8, 179, 16)" title="Ativa"></i>&nbsp;
                                                                    @else
                                                                        <i class="fas fa-user-times" style="font-size:10px;color: rgb(218, 53, 53)" title="Inativa"></i>&nbsp;
                                                                    @endif
                                                                    {{$empresa->nome}}
                                                                </td>
                                                                <td class="mask_cnpj">{{$empresa->cnpj}}</td>
                                                                <td>{{$empresa->email}}</td>
                                                                <td style="text-align:center;">{{$empresa->empresa_funcionarios->count()}}</td>
                                                                <td style="text-align:center;">
                                                                    @can('edit_empresa')
                                                                        <a href="{{route('empresa.show', compact('empresa'))}}"><i class="fa fa-edit" style="color: goldenrod" title="Editar Empresa"></i></a>
                                                                    @endcan

                                                                    @can('delete_empresa')
                                                                        <a href="javascript:;" data-toggle="modal" onclick="deleteData({{$empresa->id}})"
                                                                            data-target="#modal-delete-empresa"><i class="fa fa-minus-circle" style="color: crimson" title="Excluir a Empresa"></i></a>
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

    @section('modal_target')"formSubmit();"@endsection
    @section('modal_type')@endsection
    @section('modal_name')"modal-delete-empresa"@endsection
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
		$(document).ready(function(){
            $('.mask_cnpj').inputmask('99.999.999/9999-99');
		});

       function deleteData(id)
       {
           var id = id;
           var url = '{{ route("empresa.destroy", ":id") }}';
           url = url.replace(':id', id);
           $("#deleteForm").attr('action', url);
       }

       function formSubmit()
       {
           $("#deleteForm").submit();
       }
    </script>

    @if($empresas_AT->count() > 0)
        <script>
            var table_AT = $('#dt_ativas').DataTable({
                language: {
                    url: '{{asset('nazox/assets/localisation/pt_br.json')}}'
                },
                "order": [[ 1, "asc" ]]
            });
    </script>
    @endif

    @if($empresas_IN->count() > 0)
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
