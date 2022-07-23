@extends('admin.layouts.master')

@section('title') {{ transLang('profile') }} @endsection

@section('content')
    <section class="content-header">
        <h1> {{ transLang('profile') }}</h1>
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i> {{ transLang('dashboard') }}</a></li>
            <li class="active">{{ transLang('profile') }}</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#basic-info-tab">{{ transLang('basic_info') }}</a></li>
                        <li><a data-toggle="tab" href="#change-password-tab">{{ transLang('change_password') }}</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="basic-info-tab">
                            <p class="alert message_box hide"></p>
                            <form id="save-frm" class="form-horizontal">
                                @csrf
                                <div class="form-group">
                                    <label class="col-sm-2 control-label required">{{ transLang('name') }}</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" name="name" placeholder="{{ transLang('name') }}" value="{{ $admin->name }}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label required">{{ transLang('email') }}</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" name="email" placeholder="{{ transLang('email') }}" value="{{ $admin->email }}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label required">{{ transLang('mobile') }}</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" name="mobile" value="{{ $admin->mobile }}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">{{ transLang('profile_image') }}</label>
                                    <div class="col-sm-6">
                                        @if($admin->profile_image)
                                            <img src="{{ imageBasePath($admin->profile_image) }}" width="60"/>
                                        @endif
                                        <input type="file" name="profile_image">
                                    </div>
                                </div>
                            </form>
                            <hr>
                            <div class="row">
                                <div class="col-xs-12 col-md-offset-1">
                                    <button id="saveBtn" class="btn btn-success" type="button"><i class="fa fa-check"></i> {{ transLang('save') }}</button>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="change-password-tab">
                            <p class="alert message_box hide"></p>
                            <form id="change-password-frm" class="form-horizontal">
                                @csrf
                                <div class="form-group">
                                    <label class="col-sm-2 control-label required">{{ transLang('old_password') }}</label>
                                    <div class="col-sm-6">
                                        <input type="password" class="form-control" name="old_password" placeholder="{{ transLang('old_password') }}">
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="col-sm-2 control-label required">{{ transLang('password') }}</label>
                                    <div class="col-sm-6">
                                        <input type="password" class="form-control" name="password" placeholder="{{ transLang('password') }}">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label required">{{ transLang('confirm_password') }}</label>
                                    <div class="col-sm-6">
                                        <input type="password" class="form-control" name="password_confirmation" placeholder="{{ transLang('confirm_password') }}">
                                    </div>
                                </div>
                            </form>
                            <hr>
                            <div class="row">
                                <div class="col-xs-12 col-md-offset-1">
                                    <button id="changePasswordBtn" class="btn btn-success" type="button"><i class="fa fa-check"></i> {{ transLang('update') }}</button>
                                </div>
                            </div>
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
            $(document).on('click','#saveBtn',function(e) {
                e.preventDefault();
                const btn = $(this);
                const loader = $('#basic-info-tab .message_box');
                
                $.ajax({
                    dataType: 'json',
                    type: 'POST',
                    url: "{{ route('admin.profile.update') }}",
                    data: new FormData($('#save-frm')[0]),
                    processData: false,
                    contentType: false,
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
                    }
                });
            });

            $(document).on('click','#changePasswordBtn',function(e) {
                e.preventDefault();
                const btn = $(this);
                const loader = $('#change-password-tab .message_box');
                
                $.ajax({
                    dataType: 'json',
                    type: 'POST',
                    url: "{{ route('admin.profile.change_password') }}",
                    data: new FormData($('#change-password-frm')[0]),
                    processData: false,
                    contentType: false,
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
                    }
                });
            });
        });
    </script>
@endsection