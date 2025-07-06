            <!-- ========== Left Sidebar Start ========== -->
            <div class="vertical-menu">

                <div data-simplebar class="h-100">

                    <!--- Sidemenu -->
                    <div id="sidebar-menu">
                        <!-- Left Menu Start -->

                        <ul class="metismenu list-unstyled" id="side-menu">


                            @if($user->roles->contains('name', 'Gestor'))
                            <!-- Menus Relacioandos a administração - Acesso somente para GESTOR - INICIO-->

                            <li class="menu-title">CADASTROS</li>
                            <li>
                                <a href="{{route('usuario.index')}}" class="waves-effect">
                                    <i class="ri-file-user-line"></i>
                                    <span>Usuários</span>
                                </a>
                            </li>
                            <li>
                                <a href="javascript: void(0);" class="has-arrow waves-effect">
                                    <i class="ri-store-2-line"></i>
                                    <span>Home</span>
                                </a>
                                <ul class="sub-menu" aria-expanded="false">
                                    <li><a href="{{route('empresa.index')}}">Empresas</a></li>
                                    <li><a href="{{route('campanha.index')}}">Campanhas</a></li>
                                </ul>
                            </li>

                            <li class="menu-title">GESTÃO</li>
                            <li>
                                <a href="{{route('empresa_funcionario.index')}}" class="waves-effect">
                                    <i class="ri-file-user-line"></i>
                                    <span>Empresas</span>
                                </a>
                            </li>
                            <!-- Menus Relacioandos a administração - Acesso somente para GESTOR - FIM-->
                            @endif


                            @if($user->roles->contains('name', 'Consultor'))
                            <!-- Menus Relacioandos a administração - Acesso somente para CONSULTOR - INICIO-->

                            <li class="menu-title">CADASTROS</li>
                            <li>
                                <a href="javascript: void(0);" class="has-arrow waves-effect">
                                    <i class="ri-store-2-line"></i>
                                    <span>Home</span>
                                </a>
                                <ul class="sub-menu" aria-expanded="false">
                                    <li><a href="{{route('empresa.index')}}">Empresas</a></li>
                                    <li><a href="{{route('campanha.index')}}">Campanhas</a></li>
                                </ul>
                            </li>

                            <li class="menu-title">GESTÃO</li>
                            <li>
                                <a href="{{route('empresa_funcionario.index')}}" class="waves-effect">
                                    <i class="ri-file-user-line"></i>
                                    <span>Empresas</span>
                                </a>
                            </li>

                            <!-- Menus Relacioandos a administração - Acesso somente para CONSULTOR - FIM-->
                            @endif


                            @if($user->roles->contains('name', 'Funcionario'))
                            <!-- Menus Relacioandos a administração - Acesso somente para FUNCINARIO - INICIO-->

                            <li class="menu-title">MINDRA</li>
                            <li>
                                <a href="{{ route('avaliacao.index') }}" class="waves-effect">
                                    <i class="ri-file-user-line"></i>
                                    <span>Avaliações</span>
                                </a>
                            </li>

                            <!-- Menus Relacioandos a administração - Acesso somente para CONSULTOR - FIM-->
                            @endif
                        </ul>

                    </div>
                    <!-- Sidebar -->
                </div>
            </div>
            <!-- Left Sidebar End -->

