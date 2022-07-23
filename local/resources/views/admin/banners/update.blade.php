@extends('admin.layouts.master')

@section('title') {{ transLang('update_banner') }} @endsection

@section('content')
    <section class="content-header">
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i> {{ transLang('dashboard') }}</a></li>
            <li><a href="{{ route('admin.banners.index') }}">{{ transLang('all_banners') }}</a></li>
            <li class="active">{{ transLang('update_banner') }}</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{ transLang('update_banner') }}</h3>
                    </div>
                    <div class="box-body">
                        <p class="alert message_box hide"></p>
                        <form id="save-frm" class="form-horizontal">
                            @csrf
                            <div class="form-group">
                                <label class="col-sm-2 control-label required">{{ transLang('image') }}</label>
                                <div class="col-sm-6">
                                    @if ($banner->image)
                                        <img src="{{ imageBasePath($banner->image) }}" width="80">
                                    @endif
                                    <input type="file" name="image">
                                    <small class="grey">{{ transLang('image_dimension') }}: <span class="dir-ltr">{{ $imgDimension[0] }} x {{ $imgDimension[1] }}</span></small>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label required">{{ transLang('en_image') }}</label>
                                <div class="col-sm-6">
                                    @if ($banner->en_image)
                                        <img src="{{ imageBasePath($banner->en_image) }}" width="80">
                                    @endif
                                    <input type="file" name="en_image">
                                    <small class="grey">{{ transLang('image_dimension') }}: <span class="dir-ltr">{{ $imgDimension[0] }} x {{ $imgDimension[1] }}</span></small>
                                </div>
                            </div>
                            <div class="form-group hide">
                                <label class="col-sm-2 control-label required">{{ transLang('click_type') }}</label>
                                <div class="col-sm-6">
                                    <div class="radio icheck">
                                        @foreach (transLang('click_type_arr') as $key => $val)
                                            <label>
                                                <input type="radio" name="click_type" value="{{ $key }}" {{ $key == $banner->click_type ? 'checked' : '' }}> {{ $val }}&nbsp;
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="form-group products-option click-type-options" style="display: none;">
                                <label class="col-sm-2 control-label required">{{ transLang('product') }}</label>
                                <div class="col-sm-6">
                                    <select name="product" class="form-control select2-class" data-placeholder="{{ transLang('choose') }}">
                                        <option value=""></option>
                                        @if ($products->count())
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}" {{ $banner->product_id == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-group services-option click-type-options" style="display: none;">
                                <label class="col-sm-2 control-label required">{{ transLang('service') }}</label>
                                <div class="col-sm-6">
                                    <select name="service" class="form-control select2-class" data-placeholder="{{ transLang('choose') }}">
                                        <option value=""></option>
                                        @if ($services->count())
                                            @foreach ($services as $service)
                                                <option value="{{ $service->id }}" {{ $banner->service_id == $service->id ? 'selected' : '' }}>{{ $service->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label required">{{ transLang('status') }}</label>
                                <div class="col-sm-6">
                                    <select class="form-control" name="status">
                                        @foreach(transLang('action_status') as $key => $status)
                                            <option value="{{ $key }}" {{ $key == $banner->status ? 'selected' : '' }}>{{ $status }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="box-footer">
                        <div class="col-sm-offset-1 col-sm-6">
                            <button type="button" class="btn btn-success" id="updateBtn"><i class="fa fa-check"></i> {{ transLang('update') }}</button>
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
            $('[name="click_type"]').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%' // optional
            });

            $(document).on('ifChecked', '[name="click_type"]', function (e) {
                let val = $('[name="click_type"]:checked').val();
                $('.click-type-options').hide();

                if (val == 2) {
                    $('.products-option').show();
                } else if (val == 3) {
                    $('.services-option').show();
                }
            });

            $(document).on('click','#updateBtn', function (e) {
                e.preventDefault();
                let btn = $(this);
                let loader = $('.message_box');
                
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: "{{ route('admin.banners.update', $banner->id) }}",
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
                        location.replace('{{ route("admin.banners.index")}}');
                    }
                });
            });
        });
    </script>
@endsection