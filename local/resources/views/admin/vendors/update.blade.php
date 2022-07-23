@extends('admin.layouts.master')

@section('title') {{ transLang('update_vendors') }} @endsection

@section('content')
    <section class="content-header">
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i>
                    {{ transLang('dashboard') }}</a></li>
            <li><a href="{{ route('admin.vendors.index') }}">{{ transLang('all_vendors') }}</a></li>
            <li class="active">{{ transLang('update_vendors') }}</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h2 class="box-title">{{ transLang('update_vendors') }}</h2>
                    </div>
                    <div class="box-body">
                        <p class="alert message_box hide"></p>
                        <form id="save-frm" class="form-horizontal">
                            @csrf
                            
                            <div class="form-group">
                                <label class="col-sm-3 control-label required">{{ transLang('name') }}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="name" placeholder="{{ transLang('name') }}" value="{{ $vendor->name}}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label required">{{ transLang('email') }}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="email" placeholder="{{ transLang('email') }}" value="{{ $vendor->email}}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="mobile" class="col-sm-3 control-label required">{{ transLang('mobile') }}</label>
                                <div class="col-sm-6">
                                    <div class="col-sm-3 no-padding">
                                        <select name="dial_code" class="form-control select2-class" data-placeholder="{{ transLang('dial_code') }}">
                                            <option value=""></option>
                                            @if ($dial_codes->count())
                                                @foreach ($dial_codes as $item)
                                                    <option value="{{ $item->dial_code }}" {{ $item->dial_code==$vendor->dial_code ? 'selected' : ''}}>{{ $item->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="mobile" placeholder="{{ transLang('mobile') }}" value="{{ $vendor->mobile}}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label required">{{ transLang('national_id') }}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="national_id" placeholder="{{ transLang('national_id') }}" value="{{ $vendor->national_id}}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label required">{{ transLang('bank_name') }}</label>
                                <div class="col-sm-6">
                                    <select name="bank_id" class="form-control select2-class" data-placeholder="{{ transLang('bank') }}">
                                        <option value=""></option>
                                        @if ($banks->count())
                                            @foreach ($banks as $item)
                                                <option value="{{ $item->id }}" {{ $item->id==$vendor->bank_details->bank_id ? 'selected' : ''}}>{{ $item->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label required">{{ transLang('bank_code') }}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="bank_code" value="{{ $vendor->bank_details->iban_no}}"  placeholder="{{ transLang('bank_code') }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label required">{{ transLang('account_no') }}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="account_no" placeholder="{{ transLang('account_no') }}" value="{{ $vendor->bank_details->account_no}}">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="col-sm-3 control-label required">{{ transLang('account_holder_name') }}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="account_holder_name" placeholder="{{ transLang('account_holder_name') }}" value="{{ $vendor->bank_details->account_holder_name}}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label required">{{ transLang('status') }}</label>
                                <div class="col-sm-6">
                                    <select class="form-control select2-class" name="status">
                                        @foreach (transLang('action_status') as $key => $status)
                                            <option value="{{ $key }}">{{ $status }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group image-wrapper">
                                <label class="col-sm-3 control-label">{{ transLang('national_id_image') }}</label>
                                <div class="col-sm-6">
                                    <img src="{{ imageBasePath($vendor->national_id_front_image)}}" width="40">
                                    <input type="file" name="national_id_image">
                                </div>
                            </div>
                            <div class="form-group image-wrapper">
                                <label class="col-sm-3 control-label">{{ transLang('profile_image') }}</label>
                                <div class="col-sm-6">
                                    <img src="{{ imageBasePath($vendor->profile_image)}}" width="40">
                                    <input type="file" name="profile_image">
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
                    url: "{{ route('admin.vendors.update',$vendor->id) }}",
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
