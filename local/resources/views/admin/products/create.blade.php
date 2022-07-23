@extends('admin.layouts.master')

@section('title') {{ transLang('create_products') }} @endsection

@section('content')
    <section class="content-header">
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i>{{ transLang('dashboard') }}</a></li>
            <li><a href="{{ route('admin.products.index') }}">{{ transLang('all_products') }}</a></li>
            <li class="active">{{ transLang('create_products') }}</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{ transLang('create_products') }}</h3>
                    </div>
                    <div class="box-body">
                        <p class="alert message_box hide"></p>
                        <form id="save-frm" class="form-horizontal">
                            @csrf
                            <input type="hidden" name="latitude">
                            <input type="hidden" name="longitude">
                            <div class="form-group">
                                <label class="col-sm-3 control-label required">{{ transLang('vendor') }}</label>
                                <div class="col-sm-6">
                                    <select name="vendor_id" class="form-control select2-class"  data-placeholder="{{ transLang('choose') }}">
                                        <option value=""></option>
                                        @if ($vendors->count())
                                            @foreach ($vendors as $vendor)
                                                <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label required">{{ transLang('category') }}</label>
                                <div class="col-sm-6">
                                    <select name="category_id" class="form-control select2-class"  data-placeholder="{{ transLang('choose') }}">
                                        <option value=""></option>
                                        @if ($categories->count())
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label required">{{ transLang('sub_category') }}</label>
                                <div class="col-sm-6">
                                    <select name="sub_category_id" class="form-control select2-class"   data-placeholder="{{ transLang('choose') }}">
                                        <option value=""></option>
                                    </select>
                                </div>
                            </div>
                        

                            <div class="form-group">
                                <label class="col-sm-3 control-label required">{{ transLang('ar_name') }}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="name" placeholder="{{ transLang('ar_name') }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label required">{{ transLang('en_name') }}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="en_name" placeholder="{{ transLang('en_name') }}">
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
                                <label class="col-sm-3 control-label required">{{ transLang('is_new') }}</label>
                                <div class="col-sm-6">
                                    <div class="radio icheck">
                                        @foreach (transLang('other_action') as $key => $val)
                                            <label>
                                                <input type="radio" name="is_new" value="{{ $key }}" {{ $key == 1 ? 'checked' : '' }}> {{ $val }}&nbsp;
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="form-group click-type-options new-price">
                                <label class="col-sm-3 control-label required">{{ transLang('new_product_price') }}</label>
                                <div class="col-sm-6">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="new_product_price" placeholder="{{ transLang('new_product_price') }}">
                                        <span class="input-group-addon">{{ transLang('sar') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group click-type-options date" style="display: none;">
                                <label class="col-sm-3 control-label required">{{ transLang('total_years') }}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control " name="total_years" placeholder="{{ transLang('total_years') }}">
                                </div>
                            </div>
                            <div class="form-group click-type-options date" style="display: none;">
                                <label class="col-sm-3 control-label required">{{ transLang('total_months') }}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="total_months" placeholder="{{ transLang('total_months') }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label required">{{ transLang('amount') }}</label>
                                <div class="col-sm-6">
                                    <div class="col-sm-6 no-padding">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="amount" placeholder="{{ transLang('amount') }}">
                                            <span class="input-group-addon">{{ transLang('sar') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 ">
                                        <select class="form-control select2-class" name="amount_type" placeholder="{{ transLang('amount_type') }}">
                                            @foreach (transLang('amount_type_arr') as $key => $amount_type)
                                                <option value="{{ $key }}">{{ $amount_type }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label required">{{ transLang('delay_charges') }}</label>
                                <div class="col-sm-6">
                                    <div class="col-sm-6 no-padding">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="delay_charges" placeholder="{{ transLang('delay_charges') }}">
                                            <span class="input-group-addon">{{ transLang('sar') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <select class="form-control select2-class" name="delay_charges_type" placeholder="{{ transLang('delay_charges_type') }}">
                                            @foreach (transLang('delay_charges_arr') as $key => $amount_type)
                                                <option value="{{ $key }}">{{ $amount_type }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label required">{{ transLang('security_amount') }}</label>
                                <div class="col-sm-6">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="security_amount" placeholder="{{ transLang('security_amount') }}">
                                        <span class="input-group-addon">{{ transLang('sar') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label required">{{ transLang('is_delivery_available') }}</label>
                                <div class="col-sm-6">
                                    <select class="form-control select2-class" name="is_delivery_available" data-placeholder="{{ transLang('is_delivery_available') }}">
                                        @foreach(transLang('other_action') as $key => $status)
                                            <option value="{{ $key }}">{{ $status }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group delivery_charges_wrapper">
                                <label class="col-sm-3 control-label required">{{ transLang('delivery_charges') }}</label>
                                <div class="col-sm-6">
                                    <div class="input-group">
                                    <input type="text" class="form-control" name="delivery_charges" placeholder="{{ transLang('delivery_charges') }}">
                                    <span class="input-group-addon">{{ transLang('sar') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label required">{{ transLang('city') }}</label>
                                <div class="col-sm-6">
                                    <select name="city_id" class="form-control select2-class"  data-placeholder="{{ transLang('choose') }}">
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
                                <label class="col-sm-3 control-label required">{{ transLang('address') }}</label>
                                <div class="col-sm-6">
                                    <textarea class="form-control" name="address" placeholder="{{ transLang('address') }}" readonly></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label required">{{ transLang('status') }}</label>
                                <div class="col-sm-6">
                                    <select class="form-control select2-class" name="status"  data-placeholder="{{ transLang('is_new') }}">
                                        @foreach(transLang('other_action') as $key => $status)
                                            <option value="{{ $key }}">{{ $status }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label required">{{ transLang('image') }}</label>
                                <div class="col-sm-6">
                                    <img id="image-cropper-preview" style="display:none; float:left;" width="60">
                                    <input type="hidden" name="image">
                                    <input type="file" class="image-cropper" data-width="{{ $img_width }}" data-height="{{ $img_height }}" data-name="image">
                                    <small class="grey">{{ transLang('image_dimension') }}: <span class='dir-ltr'>{{ $img_width }} x {{ $img_height }}</span></small>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="box-footer">
                        <div class="col-sm-offset-1 col-sm-6">
                            <button type="button" class="btn btn-success" id="createBtn"><i class="fa fa-check"></i>  {{ transLang('create') }}</button>
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
            $('[name="is_new"]').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%' // optional
            });

            $(document).on('ifChecked', '[name="is_new"]', function (e) {
                let val = $('[name="is_new"]:checked').val();
                $('.click-type-options').hide();

                if (val == 1) {
                    $('.new-price').show();
                } else if (val == 0) {
                    $('.date').show();
                } 
            });

            $(document).on('change', '[name="is_delivery_available"]', function (e) {
                let val = $(this).val();
                $('.delivery_charges_wrapper').hide();
                if (val == 1) {
                    $('.delivery_charges_wrapper').show();
                }
            });
            
            $(document).on('change', '[name="category_id"]', function (e) {
                let id = $(this).val();
                let $target = $('[name="sub_category_id"]');

                $target.html('<option value=""></option>').trigger('change');

                $.get(`{{ route('admin.categories.subcategories.list') }}/${id}`, response => {
                    let html = '<option value=""></option>';
                    response.forEach(el => html += `<option value="${el.id}">${el.name}</option>`);
                    $target.html(html).trigger('change');
                }, 'json');
            });
            
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

            $(document).on('click', '#createBtn', function(e) {
                e.preventDefault();
                let btn = $(this);
                let loader = $('.message_box');

                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: "{{ route('admin.products.create') }}",
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
                        location.replace('{{ route('admin.products.index') }}');
                    }
                });
            });
        });
    </script>
@endsection
