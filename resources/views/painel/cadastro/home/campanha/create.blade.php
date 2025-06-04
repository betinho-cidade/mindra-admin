@extends('painel.layout.index')


@section('content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0">Nova Campanha do Sistema</h4>
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

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
            <!-- FORMULÁRIO - INICIO -->

            <h4 class="card-title">Formulário de Cadastro - Campanha</h4>
            <p class="card-title-desc">A Campanha cadastrada será utilizada para acesso pelos funcionários no preenchimento do formulário de avaliação.</p>
            <form name="create_campanha" method="POST" action="{{route('campanha.store')}}"  class="needs-validation"  accept-charset="utf-8" enctype="multipart/form-data" novalidate>
                @csrf

                <!-- Dados Pessoais - INI -->
                <div class="bg-soft-primary p-3 rounded" style="margin-bottom:10px;">
                    <h5 class="text-primary font-size-14" style="margin-bottom: 0px;">Dados Campanha</h5>
                </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="empresa">Empresa</label>
                                <select id="empresa" name="empresa" class="form-control" required>
                                    <option value="">---</option>
                                    @foreach ($empresas as $empresa)
                                        <option value="{{ $empresa->id }}"
                                            {{ $empresa->id == old('empresa') ? 'selected' : '' }}>{{ $empresa->nome }}</option>
                                    @endforeach
                                </select>
                                <div class="valid-feedback">ok!</div>
                                <div class="invalid-feedback">Inválido!</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="titulo">Título</label>
                                <input type="text" class="form-control" id="titulo" name="titulo" value="{{old('titulo')}}" placeholder="Título" required>
                                <div class="valid-feedback">ok!</div>
                                <div class="invalid-feedback">Inválido!</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="descricao">Descrição</label>
                                <textarea rows="2" class="form-control" id="descricao" name="descricao" placeholder="Descrição">{{old('descricao')}}</textarea>
                                <div class="valid-feedback">ok!</div>
                                <div class="invalid-feedback">Inválido!</div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="formulario">Formulário
                                    <a href="javascript:;" onclick="preview_formulario()">
                                        <i class="mdi mdi-view-grid-plus" style="color: goldenrod" title="Preview Formulário"></i>
                                    </a>
                                </label>
                                <select id="formulario" name="formulario" class="form-control" required>
                                    <option value="">---</option>
                                    @foreach ($formularios as $formulario)
                                        <option value="{{ $formulario->id }}"
                                            {{ $formulario->id == old('formulario') ? 'selected' : '' }}>{{ $formulario->titulo }}</option>
                                    @endforeach
                                </select>
                                <div class="valid-feedback">ok!</div>
                                <div class="invalid-feedback">Inválido!</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="data_inicio">Data Abertura</label>
                            <input type="date" name="data_inicio" id="data_inicio" class="form-control" value="{{old('data_inicio')}}" required>
                        </div>
                        <div class="col-md-3">
                            <label for="data_fim">Data Fim</label>
                            <input type="date" name="data_fim" id="data_fim" class="form-control" value="{{old('data_fim')}}" required>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="situacao">Situação</label>
                                <select id="situacao" name="situacao" class="form-control" required>
                                    <option value="">---</option>
                                    <option value="A" {{(old('situacao') == 'A') ? 'selected' : '' }}>Ativo</option>
                                    <option value="I" {{(old('situacao') == 'I') ? 'selected' : '' }}>Inativo</option>
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

            <!-- FORMULÁRIO - FIM -->
            </div>
        </div>
    </div>
</div>

<form action="" id="previewForm" method="post" target="_blank">
    @csrf
</form>

@endsection


@section('script-js')
    <script src="{{asset('nazox/assets/js/pages/form-validation.init.js')}}"></script>
    <script src="{{asset('nazox/assets/libs/bs-custom-file-input/bs-custom-file-input.min.js')}}"></script>
    <script src="{{asset('nazox/assets/js/pages/form-element.init.js')}}"></script>

    <script>

        function preview_formulario(){
            formulario = $('#formulario').val();
            if(formulario){
                var url = '{{ route('painel.preview_formulario', [':formulario']) }}';
                url = url.replace(':formulario', formulario);
                $("#previewForm").attr('action', url);
                $("#previewForm").submit();
            }
        }

    </script>

@endsection
