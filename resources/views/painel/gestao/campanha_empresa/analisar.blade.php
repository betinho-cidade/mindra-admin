@extends('painel.layout.index')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0">Análise da Campanha</h4>
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

<table>
    <tr>
        <th>formulario_pergunta_id</th>
        @foreach ($respostaIds as $id)
            <th>{{ $id }}</th>
        @endforeach
    </tr>
    @foreach ($pivoted as $key => $row)
        <tr>
            <td>{{ $key === 'total' ? $row['total'] : $key }}</td>
            @foreach ($respostaIds as $id)
                <td>{{ $row[$id] ?? 0 }}</td>
            @endforeach
        </tr>
    @endforeach
</table>


@endsection


@section('script-js')
@endsection
