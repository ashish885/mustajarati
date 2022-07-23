@extends('admin.layouts.login-master')

@section('title') {{ transLang('sign_in') }} @endsection

@section('content')
    <div id="message_box" style="display: none;"></div>

    <div class="login-box-body">
        <p class="login-box-msg">{{ transLang('sign_in_to_start_your_session') }}</p>

        <div class="row">
            <div class="col-xs-12">
                <div class="btn-group btn-group-justified login-lang-switcher" style="margin-bottom: 20px;">
                    <a href="{{ route('admin.login.change.locale', ['lang' => 'en']) }}" class="btn btn-primary btn-flat {{ getSessionLang() == 'en' ? 'disabled' : '' }}">{{ transLang('english') }}</a>
                    <a href="{{ route('admin.login.change.locale', ['lang' => 'ar']) }}" class="btn btn-primary btn-flat {{ getSessionLang() == 'ar' ? 'disabled' : '' }}">{{ transLang('arabic') }}</a>
                </div>
            </div>
        </div>
        
        <form id="login-form">
            @csrf
            <div class="form-group has-feedback">
                <input type="email" class="form-control login_input" name="email" placeholder="{{ transLang('email') }}" value="admin@mustajarati.com">
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="password" class="form-control login_input" placeholder="{{ transLang('password') }}" name="password" value="Coff1248!EE">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            <div class="row">
                <div class="col-xs-8">
                    <div class="checkbox icheck">
                        <label>
                            <input type="checkbox" name="remember"> {{ transLang('remember_me') }}
                        </label>
                    </div>
                </div>
                <div class="col-xs-4">
                    <button type="button" class="btn btn-success btn-block btn-flat" id="login-submit">{{ transLang('sign_in') }}</button>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 text-center">
                    <a href="{{ route('admin.forgot.password') }}">{{ transLang('forgot_password_lbl') }}</a>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
        jQuery(function($) {
            $(document).on('click','#login-submit',function(e) {
                e.preventDefault();
                let $loader = $('#message_box');
                
                $.ajax({
                    url: '{{ route("admin.login") }}',
                    dataType : 'json',
                    type: 'POST',
                    data: $('#login-form').serialize(),
                    beforeSend: () => {
                        $('#login-submit').attr('disabled',true);
                        $loader.hide();
                    },
                    error: (jqXHR, exception) => {
                        $('#login-submit').attr('disabled',false);
                        $loader.html(`
                            <div class="alert alert-danger alert-dismissible">
                                <button aria-hidden="true" class="close" data-dismiss="alert" type="button">&times;</button>
                                ${formatErrorMessage(jqXHR, exception)}
                            </div>
                        `).show();
                    },
                    success: data => {
                        $('#login-submit').html(data.success).removeClass('btn-primary').addClass('btn-success');
                        $loader.hide();
                        location.replace('{{ route("admin.dashboard")}}');
                    }
                });
            });

            $(document).on('keypress', '.login_input', function(e) {
                if(e.which == 10 || e.which == 13) {
                    e.preventDefault();
                    $('#login-submit').click();
                }
            });

            $('input').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%' // optional
            });
        });
    </script>
@endsection