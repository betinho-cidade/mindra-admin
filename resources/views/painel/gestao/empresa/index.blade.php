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

                            <div class="tab-content py-4">
                                <div class="tab-pane show active" id="ativas">
                                    <div>
                                        <h5 class="px-3 mb-3" style="text-align: left; margin-top: -15px; padding-left: 0 !important; margin-bottom: 25px !important;">Listagem de Empresas</h1>

                                        <ul class="nav nav-tabs" role="tablist">
                                            <li class="nav-item" title="Lista de Empresas Ativas">
                                                <a class="nav-link active" data-toggle="tab" href="#ativas" role="tab">
                                                    <span class="d-sm-block">Empresas( <code class="highlighter-rouge">{{$empresas->count()}}</code> )</span>
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
                                                        @forelse($empresas as $empresa)
                                                            <tr>
                                                                <td>{{$empresa->nome}}</td>
                                                                <td class="mask_cnpj">{{$empresa->cnpj}}</td>
                                                                <td>{{$empresa->email}}</td>
                                                                <td style="text-align:center;">{{$empresa->empresa_funcionarios->count()}}</td>
                                                                <td style="text-align:center;">
                                                                    @can('edit_empresa_funcionario')
                                                                        <a href="{{route('empresa_funcionario.show', compact('empresa'))}}"><i class="fa fa-edit" style="color: goldenrod" title="Editar Empresa"></i></a>
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
    </script>

    @if($empresas->count() > 0)
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
