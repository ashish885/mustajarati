@extends('admin.layouts.master')

@section('title') {{ transLang('update_services') }} @endsection

@section('content')
    <section class="content-header">
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i> {{ transLang('dashboard') }}</a></li>
            <li><a href="{{ route('admin.services.index') }}">{{ transLang('all_services') }}</a></li>
            <li class="active">{{ transLang('update_services') }}</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{ transLang('update_services') }}</h3>
                    </div>
                    <div class="box-body">
                        <p class="alert message_box hide"></p>
                        <form id="save-frm" class="form-horizontal">
                            @csrf
                            <input type="hidden" name="latitude" value="{{$service->latitude}}">
                            <input type="hidden" name="longitude" value="{{$service->longitude}}">
                            <div class="form-group">
                                <label class="col-sm-3 control-label required">{{ transLang('vendor') }}</label>
                                <div class="col-sm-6">
                                    <select name="vendor_id" class="form-control select2-class"
                                        data-placeholder="{{ transLang('choose') }}">
                                        <option value=""></option>
                                        @if ($vendors->count())
                                            @foreach ($vendors as $value)
                                                <option value="{{ $value->id }}" {{ $value->id == $service->vendor_id ? 'selected' : '' }}>{{ $value->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label required">{{ transLang('category') }}</label>
                                <div class="col-sm-6">
                                    <select name="category_id" class="form-control select2-class"
                                        data-placeholder="{{ transLang('choose') }}">
                                        <option value=""></option>
                                        @if ($categories->count())
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}" {{ $category->id == $service->category_id ? 'selected' : '' }}>{{ $category->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label required">{{ transLang('sub_category') }}</label>
                                <div class="col-sm-6">
                                    <select name="sub_category_id" class="form-control select2-class"
                                        data-placeholder="{{ transLang('choose') }}">
                                        <option value=""></option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label required">{{ transLang('ar_name') }}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="ar_name"
                                        placeholder="{{ transLang('name') }}" value="{{$service->name}}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label required">{{ transLang('en_name') }}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="en_name"
                                        placeholder="{{ transLang('en_name') }}" value="{{$service->en_name}}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label required">{{ transLang('ar_description') }}</label>
                                <div class="col-sm-6">
                                    <textarea rows="2" class="form-control" name="ar_description"
                                        placeholder="{{ transLang('ar_description') }}">{{$service->description}}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label required">{{ transLang('en_description') }}</label>
                                <div class="col-sm-6">
                                    <textarea rows="2" class="form-control" name="en_description"
                                        placeholder="{{ transLang('en_description') }}">{{$service->en_description}}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label required">{{ transLang('amount') }}</label>
                                <div class="col-sm-6">
                                    <div class="col-sm-6 no-padding">
                                        <input type="text" class="form-control" name="amount"
                                            placeholder="{{ transLang('amount') }}" value="{{$service->amount}}">
                                    </div>
                                    <div class="col-sm-6 ">
                                        <select class="form-control select2-class" name="amount_type">
                                            @foreach (transLang('amount_type_arr') as $key => $amount_type)
                                                <option value="{{ $key }}" {{ $key== $service->amount_type ? 'selected' : '' }}>{{ $amount_type }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="type"
                                    class="col-sm-3 control-label required">{{ transLang('provide_service_customer_location') }}</label>
                                <div class="col-sm-8">
                                    <div class="form-inline">
                                        @foreach (transLang('action_type') as $key => $type)
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="action_type" {{ $key == $service->type ? 'checked' : '' }} value="{{ $key }}"> {{ $type }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label required">{{ transLang('address') }}</label>
                                <div class="col-sm-6">
                                    <textarea class="form-control" name="address"
                                        placeholder="{{ transLang('address') }}" readonly>{{$service->address}}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label required">{{ transLang('status') }}</label>
                                <div class="col-sm-6">
                                    <select class="form-control select2-class" name="status">
                                        @foreach (transLang('action_status') as $key => $status)
                                            <option value="{{ $key }}" {{ $key== $service->status ? 'selected' : '' }}>{{ $status }}</option>
                                        @endforeach
                                    </select>
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
            let selectedSubcategory = '{{ $service->sub_category_id }}';

            $(document).on('change', '[name="category_id"]', function (e) {
                let id = $(this).val();
                let $target = $('[name="sub_category_id"]');

                $target.html('<option value=""></option>').trigger('change');

                $.get(`{{ route('admin.categories.subcategories.list') }}/${id}`, response => {
                    let html = '<option value=""></option>';
                    response.forEach(el => html += `<option value="${el.id}" ${selectedSubcategory == el.id ? 'selected' : ''}>${el.name}</option>`);
                    selectedSubcategory = null;
                    $target.html(html).trigger('change');
                }, 'json');
            });
            
            $('[name="category_id"]').change();
            
            $(document).on('click', '[name="address"]', function(e) {
                e.preventDefault();
                let latitude = $('#save-frm [name="latitude"]').val();
                let longitude = $('#save-frm [name="longitude"]').val();
                let urlParams = latitude && longitude ? `/${latitude}/${longitude}` : '';
                $('#remote_model').modal({
                    show: true,
                    remote: `{{ route('admin.address.picker') }}${urlParams}`
                });
            });

            $(document).on('click', '#updateBtn', function(e) {
                e.preventDefault();
                let btn = $(this);
                let loader = $('.message_box');

                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: "{{ route('admin.services.update', $service->id) }}",
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
                        location.replace('{{ route('admin.services.index') }}');
                    }
                });
            });
        });
    </script>
@endsection
