
<!doctype html>
<html lang="en">

    <head>

        <meta charset="utf-8" />
        <title>Mindra | Sistema de Avaliações Comportamentais</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Mindra | Sistema de Avaliações Comportamentais" name="description" />
        <meta content="Mindra" name="Mindra" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="assets/images/favicon.ico">

        <!-- Bootstrap Css -->
        <link href="{{asset('nazox/assets/css/bootstrap.min.css')}}" id="bootstrap-style" rel="stylesheet" type="text/css" />
        <!-- Icons Css -->
        <link href="{{asset('nazox/assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
        <!-- App Css-->
        <link href="{{asset('nazox/assets/css/app.min.css')}}" id="app-style" rel="stylesheet" type="text/css" />
        <!-- Cityinbag - CSS -->
        <link href="{{asset('css/cityinbag.css')}}" id="app-style" rel="stylesheet" type="text/css" />

        <!-- CSS PERSONALIZADO CADASTRO CITYINBAG -->

        <style type="text/css">
            .btn-primary{background-color: #000 !important;border-color: #000 !important;}
            .btn-primary:hover, .btn-primary:not(:disabled):not(.disabled).active, .btn-primary:not(:disabled):not(.disabled):active, .show>.btn-primary.dropdown-toggle{    box-shadow: none;background-color: #750185 !important;border-color: #750185 !important;}

            .bg-soft-primary {background-color: #fef68c !important;}
            .text-primary, .page-title-box h4, .card-title, label{  color: #000!important;}

            body[data-sidebar=dark] .navbar-brand-box {background: #1d2020;}
            body[data-sidebar=dark] .vertical-menu{background: #2f3131;}

        </style>


    </head>

    <body data-sidebar="dark">

        <!-- Begin page -->
        <div id="layout-wrapper">

            <header id="page-topbar">
                <div class="navbar-header">
                    <div class="d-flex">
                        <!-- LOGO -->
                        <div class="navbar-brand-box">
                            <a href="{{route('login')}}" class="logo logo-dark">
                                <span class="logo-sm">
                                    <img src="{{asset('nazox/assets/images/logo-sm-dark.png')}}" alt="" height="22">
                                </span>
                                <span class="logo-lg">
                                    <img src="{{asset('nazox/assets/images/logo-dark.png')}}" alt="" height="20">
                                </span>
                            </a>

                            <a href="{{route('login')}}" class="logo logo-light">
                                <span class="logo-sm">
                                    <img src="{{asset('nazox/assets/images/logo-sm-light.png')}}" alt="" height="22">
                                </span>
                                <span class="logo-lg">
                                    <img src="{{asset('nazox/assets/images/logo-light.png')}}" alt="" height="31">
                                </span>
                            </a>
                        </div>

                    </div>
                </div>
            </header>

            <!-- ========== Left Sidebar Start ========== -->
            <div class="vertical-menu">

                <div data-simplebar class="h-100">

                    <!--- Sidemenu -->
                    <div id="sidebar-menu">
                        <!-- Left Menu Start -->
                        <ul class="metismenu list-unstyled" id="side-menu">
                            <li>
                                <a href="index.html" class="waves-effect" style="cursor: default;color: #ffffff;pointer-events: none;">
                                    <i class="ri-lock-line" style="color: #ffffff;"></i><span class="badge badge-pill badge-success float-right"></span>
                                    <span>Troque sua senha</span>
                                </a>
                            </li>
                            <li>
                                <a style="padding: .625rem 1rem;  color: #b7b7b7;cursor: default;pointer-events: none;">
                                    <span>Para completar o processo, informe o e-mail que solicitou a troca de senha e na sequência a nova senha que gostaria de alterar.</span>
                                </a>
                            </li>


                        </ul>
                    </div>
                    <!-- Sidebar -->
                </div>
            </div>
            <!-- Left Sidebar End -->


            <!-- ============================================================== -->
            <!-- Start right Content here -->
            <!-- ============================================================== -->
            <div class="main-content">

                <div class="page-content">
                    <div class="container-fluid">

                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-flex align-items-center justify-content-between">
                                <h4 class="mb-0">Agora falta pouco!</h4>
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

                                <h4 class="card-title">TROQUE SUA SENHA...</h4>
                                <p class="card-title-desc">Informe seu e-mail e nova senha!</p>
                                <form name="reset_password" method="POST" action="{{route('reset')}}"  class="needs-validation" novalidate>
                                    @csrf

                                        <input type="hidden" id="token" name="token" value="{{ $token }}">

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="email">E-mail</label>
                                                    <input type="email" class="form-control" id="email" name="email" value="{{old('email')}}" required>
                                                    <div class="valid-feedback">ok!</div>
                                                    <div class="invalid-feedback">Inválido!</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="password">Nova Senha</label>
                                                    <input type="password" class="form-control" id="password" name="password" required>
                                                    <div class="valid-feedback">ok!</div>
                                                    <div class="invalid-feedback">Inválido!</div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="newPassword">Confirmação de Senha</label>
                                                    <input type="password" class="form-control" id="newPassword" name="newPassword" required>
                                                    <div class="valid-feedback">ok!</div>
                                                    <div class="invalid-feedback">Inválido!</div>
                                                </div>
                                            </div>
                                        </div>

                                    <button class="btn btn-primary w-md waves-effect waves-light" style="background: #000 !important; border-color: #000 !important;" type="submit">Gerar nova senha</button>
                                </form>

                                <!-- FORMULÁRIO - FIM -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CADASTRO DE NOVO USUARIO - FIM -->
                    </div> <!-- container-fluid -->
                </div>
                <!-- End Page-content -->


                <footer class="footer">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-6">
                                2014-<script>document.write(new Date().getFullYear())</script> © Cityinbag.
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
            <!-- end main content-->

        </div>
        <!-- END layout-wrapper -->

        <!-- JAVASCRIPT -->
        <script src="{{asset('nazox/assets/libs/jquery/jquery.min.js')}}"></script>
        <script src="{{asset('nazox/assets/libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
        <script src="{{asset('nazox/assets/libs/metismenu/metisMenu.min.js')}}"></script>
        <script src="{{asset('nazox/assets/libs/simplebar/simplebar.min.js')}}"></script>
        <script src="{{asset('nazox/assets/js/app.js')}}"></script>

        <script src="{{asset('nazox/assets/js/pages/form-validation.init.js')}}"></script>
        <script src="{{asset('nazox/assets/libs/bs-custom-file-input/bs-custom-file-input.min.js')}}"></script>
        <script src="{{asset('nazox/assets/js/pages/form-element.init.js')}}"></script>
        <!-- form mask -->
        <script src="{{asset('nazox/assets/libs/inputmask/jquery.inputmask.min.js')}}"></script>

    </body>
</html>














