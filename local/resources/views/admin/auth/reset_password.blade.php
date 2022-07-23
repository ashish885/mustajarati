@extends('admin.layouts.login-master')

@section('title') {{ transLang('change_password') }} @endsection

@section('content')
    <div class="login-box">
        <div class="login-logo">
            <h2>{{ transLang('change_password') }}</h2>
        </div>
        <div class="login-box-body">
            <h5>{!!sprintf(transLang('reset_password_heading'), $admin->name) !!}</h5>
            <br>
            <form>
                @csrf
                <p class="alert alert-block alert-danger message_box hide"></p>
                <div class="form-group has-feedback">
                    <input type="password" name="password" class="form-control" placeholder="{{ transLang('new_password') }}">
                    <span class="fa fa-key form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    <input type="password" name="password_confirmation" class="form-control" placeholder="{{ transLang('confirm_password') }}">
                    <span class="fa fa-key form-control-feedback"></span>
                </div>
                <div>
                    <button id="submitBtn" type="button" class="btn btn-success btn-flat pull-right">{{ transLang('change_password') }}</button>
                </div>
                <br><br>
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
                    url: '{{ route("admin.password.reset") }}',
                    data: $('form').serialize() + '&' + $.param({id: '{{ $admin->id }}'}),
                    beforeSend: () => {
                        btn.attr('disabled',true);
                        $('.message_box').html('').addClass('hide');
                    },
                    error: (jqXHR, exception) => {
                        btn.attr('disabled',false);
                        $('.message_box').html(formatErrorMessage(jqXHR, exception)).removeClass('hide');
                    },
                    success: (data) => {
                        $('.message_box').html(data.message).removeClass('alert-danger hide').addClass('alert-success');
                        btn.attr('disabled',false);
                        setTimeout(() => location.replace('{{ route("login") }}'), 1000);
                    }
                });
            });

            $(document).on('keypress', '[name="password"],[name="password_confirmation"]', function(e) {
                if(e.which == 10 || e.which == 13) {
                    e.preventDefault();
                    $('#submitBtn').click();
                }
            });
        });
    </script>
@endsection