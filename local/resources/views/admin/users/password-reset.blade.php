@extends('admin.layouts.master') 

@section('title') {{ transLang('reset_password') }} @endsection
 
@section('content')
    <section class="content-header">
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i> {{ transLang('dashboard') }}</a></li>
            <li><a href="{{ route('admin.users.index') }}">{{ transLang('all_users') }}</a></li>
            <li class="active">{{ transLang('reset_password') }}</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{ transLang('reset_password') }}</h3>
                    </div>
                    <div class="box-body">
                        <p class="alert message_box hide"></p>
                        <form id="resetPassword-form" class="form-horizontal">
                            @csrf
                            <div class="form-group">
                                <label class="col-sm-2 control-label required">{{ transLang('new_password') }}</label>
                                <div class="col-sm-6">
                                    <input type="password" class="form-control" name="password" placeholder="{{ transLang('new_password') }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label required">{{ transLang('confirm_password') }}</label>
                                <div class="col-sm-6">
                                    <input type="password" class="form-control" name="password_confirmation" placeholder="{{ transLang('confirm_password') }}">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="box-footer">
                        <div class="col-sm-offset-1 col-sm-6">
                            <button type="button" class="btn btn-success" id="resetPasswordBtn"><i class="fa fa-check"></i> {{ transLang('change_password') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
 
@section('scripts')
<script type="text/javascript">
    jQuery(function($) {
        $(document).on('click','#resetPasswordBtn',function(e) {
            e.preventDefault();
            let btn = $(this);
            let loader = $('.message_box');
            
            $.ajax({
                url: "{{ route('admin.users.password_reset', ['id' => $user->id]) }}",
                data: $('#resetPassword-form').serialize(),
                dataType: 'json',
                type: 'POST',
                beforeSend: () => {
                    btn.attr('disabled',true);
                    loader.html(`{!! transLang('loader_message') !!}`).removeClass('hide alert-danger alert-success').addClass('alert-info');
                },
                error: (jqXHR, exception) => {
                    btn.attr('disabled',false);
                    loader.html(formatErrorMessage(jqXHR, exception)).removeClass('alert-info').addClass('alert-danger');
                },
                success: response => {
                    btn.attr('disabled',false);
                    loader.html(response.message).removeClass('alert-info').addClass('alert-success');
                    location.replace('{{ route("admin.users.index")}}');
                }
            });
        });
    });
</script>
@endsection