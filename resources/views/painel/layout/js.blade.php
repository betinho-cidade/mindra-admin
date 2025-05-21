
        <!-- JAVASCRIPT -->
        <script src="{{asset('nazox/assets/libs/jquery/jquery.min.js')}}"></script>
        <script src="{{asset('nazox/assets/libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
        <script src="{{asset('nazox/assets/libs/metismenu/metisMenu.min.js')}}"></script>
        {{-- <script src="{{asset('nazox/assets/libs/simplebar/simplebar.min.js')}}"></script>
        <script src="{{asset('nazox/assets/libs/node-waves/waves.min.js')}}"></script> --}}

        <!-- apexcharts -->
        {{-- <script src="{{asset('nazox/assets/libs/apexcharts/apexcharts.min.js')}}"></script> --}}

        <!-- jquery.vectormap map -->
        {{-- <script src="{{asset('nazox/assets/libs/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.min.js')}}"></script>
        <script src="{{asset('nazox/assets/libs/admin-resources/jquery.vectormap/maps/jquery-jvectormap-us-merc-en.js')}}"></script> --}}

        {{-- <script src="{{asset('nazox/assets/js/pages/dashboard.init.js')}}"></script> --}}

        <script src="{{asset('nazox/assets/js/app.js')}}"></script>

        <script type='text/javascript'>
        
        function menu_aberto(){

                var _token = $('input[name="_token"]').val();

                $.ajax({
                        url: "{{route('painel.js_menu_aberto')}}",
                        method: "POST",
                        data: {_token:_token},
                        success:function(result){
                            dados = JSON.parse(result);
                        },
                        error:function(erro){
                        }
                    });
        }
    </script>        
