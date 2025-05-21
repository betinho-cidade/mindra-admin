<!doctype html>
<html lang="en">

    <head>

        <meta charset="utf-8" />
        <title>Mindra | Sistema de Aprendizagem</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Mindra | Sistema de Aprendizagem" name="description" />
        <meta content="Mindra" name="Mindra" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="{{asset('nazox/assets/images/favicon.ico')}}">

        <!-- Bootstrap Css -->
        <link href="{{asset('nazox/assets/css/bootstrap.min.css')}}" id="bootstrap-style" rel="stylesheet" type="text/css" />
        <!-- Icons Css -->
        <link href="{{asset('nazox/assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
        <!-- App Css-->
        <link href="{{asset('nazox/assets/css/app.min.css')}}" id="app-style" rel="stylesheet" type="text/css" />
        <!-- Cityinbag Css-->
        <link href="{{asset('css/cityinbag.css')}}" id="app-style" rel="stylesheet" type="text/css" />

    </head>

    <body class="auth-body-bg">
        <div>
            <div class="container-fluid p-0">
                <div class="row no-gutters">
                    <div class="col-lg-4">
                        <div class="authentication-page-content p-4 d-flex align-items-center min-vh-100">
                            <div class="w-100">
                                <div class="row justify-content-center">
                                    <div class="col-lg-9">
                                        <div>
                                            <div class="text-center">
                                                <div>
                                                    <img src="{{asset('nazox/assets/images/logo-dark.png')}}" height="90" alt="logo">
                                                </div>

                                                <h4 class="font-size-18 mt-4">Bem-vindo!</h4>
                                                <p class="text-muted">Faça seu login.</p>
                                            </div>

                                            <div class="p-2 mt-5">
                                                <form class="form-horizontal" method="POST" action="{{route('login')}}">
                                                    @csrf

                                                    <div class="form-group auth-form-group-custom mb-4">
                                                        <i class="ri-user-2-line auti-custom-input-icon"></i>
                                                        <label for="cpf">CPF</label>
                                                        <input type="text" class="form-control @error('cpf') is-invalid @enderror" id="cpf" name="cpf" placeholder="Informe o CPF">
                                                        @error('cpf')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>

                                                    <div class="form-group auth-form-group-custom mb-4">
                                                        <i class="ri-lock-2-line auti-custom-input-icon"></i>
                                                        <label for="password">Senha</label>
                                                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Informe a senha">
                                                        @error('password')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>

                                                    <div class="mt-4 text-center">
                                                        <button class="btn btn-primary w-md waves-effect waves-light" type="submit">Log In</button>
                                                    </div>

                                                    {{-- <div class="mt-4 text-center">
                                                        <a href="auth-recoverpw.html" class="text-muted"><i class="mdi mdi-lock mr-1"></i> Forgot your password?</a>
                                                    </div> --}}
                                                </form>
                                            </div>

                                            <div class="mt-5 text-center">
                                                <p><script>document.write(new Date().getFullYear())</script> © Mindra <br>Desenvolvido por <a href="https://cityinbag.com.br/" target="_blank">Cityinbag</a>.</p>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="authentication-bg" style="background-position: center left !important;">
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <!-- JAVASCRIPT -->
        <script src="{{asset('nazox/assets/libs/jquery/jquery.min.js')}}"></script>
        <script src="{{asset('nazox/assets/libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
        <script src="{{asset('nazox/assets/libs/metismenu/metisMenu.min.js')}}"></script>
        <script src="{{asset('nazox/assets/libs/simplebar/simplebar.min.js')}}"></script>
        <script src="{{asset('nazox/assets/libs/node-waves/waves.min.js')}}"></script>

        <script src="{{asset('nazox/assets/js/app.js')}}"></script>

    </body>
</html>
