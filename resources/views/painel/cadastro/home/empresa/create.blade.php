@extends('painel.layout.index')


@section('content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0">Nova Empresa do Sistema</h4>
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

            <h4 class="card-title">Formulário de Cadastro - Empresa</h4>
            <p class="card-title-desc">A Empresa cadastrada poderá acessar ao sistema e realizar as ações necessárias através de um dos seus consultores vinculados.</p>
            <form name="create_empresa" method="POST" action="{{route('empresa.store')}}"  class="needs-validation"  accept-charset="utf-8" enctype="multipart/form-data" novalidate>
                @csrf

                <!-- Dados Pessoais - INI -->
                <div class="bg-soft-primary p-3 rounded" style="margin-bottom:10px;">
                    <h5 class="text-primary font-size-14" style="margin-bottom: 0px;">Dados Empresa</h5>
                </div>

                    <div class="row">
                        <div class="col-md-2">
                            <label for="cnpj">CNPJ</label>
                            <img src="{{asset('images/loading.gif')}}" id="img-loading-cnpj" style="display:none;max-width: 17%; margin-left: 26px;">
                            <input type="text" name="cnpj" id="cnpj" class="form-control mask_cnpj dynamic_cnpj" value="{{old('cnpj')}}" placeholder="99.999.999/9999-99" required>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="nome">Nome</label>
                                <input type="text" class="form-control" id="nome" name="nome" value="{{old('nome')}}" placeholder="Nome" required>
                                <div class="valid-feedback">ok!</div>
                                <div class="invalid-feedback">Inválido!</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="email">E-mail</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{old('email')}}" placeholder="E-mail">
                                <div class="valid-feedback">ok!</div>
                                <div class="invalid-feedback">Inválido!</div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="telefone">Telefone</label>
                            <input type="text" name="telefone" id="telefone" class="form-control mask_telefone" value="{{old('telefone')}}" placeholder="(99) 99999-9999">
                        </div>
                    </div>
                    <p></p>
                    <div class="row">
                        <div class="col-md-4">
                            <label for="responsavel_nome">Nome Responsável</label>
                            <input type="text" name="responsavel_nome" id="responsavel_nome" class="form-control" value="{{old('responsavel_nome')}}" placeholder="">
                        </div>
                        <div class="col-md-2">
                            <label for="responsavel_telefone">Telefone Responsável</label>
                            <input type="text" name="responsavel_telefone" id="responsavel_telefone" class="form-control mask_telefone" value="{{old('responsavel_telefone')}}" placeholder="(99) 99999-9999">
                        </div>
                        <div class="col-md-2">
                            <label for="inscricao_estadual">Inscrição Estadual</label>
                            <input type="text" name="inscricao_estadual" id="inscricao_estadual" class="form-control" value="{{old('inscricao_estadual')}}">
                        </div>
                        <div class="col-md-2">
                            <label for="qtd_funcionario">Qtd Funcionários</label>
                            <input type="number" name="qtd_funcionario" id="qtd_funcionario" class="form-control" value="{{old('qtd_funcionario')}}" placeholder="">
                        </div>
                        <div class="col-md-2">
                            <label for="data_abertura">Data Abertura</label>
                            <input type="date" name="data_abertura" id="data_abertura" class="form-control" value="{{old('data_abertura')}}">
                        </div>
                    </div>
                    <p></p>
                    <div class="row">
                        <div class="col-md-5">
                            <label for="atividade_principal">Atividade Principal</label>
                            <textarea rows="1" name="atividade_principal" id="atividade_principal" class="form-control">{{old('atividade_principal')}}</textarea>
                        </div>
                        <div class="col-md-3">
                            <label for="site">Site</label>
                            <input type="url" name="site" id="site" class="form-control" value="{{old('site')}}" placeholder="">
                        </div>
                        <div class="col-md-2">
                            <label for="num_contrato">Número Contrato</label>
                            <input type="text" name="num_contrato" id="num_contrato" class="form-control" value="{{old('num_contrato')}}">
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

                    <div class="row">
                        <div class="col-md-4">
                            <label for="path_imagem">Imagem</label>
                            <div class="form-group custom-file">
                                <input type="file" class="custom-file-input" id="path_imagem" name="path_imagem"
                                    accept="image/*" required>
                                <label class="custom-file-label" for="path_imagem">Selecionar Imagem</label>
                                <div class="valid-feedback">ok!</div>
                                <div class="invalid-feedback">Inválido!</div>
                            </div>
                        </div>
                    </div>


                    <p></p>
                <!-- Dados Pessoais - FIM -->

                <!-- Dados Endereço - INI -->
                <div class="bg-soft-primary p-3 rounded" style="margin-bottom:10px;">
                    <h5 class="text-primary font-size-14" style="margin-bottom: 0px;">Dados Endereço</h5>
                </div>
                    <div class="row">
                        <div class="col-md-2">
                            <label for="end_cep">CEP</label>
                            <img src="{{asset('images/loading.gif')}}" id="img-loading-cep" style="display:none;max-width: 17%; margin-left: 26px;">
                            <input type="text" name="end_cep" id="end_cep" class="form-control dynamic_cep mask_cep" value="{{old('end_cep')}}" placeholder="99.999-999">
                        </div>

                        <div class="col-md-4">
                            <label for="end_cidade">Cidade</label>
                            <input type="text" name="end_cidade" id="end_cidade" class="form-control" value="{{old('end_cidade')}}">
                        </div>

                        <div class="col-md-2">
                            <label for="end_uf">Estado</label>
                            <input type="text" name="end_uf" id="end_uf" class="form-control" value="{{old('end_uf')}}">
                        </div>

                        <div class="col-md-4">
                            <label for="end_bairro">Bairro</label>
                            <input type="text" name="end_bairro" id="end_bairro" class="form-control" value="{{old('end_bairro')}}">
                        </div>
                    </div>
                    <p></p>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="end_endereco">Endereço</label>
                            <input type="text" name="end_logradouro" id="end_logradouro" class="form-control" value="{{old('end_logradouro')}}">
                        </div>

                        <div class="col-md-2">
                            <label for="end_numero">Número</label>
                            <input type="text" name="end_numero" id="end_numero" value="{{old('end_numero')}}" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label for="end_complemento">Complemento </label>
                            <input type="text" name="end_complemento" id="end_complemento" class="form-control" value="{{old('end_complemento')}}">
                        </div>
                    </div>
                    <p></p>
                <!-- Dados Endereço - FIM -->

                {{-- <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="invalidCheck" required>
                                <label class="custom-control-label" for="invalidCheck">Aceito os termos e condições acima</label>
                                <div class="invalid-feedback">
                                    Você deve aceitar os termos antes de enviar o formulário.
                                </div>
                            </div>
                        </div>
                    </div>
                </div> --}}
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

    <script>
		$(document).ready(function(){
			$('.mask_cep').inputmask('99.999-999');
            $('.mask_cnpj').inputmask('99.999.999/9999-99');
            $('.mask_telefone').inputmask('(99) 99999-9999');
            $('.select2').select2();
		});
	</script>

    <script type='text/javascript'>
        $(document).ready(function(){
            $('.dynamic_cep').change(function(){

                if ($(this).val() != ''){
                    document.getElementById("img-loading-cep").style.display = '';

                    var cep = $('#end_cep').val();
                    var _token = $('input[name="_token"]').val();

                    $('#end_logradouro').val('');
                    $('#end_complemento').val('');
                    $('#end_numero').val('');
                    $('#end_bairro').val('');
                    $('#end_cidade').val('');
                    $('#end_uf').val('');

                    $.ajax({
                        url: "{{route('painel.js_viacep')}}",
                        method: "POST",
                        data: {_token:_token, cep:cep},
                        success:function(result){
                            dados = JSON.parse(result);
                            if(dados==null || dados['error'] == 'true'){
                                    console.log(dados);
                            } else{
                                    $('#end_logradouro').val(dados['logradouro']);
                                    $('#end_complemento').val(dados['complemento']);
                                    $('#end_bairro').val(dados['bairro']);
                                    $('#end_cidade').val(dados['localidade']);
                                    $('#end_uf').val(dados['uf']);
                            }
                            document.getElementById("img-loading-cep").style.display = 'none';
                        },
                        error:function(erro){
                            document.getElementById("img-loading-cep").style.display = 'none';
                        }
                    })
                }
            });
        });

        $('.dynamic_cnpj').change(function(){

            if ($(this).val() != ''){
                var cnpj = $('#cnpj').val();
                var _token = $('input[name="_token"]').val();

                $('#nome').val('');
                $('#email').val('');
                $('#telefone').val('');
                $('#end_cep').val('');
                $('#end_cidade').val('');
                $('#end_uf').val('');
                $('#end_bairro').val('');
                $('#end_logradouro').val('');
                $('#end_numero').val('');
                $('#end_complemento').val('');

                document.getElementById("img-loading-cnpj").style.display = '';

                $.ajax({
                    url: "{{route('painel.js_cnpj')}}",
                    method: "POST",
                    data: {_token:_token, cnpj:cnpj},
                    success:function(result){
                        dados = JSON.parse(result);

                        if(dados==null || dados['status'] == 'ERROR'){
                                console.log(dados);
                        } else{
                                $('#nome').val(dados['nome']);
                                $('#email').val(dados['email']);

                                data_abertura = dados['abertura'];

                                if(data_abertura){
                                    data_array = data_abertura.split('/');
                                    new_data_abertura = data_array[2] + '-' + data_array[1] + '-' + data_array[0];
                                    $('#data_abertura').val(new_data_abertura);
                                }

                                telefone = dados['telefone'];

                                if(telefone.includes("/")){
                                    telefone = telefone.substring(0, telefone.indexOf("/"));
                                    $('#telefone').val(telefone.replace(/[^0-9]+/g, ""));
                                }else{
                                    $('#telefone').val(telefone.replace(/[^0-9]+/g, ""));
                                }

                                if(dados['atividade_principal'][0]){
                                    $('#atividade_principal').val(dados['atividade_principal'][0]['text']);
                                }

                                socios = dados['qsa'];

                                if(socios){
                                    for(let i = 0; i < socios.length; i = i + 1 ) {
                                        if(socios[i]['nome_rep_legal']){
                                            $('#responsavel_nome').val(socios[i]['nome_rep_legal']);
                                            break;
                                        }
                                    }
                                }

                                $('#end_cep').val(dados['cep'].replace(/[^0-9]+/g, ""));
                                $('#end_cidade').val(dados['municipio']);
                                $('#end_uf').val(dados['uf']);
                                $('#end_bairro').val(dados['bairro']);
                                $('#end_logradouro').val(dados['logradouro']);
                                $('#end_numero').val(dados['numero']);
                                $('#end_complemento').val(dados['complemento']);
                        }
                        document.getElementById("img-loading-cnpj").style.display = 'none';
                    },
                    error:function(erro){
                        document.getElementById("img-loading-cnpj").style.display = 'none';
                    }
                });
            }
        });
    </script>

@endsection
