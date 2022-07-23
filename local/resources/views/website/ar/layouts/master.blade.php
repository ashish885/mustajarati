<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" type="image/x-icon" href="{{ URL::to('logo/favicon.ico') }}" />
        <title>مُستأجراتي | وفّر ، أجّر ، واستثمر</title>
        <meta name="description" content="Mustajarati" />
        <meta name="keywords" content="Mustajarati" />
        <meta name="author" content="Mustajarati" />

        <link href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700,900" rel="stylesheet">
        <link rel="stylesheet" href="{{ URL::to('website/css/bootstrap.min.css') }}" />
        <link rel="stylesheet" href="{{ URL::to('website/font-awesome-4.7.0/css/font-awesome.min.css') }}" />
        <link rel="stylesheet" href="{{ URL::to('website/plugins/aos/aos.css') }}" />
        <link rel="stylesheet" href="{{ URL::to('website/plugins/slick-1.8.1/slick.css') }}" />
        <link rel="stylesheet" href="{{ URL::to('website/plugins/slick-1.8.1/slick-theme.css') }}" />
        <link rel="stylesheet" href="{{ URL::to('website/plugins/select2/select2.min.css') }}" />
        <link rel="stylesheet" href="{{ URL::to('website/css/style.css') }}" />
        <link rel="stylesheet" href="{{ URL::to('website/css/bootstrap-flipped.css') }}" async="async">
        <link rel="stylesheet" href="{{ URL::to('website/css/bootstrap-rtl.css') }}" async="async">
        <link rel="stylesheet" href="{{ URL::to('website/css/arabic.css') }}" async="async">
        <link rel="stylesheet" href="{{ URL::to('website/css/media.css') }}" />
    </head>

    <body>
        <!-- Include Header -->
        @includeWhen(!$showContentOnly, "website.{$locale}.includes.header")
    
        @yield('content')
    
        <!-- Include Footer -->
        @includeWhen(!$showContentOnly, "website.{$locale}.includes.footer")
    </body>

    <script src="{{ URL::to('website/js/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ URL::to('website/js/bootstrap.min.js') }}"></script>
    <script src="{{ URL::to('website/plugins/aos/aos.js') }}"></script>
    <script src="{{ URL::to('website/plugins/slick-1.8.1/slick.min.js') }}"></script>
    <script src="{{ URL::to('website/plugins/select2/select2.min.js') }}"></script>

    <script>
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

        function formatErrorMessage(jqXHR, exception) {
            if (jqXHR.status === 0) {
                return ajax_errors.http_not_connected;
            } else if (jqXHR.status == 400) {
                return ajax_errors.request_forbidden;
            } else if (jqXHR.status == 404) {
                return ajax_errors.not_found_request;
            } else if (jqXHR.status == 500) {
                return ajax_errors.session_expire;
            } else if (jqXHR.status == 503) {
                return ajax_errors.service_unavailable;
            } else if (exception === 'parsererror') {
                return ajax_errors.parser_error;
            } else if (exception === 'timeout') {
                return ajax_errors.request_timeout;
            } else if (exception === 'abort') {
                return ajax_errors.request_abort;
            } else {
                var message = '';
                try {
                    var r = jQuery.parseJSON(jqXHR.responseText);
                    if (jQuery.isEmptyObject(r) == false) {
                        $.each(r.errors, function (key, value) {
                            if (jQuery.isEmptyObject(r) != false) {
                                $.each(value, function (key, row) {
                                    message += '<p>' + row + '</p>';
                                });
                            } else {
                                message += '<p>' + value + '</p>';
                            }
                        });
                    }
                } catch (e) {
                    message = 'Uncaught Error.\n' + jqXHR.responseText;
                }
                return message;
            }
        }
    </script>

    @yield('script')
</html>