<!doctype html>
<html lang="pt">

	<!-- Cityinbag - Head INI -->
    @includeif('painel.layout.head')
	<!-- Cityinbag - Head FIM-->

    <body data-sidebar="dark" 
        @if($user) 
            class="{{($user->menu_aberto == 'S') ? 'sidebar-enable vertical-collpsed' : ''}}"
        @endif
    >

        <!-- Loader -->
        {{-- <div id="preloader">
            <div id="status">
                <div class="spinner">
                    <i class="ri-loader-line spin-icon"></i>
                </div>
            </div>
        </div> --}}

        <!-- Begin page -->
        <div id="layout-wrapper">

		    <!-- Cityinbag - SIDEBAR_HEADER INI -->
		    @includeif('painel.layout.sidebar_header')
		    <!-- Cityinbag - SIDEBAR_HEADER FIM-->

	        <!-- Cityinbag - SIDEBAR_LEFT INI -->
			@includeif('painel.layout.sidebar_left')
			<!-- Cityinbag - SIDEBAR_LEFT FIM-->


            <!-- ============================================================== -->
            <!-- Start right Content here -->
            <!-- ============================================================== -->
            <div class="main-content">

			    <!-- Cityinbag - CONTENT INI -->
                <div class="page-content">
                    <div class="container-fluid">
						@yield('content')
                    </div>
                </div>
			    <!-- Cityinbag - CONTENT FIM-->
				
				<!-- Cityinbag - FOOTER INI -->
				@includeif('painel.layout.footer')
				<!-- Cityinbag - FOOTER FIM-->

            </div>
            <!-- end main content-->

        </div>
        <!-- END layout-wrapper -->

        <!-- Right Sidebar -->
        @isset($search_tools)
        <div class="right-bar">
            <div data-simplebar class="h-100">
                <div class="rightbar-title px-3 py-4">
                    <a href="javascript:void(0);" class="right-bar-toggle float-right">
                        <i class="mdi mdi-close noti-icon"></i>
                    </a>
                    <h5 class="m-0">{{$search_tools}}</h5>
                </div>
                <hr class="mt-0" />
                <div class="rightbar px-3 py-4">
                    @yield('search_tools')
                </div>
            </div> <!-- end slimscroll-menu-->
        </div>  
        @endisset
        
        <!-- /Right-bar -->

        <!-- Right bar overlay-->
        <div class="rightbar-overlay"></div>

		<!-- Cityinbag - JavaScript INI -->
        @includeif('painel.layout.js')
        @yield('script-js')
		<!-- Cityinbag - JavaScript FIM-->

        <!-- Cityinbag - Modal Info INI-->
        <div class="modal fade" id=@yield('modal_name') data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog @yield('modal_type')" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">@yield('modal_msg_title')</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>@yield('modal_msg_description')</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light waves-effect" data-dismiss="modal">@yield('modal_close')</button>
                        <button type="button" onclick=@yield('modal_target') class="btn btn-primary waves-effect waves-light">@yield('modal_save')</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Cityinbag - Modal Info FIM-->



    </body>
</html>
