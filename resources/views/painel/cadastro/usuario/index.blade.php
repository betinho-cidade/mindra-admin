@extends('painel.layout.index')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0">Usuários do Sistema</h4>
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

<div class="row tab-filter">


    <div class="col-md-12">
        <div class="card">
            <div class="card-body" style="padding:0;">
                <!-- Right Sidebar -->
                    <div class="card" style="margin-bottom: 0;">
                        <div class="card-body">

                            <span class="float-right">
                                @can('create_usuario')
                                    <a href="{{route("usuario.create")}}" class="btn btn-outline-secondary waves-effect">Novo Usuário</a>
                                @endcan
                            </span>

                            <div class="tab-content py-4" style="padding-bottom:0 !important;">
                                <div class="tab-pane show active" id="pendente">
                                    <div>
                                        <h5 class="px-3 mb-3" style="text-align: left; margin-top: -15px; padding-left: 0 !important; margin-bottom: 25px !important;">Listagem de Usuários</h1>
                                        @php $count = 0; @endphp
                                        <span style="display: block;margin-bottom: 10px;">
                                            <form name="search_usuario" method="GET" action="{{route('usuario.search')}}"  class="needs-validation" novalidate>
                                                @csrf
                                                        <!-- CAMPOS DE BUSCA - INI -->
                                                        <div class="row">
                                                            <div class="col-md-3" style="padding-right: 0;">
                                                                <div class="form-group">
                                                                    <input type="text" class="form-control" id="nome" name="nome" placeholder="Nome" style="height: 30px;">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3" style="padding-right: 0;">
                                                                <div class="form-group">
                                                                    <input type="text" class="form-control" id="email" name="email" placeholder="E-mail" style="height: 30px;">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3" style="padding-right: 0;">
                                                                <div class="form-group">
                                                                    <input type="text" class="form-control mask_cpf" id="cpf" name="cpf" placeholder="CPF" style="height: 30px;">
                                                                </div>
                                                            </div>

                                                            <div class="col-md-3">
                                                                <button style="font-size: 14px; line-height: 14px; width: 100%; margin-bottom: 10px;" type="submit" class="btn btn-primary waves-effect waves-light">Filtrar</button>
                                                            </div>

                                                        </div>

                                                </form>
                                                <!-- FILTROS DE PESQUISA - FIM -->


                                        @if($excel_params)
                                            @foreach ($excel_params as $param=>$value )
                                                @if($value)
                                                    @if($count == 0)
                                                        <code style="font-size:14px;">Filtro:</code>
                                                    @endif
                                                    <code style="font-size:14px;">[{{ $excel_params_translate[$param] }}:
                                                        @switch($param)
                                                            @case("nome") {{ $value }} @break
                                                            @default {{ $value }}
                                                        @endswitch
                                                    ]&nbsp;</code>
                                                    @php $count = $count + 1; @endphp
                                                @endif
                                            @endforeach
                                            </code>
                                        @endif
                                        </span>

                                        <ul class="nav nav-tabs" role="tablist">
                                            <li class="nav-item" title="Lista de Usuários">
                                                <a class="nav-link active" data-toggle="tab" href="#lista_usuarios" role="tab">
                                                    <span class="d-sm-block">Usuários ( <code class="highlighter-rouge">{{($usuarios) ? $usuarios->count() : 0}}</code> )</span>
                                                </a>
                                            </li>

                                            @if($usuarios)
                                                <span class="float-right" style="font-size: 12px;padding-top: 8px;margin-left: -4px;">
                                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                    Registros: {{ ($usuarios->lastItem()) ? $usuarios->lastItem() : 0}} / {{ $usuarios->total() }} &nbsp;&nbsp;&nbsp;
                                                    Página: {{ $usuarios->currentPage() }} / {{ $usuarios->lastPage() }} &nbsp;&nbsp;&nbsp;
                                                    @if($usuarios->previousPageUrl()) <a href="{{ $usuarios->previousPageUrl() . '&' . http_build_query($excel_params)}}"> <i class="mdi mdi-skip-previous" style="font-size: 16px;" title="Anterior"></i>  </a> @else <i class="mdi mdi-dots-horizontal" style="font-size: 16px;" title="..."></i> @endif
                                                    @if($usuarios->hasMorePages()) <a href="{{ $usuarios->nextPageUrl() . '&' . http_build_query($excel_params)}}"> <i class="mdi mdi-skip-next" style="font-size: 16px;" title="Próximo"></i>  </a> @else <i class="mdi mdi-dots-horizontal" style="font-size: 16px;" title="..."></i> @endif
                                                </span>
                                                <br>
                                            @endif
                                        </ul>

                                        <!-- Tab panes -->
                                        <div class="tab-content text-muted">

                                            <!-- Lista Usuarios - INI -->
                                            <div class="tab-pane active" id="lista_usuarios" role="tabpanel">
                                                <ul class="list-unstyled chat-list" data-simplebar>
                                                    <table id="dt_lista_usuarios" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                                        <thead>
                                                        <tr>
                                                            <th>Nome</th>
                                                            <th>CPF</th>
                                                            <th>Email</th>
                                                            <th style="text-align:center;">Perfil</th>
                                                            <th style="text-align:center;">Ações</th>
                                                        </tr>
                                                        </thead>

                                                        <tbody>
                                                        @if($usuarios)
                                                            @forelse($usuarios as $usuario)
                                                            <tr>
                                                                <td class="icone_ativacao">
                                                                    @if($usuario->situacao['status'] == 'A')
                                                                        <i class="fas fa-user" style="font-size:10px; color: rgb(8, 179, 16)" title="Ativo"></i>&nbsp;
                                                                    @else
                                                                        <i class="fas fa-user-times" style="font-size:10px;color: rgb(218, 53, 53)" title="Inativo"></i>&nbsp;
                                                                    @endif
                                                                    {{$usuario->nome}}
                                                                </td>
                                                                <td class="mask_cpf">{{$usuario->cpf}}</td>
                                                                <td>{{$usuario->email}}</td>
                                                                <td style="text-align:center;">{{$usuario->perfil}}</td>
                                                                <td style="text-align:center;">
                                                                    @can('edit_usuario')
                                                                        <a href="{{route('usuario.show', compact('usuario', 'excel_params'))}}"><i class="fa fa-edit" style="color: goldenrod" title="Editar Usuário"></i></a>
                                                                    @endcan

                                                                    @can('delete_usuario')
                                                                        <a href="javascript:;" data-toggle="modal" onclick="deleteData({{$usuario->id}})"
                                                                            data-target="#modal-delete-usuario"><i class="fa fa-minus-circle" style="color: crimson" title="Excluir o Usuário"></i></a>
                                                                    @endcan

                                                                    </td>
                                                            </tr>
                                                            @empty
                                                            <tr>
                                                                <td colspan="5">Nenhum registro encontrado</td>
                                                            </tr>
                                                            @endforelse
                                                        @else
                                                            <td colspan="5">Utilize o filtro ao lado para realizar a busca.</td>
                                                        @endif
                                                        </tbody>
                                                    </table>
                                                </ul>
                                            </div>
                                            <!-- Lista Usuários - FIM -->
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
    <input type="hidden" class="form-control" id="excel_params_set" name="excel_params_set" value="delete">
    <input type="hidden" class="form-control" id="excel_params" name="excel_params" value="{{json_encode($excel_params)}}">

    </form>
    @section('modal_target')"formSubmit();"@endsection
    @section('modal_type')@endsection
    @section('modal_name')"modal-delete-usuario"@endsection
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
            $('.mask_cpf').inputmask('999.999.999-99');
		});


       function deleteData(id)
       {
           var id = id;
           var url = '{{ route("usuario.destroy", ":id") }}';
           url = url.replace(':id', id);
           $("#deleteForm").attr('action', url);
       }

       function formSubmit()
       {
           $("#deleteForm").submit();
       }
    </script>

@endsection
