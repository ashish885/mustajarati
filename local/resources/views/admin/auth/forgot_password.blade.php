@extends('admin.layouts.login-master')

@section('title') {{ transLang('forgot_password') }} @endsection

@section('content')
    <div class="login-box">
        <div class="login-logo">
            <h2>{{ transLang('password_recover') }}</h2>
        </div>
        <div class="login-box-body">
            <h5>{{ transLang('password_recover_sub_header') }}</h5>
            <br>
            <form onkeypress="return event.keyCode != 13;">
                @csrf
                <p class="alert alert-block alert-danger message_box hide"></p>
                <div class="form-group has-feedback">
                    <input type="email" name="email" class="form-control" placeholder="{{ transLang('email') }}">
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                </div>
                <div>
                    <a href="{{ route('login') }}">{{ transLang('back_to_login') }}</a>
                    <button id="submitBtn" type="button" class="btn btn-success btn-flat pull-right">{{ transLang('reset_password') }}</button>
                </div>
                <br>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
        jQuery(function($) {
            $(document).on('click','#submitBtn',function(e) {
                e.preventDefault();
                var btn = $(this);

                $.ajax({
                    dataType: 'json',
                    type: 'POST',
                    url: '{{ route("admin.forgot.password") }}',
                    data: $('form').serialize(),
                    beforeSend: () => {
                        btn.attr('disabled',true);
                        $('.message_box').html('').addClass('hide');
                    },
                    error: (jqXHR, exception) => {
                        btn.attr('disabled',false);
                        $('.message_box').html(formatErrorMessage(jqXHR, exception)).removeClass('hide');
                    },
                    success: data => {
                        $('.message_box').html(data.message).removeClass('alert-danger hide').addClass('alert-success');
                        btn.attr('disabled',false);
                        setTimeout(() => location.replace('{{ route("login") }}'), 1000);
                    }
                });
            });

            $(document).on('keypress', '[name="email"]', function(e) {
                if(e.which == 10 || e.which == 13) {
                    e.preventDefault();
                    $('#submitBtn').click();
                }
            });
        });
    </script>
@endsection