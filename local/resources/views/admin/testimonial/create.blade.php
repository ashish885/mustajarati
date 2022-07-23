@extends('admin.layouts.master')

@section('title') {{ transLang('create_testimonial') }} @endsection

@section('content')
    <section class="content-header">
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i> {{ transLang('dashboard') }}</a></li>
            <li><a href="{{ route('admin.testimonial.index') }}">{{ transLang('all_testimonials') }}</a></li>
            <li class="active">{{ transLang('create_testimonial') }}</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h2 class="box-title">{{ transLang('create_testimonial') }}</h2>
                    </div>
                    <div class="box-body">
                        <p class="alert message_box hide"></p>
                        <form id="save-frm" class="form-horizontal">
                            @csrf
                            <div class="form-group">
                                <label class="col-sm-3 control-label required">{{ transLang('ar_title') }}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="ar_title" placeholder="{{ transLang('ar_title') }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label required">{{ transLang('en_title') }}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="en_title" placeholder="{{ transLang('en_title') }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label required">{{ transLang('ar_description') }}</label>
                                <div class="col-sm-6">
                                    <textarea rows="2" class="form-control" name="ar_description" placeholder="{{ transLang('ar_description') }}"></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label required">{{ transLang('en_description') }}</label>
                                <div class="col-sm-6">
                                    <textarea rows="2" class="form-control" name="en_description" placeholder="{{ transLang('en_description') }}"></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label required">{{ transLang('customer_name') }}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="customer_name" placeholder="{{ transLang('name') }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label required">{{ transLang('city_name') }}</label>
                                <div class="col-sm-6">
                                    <select name="city" class="form-control select2-class" data-placeholder="{{ transLang('choose') }}">
                                        <option value=""></option>
                                        @if ($cities->count())
                                            @foreach ($cities as $city)
                                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label required">{{ transLang('rating') }}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="rating" placeholder="{{ transLang('rating') }}">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="box-footer">
                        <div class="col-sm-offset-1 col-sm-6">
                            <button type="button" class="btn btn-success" id="createBtn"><i class="fa fa-check"></i>{{ transLang('create') }}</button>
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
            
            $(document).on('click', '#createBtn', function(e) {
                e.preventDefault();
                let btn = $(this);
                let loader = $('.message_box');

                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: "{{ route('admin.testimonial.create') }}",
                    data: new FormData($('#save-frm')[0]),
                    processData: false,
                    contentType: false,
                    beforeSend: () => {
                        btn.attr('disabled', true);
                        loader.html(`{!! transLang('loader_message') !!}`).removeClass('hide alert-danger alert-success').addClass('alert-info');
                    },
                    error: (jqXHR, exception) => {
                        btn.attr('disabled', false);
                        loader.html(formatErrorMessage(jqXHR, exception)).removeClass('alert-info').addClass('alert-danger');
                    },
                    success: response => {
                        btn.attr('disabled', false);
                        loader.html(response.message).removeClass('alert-info').addClass('alert-success');
                        location.replace('{{ route('admin.testimonial.index') }}');
                    }
                });
            });
        });
    </script>
@endsection
