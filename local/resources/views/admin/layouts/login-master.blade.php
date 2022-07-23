<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <meta charset="utf-8" />
        <link rel="icon" type="image/x-icon" href="{{ asset('logo/favicon.ico') }}" />
        <title>{{ transLang('admin_app_name') }} - @yield('title')</title>

        <meta name="description" content="{{ config('app.name') }} - @yield('title')" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <!-- Bootstrap 3.3.6 -->
        <link rel="stylesheet" href="{{ asset('backend/bootstrap/css/bootstrap.min.css') }}">
        @if(getSessionLang() == 'ar')
            <link rel="stylesheet" href="{{ asset('backend/bootstrap/css/bootstrap-flipped.css') }}">
            <link rel="stylesheet" href="{{ asset('backend/bootstrap/css/bootstrap-rtl.css') }}">
        @endif
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
        <!-- Ionicons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
        <!-- Theme style -->
        <link rel="stylesheet" href="{{ asset('backend/dist/css/AdminLTE.min.css') }}">
        <!-- iCheck -->
        <link rel="stylesheet" href="{{ asset('backend/plugins/iCheck/square/blue.css') }}">
        <link rel="stylesheet" href="{{ asset('backend/css/custom.css') }}?time={{ time() }}">

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head> 
    <body class="hold-transition login-page {{ getSessionLang() }}_lang">
        <div class="se-pre-con"></div>
        <div class="login-box">
            <div class="login-logo">
                <a>
                    <b><img src="{{ asset('logo/logo.svg') }}" alt="{{ transLang('company') }}" width="75"></b>
                </a>
            </div>
            @yield('content')
        </div>

        <!-- jQuery 2.2.3 -->
        <script src="{{ asset('backend/plugins/jQuery/jquery-2.2.3.min.js') }}"></script>
        <script src="{{ asset('backend/js/modernizr-custom.js') }}"></script>
        <!-- Bootstrap 3.3.6 -->
        <script src="{{ asset('backend/bootstrap/js/bootstrap.min.js') }}"></script>
        <!-- iCheck -->
        <script src="{{ asset('backend/plugins/iCheck/icheck.min.js') }}"></script>

        <script type="text/javascript">
            $(window).on('load', function() {
                // Animate loader off screen
                $(".se-pre-con").fadeOut("slow");
            });

            // <!-- Http Errors -->
            const ajax_errors = {
                http_not_connected: "{{ transLang('http_not_connected') }}",
                request_forbidden: "{{ transLang('request_forbidden') }}",
                not_found_request: "{{ transLang('not_found_request') }}",
                session_expire: "{{ transLang('session_expire') }}",
                service_unavailable: "{{ transLang('service_unavailable') }}",
                parser_error: "{{ transLang('parser_error') }}",
                request_timeout: "{{ transLang('request_timeout') }}",
                request_abort: "{{ transLang('request_abort') }}"
            };
        </script>
        
        <script src="{{ asset('backend/js/main.js') }}"></script>
        @yield('scripts')
        
    </body>
</html>