@extends('painel.layout.index')

@section('content')

    @if(session()->has('message.level'))
        <div class="row">
            <div class="col-12">
                <div class="alert alert-{{ session('message.level') }}">
                    {!! session('message.content') !!}
                </div>
            </div>
        </div>
    @endif

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0">Dashboard - Funcionário</h4>
        </div>
    </div>
</div>
<!-- end page title -->

@endsection

@section('head-css')
@endsection

@section('script-js')
@endsection

