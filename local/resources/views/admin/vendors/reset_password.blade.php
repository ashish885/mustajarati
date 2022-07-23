@extends('admin.layouts.master')

@section('title') {{ transLang('reset_password') }} @endsection

@section('content')
    <section class="content-header">
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i>
                    {{ transLang('dashboard') }}</a></li>
            <li><a href="{{ route('admin.vendors.index') }}">{{ transLang('all_vendors') }}</a></li>
            <li class="active">{{ transLang('reset_password') }}</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h2 class="box-title">{{ transLang('reset_password') }}</h2>
                    </div>
                    <div class="box-body">
                        <p class="alert message_box hide"></p>
                        <form id="save-frm" class="form-horizontal">
                            @csrf
                            
                            <div class="form-group">
                                <label class="col-sm-3 control-label required">{{ transLang('password') }}</label>
                                <div class="col-sm-6">
                                    <input type="password" class="form-control" name="password" placeholder="{{ transLang('password') }}" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label required">{{ transLang('new_password') }}</label>
                                <div class="col-sm-6">
                                    <input type="password" class="form-control" name="password_confirmation"  placeholder="{{ transLang('new_password') }}" >
                                </div>
                            </div>
                              
                        </form>
                    </div>
                    <div class="box-footer">
                        <div class="col-sm-offset-1 col-sm-6">
                            <button type="button" class="btn btn-success" id="updateBtn"><i class="fa fa-check"></i>
                                {{ transLang('update') }}</button>
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
            $(document).on('click', '#updateBtn', function(e) {
                e.preventDefault();
                let btn = $(this);
                let loader = $('.message_box');

                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: "{{ route('admin.vendors.reset_password',$id) }}",
                    data: new FormData($('#save-frm')[0]),
                    processData: false,
                    contentType: false,
                    beforeSend: () => {
                        btn.attr('disabled', true);
                        loader.html(`{!! transLang('loader_message') !!}`).removeClass(
                            'hide alert-danger alert-success').addClass('alert-info');
                    },
                    error: (jqXHR, exception) => {
                        btn.attr('disabled', false);
                        loader.html(formatErrorMessage(jqXHR, exception)).removeClass(
                            'alert-info').addClass('alert-danger');
                    },
                    success: response => {
                        btn.attr('disabled', false);
                        loader.html(response.message).removeClass('alert-info').addClass(
                            'alert-success');
                        location.replace('{{ route('admin.vendors.index') }}');
                    }
                });
            });
        });
    </script>
@endsection
