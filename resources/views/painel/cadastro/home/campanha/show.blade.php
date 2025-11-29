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
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="empresa">Empresa</label>
                                <input type="text" class="form-control"value="{{$campanha->empresa->nome}}" disabled>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="titulo">Título</label>
                                <input type="text" class="form-control" id="titulo" name="titulo" value="{{$campanha->titulo}}" placeholder="Título" required>
                                <div class="valid-feedback">ok!</div>
                                <div class="invalid-feedback">Inválido!</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="formulario">Formulário</label>
                                <select class="form-control">
                                    <option>{{ $campanha->formulario->titulo }}</option>
                                </select>
                            </div>
                        </div>                        
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="checklist">Checklist Consultor</label>
                                <select class="form-control">
                                    <option>{{ $campanha->checklist->titulo }}</option>
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
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="descricao">Descrição</label>
                                <textarea rows="2" class="form-control" id="descricao" name="descricao" placeholder="Descrição">{{$campanha->descricao}}</textarea>
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

@endsection


@section('script-js')
    <script src="{{asset('nazox/assets/js/pages/form-validation.init.js')}}"></script>
    <script src="{{asset('nazox/assets/libs/bs-custom-file-input/bs-custom-file-input.min.js')}}"></script>
    <script src="{{asset('nazox/assets/js/pages/form-element.init.js')}}"></script>
    <!-- form mask -->
    <script src="{{asset('nazox/assets/libs/inputmask/jquery.inputmask.min.js')}}"></script>

@endsection
