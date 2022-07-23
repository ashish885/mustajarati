<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <meta charset="utf-8" />
        <link rel="icon" type="image/x-icon" href="{{ asset('logo/favicon.ico') }}" />
        <title>{{ transLang('admin_app_name') }} - @yield('title')</title>
        <meta name="description" content="{{ config('app.name') }} - @yield('title')" />
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- Bootstrap 3.3.6 -->
        <link rel="stylesheet" href="{{ asset('backend/bootstrap/css/bootstrap.min.css') }}">
        @if(getSessionLang() == 'ar')
            <link rel="stylesheet" href="{{ asset('backend/bootstrap/css/bootstrap-flipped.css') }}">
            <link rel="stylesheet" href="{{ asset('backend/bootstrap/css/bootstrap-rtl.css') }}">
        @endif

        <!-- Font Awesome -->
        <link rel="stylesheet" href="{{ asset('backend/plugins/font-awesome-4.7.0/css/font-awesome.css') }}">
        <!-- Ionicons -->
        <link rel="stylesheet" href="{{ asset('backend/plugins/ionicons-2.0.1/css/ionicons.min.css') }}">
        <!-- DataTables -->
        <link rel="stylesheet" href="{{ asset('backend/plugins/datatables/dataTables.bootstrap.css') }}">

        @if(getSessionLang() == 'ar')
            <!-- Theme style -->
            <link rel="stylesheet" href="{{ asset('backend/dist/css/AdminLTE-rtl.min.css') }}">
            <!-- AdminLTE Skins. Choose a skin from the css/skins
            folder instead of downloading all of them to reduce the load. -->
            <link rel="stylesheet" href="{{ asset('backend/dist/css/skins/_all-skins-rtl.min.css') }}">
        @else
            <!-- Theme style -->
            <link rel="stylesheet" href="{{ asset('backend/dist/css/AdminLTE.min.css') }}">
            <!-- AdminLTE Skins. Choose a skin from the css/skins
            folder instead of downloading all of them to reduce the load. -->
            <link rel="stylesheet" href="{{ asset('backend/dist/css/skins/_all-skins.min.css') }}">
        @endif
        
        
        <!-- iCheck -->
        <link rel="stylesheet" href="{{ asset('backend/plugins/iCheck/flat/blue.css') }}">
        <!-- Morris chart -->
        <link rel="stylesheet" href="{{ asset('backend/plugins/morris/morris.css') }}">
        <!-- jvectormap -->
        <link rel="stylesheet" href="{{ asset('backend/plugins/jvectormap/jquery-jvectormap-1.2.2.css') }}">
        <!-- Date Picker -->
        <link rel="stylesheet" href="{{ asset('backend/plugins/datepicker/datepicker3.css') }}">
        <!-- Daterange picker -->
        <link rel="stylesheet" href="{{ asset('backend/plugins/daterangepicker/daterangepicker.css') }}">
        <!-- Bootstrap time Picker -->
        <link rel="stylesheet" href="{{ asset('backend/plugins/timepicker/bootstrap-timepicker.min.css') }}">
        <!-- Bootstrap date-time Picker -->
        <link rel="stylesheet" href="{{ asset('backend/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}">
        <!-- summernote -->
       <link href="{{ asset('backend/plugins/summernote/summernote.css') }}" rel="stylesheet">
        <!-- Select2 -->
        <link rel="stylesheet" href="{{ asset('backend/plugins/select2/select2.min.css') }}">
        <!-- sweetalert2 -->
        <link rel="stylesheet" href="{{ asset('backend/plugins/sweetalert2/sweetalert2.min.css') }}">
        <!-- fancybox -->
        <link rel="stylesheet" href="{{ asset('backend/plugins/fancybox/jquery.fancybox.min.css') }}">
        <!-- iCheck -->
        <link rel="stylesheet" href="{{ asset('backend/plugins/iCheck/square/blue.css') }}">
        <link rel="stylesheet" href="{{ asset('backend/css/site.css') }}">
        <link rel="stylesheet" href="{{ asset('backend/css/custom.css') }}?time={{ time() }}">
        <!-- Star Rating -->
        {{-- <link rel="stylesheet" href="{{ asset('backend/plugins/star-rating-svg-master/src/css/star-rating-svg.css') }}"> --}}
        <!-- Image Cropper -->
        <link rel="stylesheet" href="{{ asset('backend/plugins/cropper/cropper.css') }}">

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
       
        @yield('styles')
    </head>
    
    <body class="hold-transition skin-blue sidebar-mini {{ getSessionLang() }}_lang">
        <div class="se-pre-con"></div>
        
        <div class="wrapper">
            <!-- Header Container Start -->
            @include('admin.includes.header')
        
            <!-- Left side column. contains the logo and sidebar -->
            @include('admin.includes.leftmenu')


            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                @yield('content')
            </div>
            <!-- /.content-wrapper -->
            
            <!-- Include Footer -->
            @include('admin.includes.footer')
        </div>

        <!-- basic scripts -->
        <!-- jQuery 2.2.3 -->
        <script src="{{ asset('backend/plugins/jQuery/jquery-2.2.3.min.js') }}"></script>
        <!-- jQuery UI 1.11.4 -->
        <script src="{{ asset('backend/plugins/jQueryUI/jquery-ui.min.js') }}"></script>
        <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
        <script>
            $.widget.bridge('uibutton', $.ui.button);
            const CURRENT_URL = '{{ Request::url() }}';
            const CURRENT_LANG = '{{ getSessionLang() }}';
        </script>
        <!-- Bootstrap 3.3.6 -->
        <script src="{{ asset('backend/bootstrap/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('backend/js/modernizr-custom.js') }}"></script>
        <!-- DataTables -->
        <script src="{{ asset('/backend/plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('/backend/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
        <script src="{{ asset('/backend/plugins/datatables/dataTables.buttons.min.js') }}"></script>
        <script src="{{ asset('/backend/plugins/datatables/buttons.flash.min.js') }}"></script>
        <script src="{{ asset('/backend/plugins/datatables/jszip.min.js') }}"></script>
        <script src="{{ asset('/backend/plugins/datatables/vfs_fonts.js') }}"></script>
        <script src="{{ asset('/backend/plugins/datatables/buttons.html5.min.js') }}"></script>
       
        <!-- Sparkline -->
        <script src="{{ asset('backend/plugins/sparkline/jquery.sparkline.min.js') }}"></script>
        <!-- jvectormap -->
        <script src="{{ asset('backend/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js') }}"></script>
        <script src="{{ asset('backend/plugins/jvectormap/jquery-jvectormap-world-mill-en.js') }}"></script>
        <!-- jQuery Knob Chart -->
        <script src="{{ asset('backend/plugins/knob/jquery.knob.js') }}"></script>
        <!-- moment -->
        <script src="{{ asset('/backend/plugins/moment/moment.min.js') }}"></script>
        <script src="{{ asset('/backend/plugins/moment/moment-with-locales.min.js') }}"></script>
        <script src="{{ asset('/backend/plugins/moment/moment-timezone.min.js') }}"></script>
        <script src="{{ asset('/backend/plugins/moment/moment-timezone-with-data.min.js') }}"></script>
        <!-- daterangepicker -->
        <script src="{{ asset('backend/plugins/daterangepicker/daterangepicker.js') }}"></script>
        <!-- datepicker -->
        <script src="{{ asset('backend/plugins/datepicker/bootstrap-datepicker.js') }}"></script>
        <!-- bootstrap time picker -->
        <script src="{{ asset('backend/plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>
        <!-- bootstrap date-time picker -->
        <script src="{{ asset('backend/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js') }}"></script>
        <!-- summernote -->
        <script src="{{ asset('backend/plugins/summernote/summernote.js') }}"></script>
        <!-- Slimscroll -->
        <script src="{{ asset('backend/plugins/slimScroll/jquery.slimscroll.min.js') }}"></script>
        <!-- FastClick -->
        <script src="{{ asset('backend/plugins/fastclick/fastclick.js') }}"></script>
        <!-- Chart JS -->
        <script src="{{ asset('backend/plugins/chartjs/Chart.min.js') }}"></script>
        <!-- Select2 -->
        <script src="{{ asset('backend/plugins/select2/select2.full.min.js') }}"></script>
        <!-- sweetalert2 -->
        <script src="{{ asset('backend/plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>
        <!-- fancybox -->
        <script src="{{ asset('backend/plugins/fancybox/jquery.fancybox.min.js') }}"></script>
        <!-- iCheck -->
        <script src="{{ asset('backend/plugins/iCheck/icheck.min.js') }}"></script>
        <!-- Image Cropper -->
        <script src="{{ asset('backend/plugins/cropper/cropper.js') }}"></script>
        <!-- Star Rating -->
        {{-- <script src="{{ asset('backend/plugins/star-rating-svg-master/src/jquery.star-rating-svg.js') }}"></script> --}}
        <!-- AdminLTE App -->
        <script src="{{ asset('backend/dist/js/app.min.js') }}"></script>
        <!-- Google Map API js -->
        <script src="https://maps.google.com/maps/api/js?key={{ config('cms.google_api_key') }}&libraries=places"></script>
        <script src="{{ asset('backend/js/google-place-picker.js') }}"></script>

        <div id="remote_model" class="modal fade" data-backdrop="static">
            <div class="modal-dialog">
                <div class="modal-content"></div>
            </div>
        </div>
        <div id="cropper_model" class="modal fade" data-backdrop="static">
            <div class="modal-dialog modal-lg">
                <div class="modal-content"></div>
            </div>
        </div>

        <script type="text/javascript">
            $(window).on('load', function() {
                // Animate loader off screen
                $(".se-pre-con").fadeOut("slow");
            });

            moment.locale('{{ getSessionLang() }}');
    
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
    
            $.extend( true, $.fn.dataTable.defaults, {
                @if(getSessionLang() == 'ar')
                    language: {
                        url: "{{ asset('/backend/plugins/datatables/arabic.json') }}"
                    },
                @endif
                scrollX: true,
                scrollCollapse: true,
                fixedColumns: true,
                stateSave: true,
                dom: 'lBfrtip',
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                buttons: [
                    {
                        extend: 'csv', 
                        className: 'btn btn-sm btn-primary',
                        text: function ( dt, button, config ) {
                            return dt.i18n('buttons.csv', '<i class="fa fa-file-o fa-fw"></i> {{ transLang("csv") }}');
                        }
                    },
                    {
                        extend: 'excel', 
                        className: 'btn btn-sm btn-primary',
                        text: function ( dt, button, config ) {
                            return dt.i18n('buttons.csv', '<i class="fa fa-file-excel-o fa-fw"></i> {{ transLang("excel") }}');
                        }
                    }
                ]
            });
            
            $(document).on('click', 'a[data-toggle="modal"]', function (e) {
                e.preventDefault();
                e.stopPropagation();

                var target_element = $(this).data('target');
                $(target_element).find('.modal-content').html(`
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 center">{!! transLang("loader_message") !!}</div>
                        </div>
                    </div>
                `);
            });
            
            $('#remote_model,#cropper_model').on('hidden.bs.modal', function (e) {
                $(this).removeData();
                $(this).find('.modal-content').empty();
            });
            // $('#remote_model').on('show.bs.modal', function (e) {});
            
            // For Image Cropper 
            $(document).on('change', '.image-cropper', function (e) {
                if (this.files && this.files[0]) {
                    let file = this.files[0];
                    let _URL = window.URL || window.webkitURL;
                    
                    let { width, height, name, enable_ratio } = $(this).data();
                    enable_ratio = enable_ratio === undefined ? 1 : enable_ratio;
                    let filename = $(this).val();
                    let extension = filename.substr((filename.lastIndexOf('.') + 1)).toLowerCase();
                    if($.inArray(extension, ['jpg', 'jpeg', 'png']) < 0) {
                        infoAlert('{{ transLang("invalid_file_type") }}');
                        return false;
                    }
                    
                    // Validate image dimensions
                    let img = new Image();
                    img.onload = function () {
                        if(this.width < width || this.height < height) {
                            infoAlert('{{ transLang("invalid_file_dimension") }}');
                            return false;
                        }

                        var reader = new FileReader();
                        reader.onload = function (e) {
                            $('#cropper_model .modal-content').data('crop_img', e.target.result);
                            $('#cropper_model').modal({show:true, remote: `{{ URL::to("admin/cropper/init") }}/${width}/${height}/${name}/${enable_ratio}`});
                        }
                        reader.readAsDataURL(file);
                    };
                    img.src = _URL.createObjectURL(file);
                }
            });

            $(document).on('click', '.delete-entry', async function(e) {
                e.preventDefault();

                if (await confirmAlert()) {
                    var href = $(this).attr('href');
                    var { tbl, reload_page } = $(this).data();
                    $.get( href, () => {
                        if (reload_page == 1) {
                            location.reload(true);
                        } else {
                            reloadTable(`${tbl}-table`);
                        }
                    })
                    .fail((jqXHR, exception) => infoAlert($(formatErrorMessage(jqXHR, exception)).text()));
                }
            });

            // await confirmAlert()
            async function confirmAlert() {
                let { value: isAccepted } = await Swal.fire({
                    text: `{{ transLang('are_you_sure') }}`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: `<i class="fa fa-check"></i> {{ transLang('yes') }}`,
                    cancelButtonText: `{{ transLang('cancel') }}`,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                });
                return isAccepted === true;
            }

            async function infoAlert(msg, icon = 'warning') {
                await Swal.fire({
                    text: msg,
                    icon, // warning, error, success, info, and question
                    showCancelButton: false,
                    confirmButtonText: `<i class="fa fa-check"></i> {{ transLang('okay') }}`,
                    confirmButtonColor: '#3085d6',
                });
            }
        </script>

        <script src="{{ asset('backend/js/main.js') }}"></script>

        @yield('scripts')
    </body>
</html>