@extends('admin.layouts.master') 

@section('title') {{ transLang('create_country') }} @endsection
 
@section('content')
    <section class="content-header">
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i> {{ transLang('dashboard') }}</a></li>
            <li><a href="{{ route('admin.countries.index') }}"> {{ transLang('all_countries') }} </a></li>
            <li class="active">{{ transLang('create_country') }}</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{ transLang('create_country') }}</h3>
                    </div>
                    <div class="box-body">
                        <p class="alert message_box hide"></p>
                        <form id="save-frm" class="form-horizontal">
                            @csrf
                            <div class="form-group">
                                <label class="col-sm-2 control-label required">{{ transLang('name') }}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="name" placeholder="{{ transLang('name') }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label required">{{ transLang('en_name') }}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="en_name" placeholder="{{ transLang('en_name') }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label required">{{ transLang('dial_code') }}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="dial_code" placeholder="{{ transLang('dial_code') }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label required">{{ transLang('alpha_2') }}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="alpha_2" placeholder="{{ transLang('alpha_2') }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label required">{{ transLang('alpha_3') }}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="alpha_3" placeholder="{{ transLang('alpha_3') }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label required">{{ transLang('currency') }}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="currency" placeholder="{{ transLang('currency') }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label required">{{ transLang('tax') }}</label>
                                <div class="col-sm-6">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="tax" placeholder="{{ transLang('tax') }}">
                                        <span class="input-group-addon"><i class="fa fa-percent"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="status" class="col-sm-2 control-label required">{{ transLang('status') }}</label>
                                <div class="col-sm-6">
                                    <select class="form-control" name="status">
                                        @foreach(transLang('action_status') as $key => $status)
                                            <option value="{{ $key }}">{{ $status }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label required">{{ transLang('flag') }}</label>
                                <div class="col-sm-6">
                                    <input type="file" name="file">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="box-footer">
                        <div class="col-sm-offset-1 col-sm-6">
                            <button type="button" class="btn btn-success" id="saveBtn">
                                <i class="fa fa-check"></i> {{ transLang('create') }}
                            </button>
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
                const loader = $('.message_box');
                
                $.ajax({
                    dataType: 'json',
                    type: 'POST',
                    url: "{{ route('admin.countries.create') }}",
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
						location.replace('{{ route("admin.countries.index")}}');
					}
                });
            });
        });
    </script>
@endsection