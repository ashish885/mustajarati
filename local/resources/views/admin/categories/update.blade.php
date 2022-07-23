@extends('admin.layouts.master')

@section('title') {{ transLang('update_category') }} @endsection

@section('content')
    <section class="content-header">
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i> {{ transLang('dashboard') }}</a></li>
            <li><a href="{{ route('admin.categories.index') }}">{{ transLang('all_categories') }}</a></li>
            <li class="active">{{ transLang('update_category') }}</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{ transLang('update_category') }}</h3>
                    </div>
                    <div class="box-body">
                        <p class="alert message_box hide"></p>
                        <form id="save-frm" class="form-horizontal">
                            @csrf
                            <div class="form-group">
                                <label class="col-sm-2 control-label required">{{ transLang('category_for') }}</label>
                                <div class="col-sm-6">
                                    <select name="category_for" class="select2-class" data-placeholder="{{ transLang('choose') }}">
                                        <option value=""></option>
                                        @foreach (transLang('category_types') as $key => $val)
                                            <option value="{{ $key }}" {{ $category->type == $key ? 'selected' : '' }}>{{ $val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">{{ transLang('parent_category') }}</label>
                                <div class="col-sm-6">
                                    <select name="parent_category" class="form-control select2-class2" data-placeholder="{{ transLang('choose') }}">
                                        <option value=""></option>
                                        @if ($categories->count())
                                            @foreach ($categories as $row)
                                                <option value="{{ $row->id }}" {{ $row->id == $category->parent_id ? 'selected' : '' }}>{{ $row->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label required">{{ transLang('ar_name') }}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="ar_name" placeholder="{{ transLang('ar_name') }}" value="{{ $category->name }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label required">{{ transLang('en_name') }}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="en_name" placeholder="{{ transLang('en_name') }}" value="{{ $category->en_name }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label required">{{ transLang('status') }}</label>
                                <div class="col-sm-6">
                                    <select class="form-control" name="status">
                                        @foreach(transLang('action_status') as $key => $status)
                                        <option value="{{ $key }}" {{ $key == $category->status ? 'selected' : '' }}>{{ $status }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label required">{{ transLang('image') }}</label>
                                <div class="col-sm-6">
                                    @if ($category->image)
                                        <img id="image-cropper-preview" src="{{ imageBasePath($category->image) }}" width="60">
                                    @else
                                        <img id="image-cropper-preview" width="60">
                                    @endif
                                    <input type="hidden" name="image">
                                    <input type="file" class="image-cropper" data-width="{{ $img_width }}" data-height="{{ $img_height }}" data-name="image">
                                    <small class="grey">{{ transLang('image_dimension') }}: <span class='dir-ltr'>{{ $img_width }} x {{ $img_height }}</span></small>
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
            $(document).on('click','#updateBtn', function (e) {
                e.preventDefault();
                let btn = $(this);
                let loader = $('.message_box');
                
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: "{{ route('admin.categories.update', $category->id) }}",
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
                        location.replace('{{ route("admin.categories.index")}}');
                    }
                });
            });
        });
    </script>
@endsection