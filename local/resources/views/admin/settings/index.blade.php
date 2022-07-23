@extends('admin.layouts.master') 

@section('title') {{ transLang('app_settings') }} @endsection
 
@section('content')
    <section class="content-header">
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i> {{ transLang('dashboard') }} </a></li>
            <li class="active">{{ transLang('app_settings') }}</li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <section class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{ transLang('app_settings') }}</h3>
                    </div>
                    <div class="box-body">
                        <p class="alert message_box hide"></p>
                        <form class="form-horizontal" id="save-frm">
                            @csrf
                            <div class="col-md-12">
                                @if($settings->isNotEmpty())
                                    @foreach($settings as $value)
                                        @if($value->is_file)
                                            <div class="col-md-12">
                                                <div class="box-body" style="padding-top:0px;">
                                                    <div class="form-group" style="margin-bottom: 0px !important;">
                                                        <label for="{{ $value->attribute }}" class="control-label">{{ transLang($value->attribute) }}</label>
                                                        @if($value->value)
                                                            <img src="{{ imageBasePath($value->value) }}" width="60"/>
                                                        @endif
                                                        <input type="file" name="{{$value->attribute}}">
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif($value->is_single)
                                            <div class="col-md-6">
                                                <div class="box-body" style="padding-top:0px;">
                                                    <div class="form-group" style="margin-bottom: 0px !important;">
                                                        <label class="control-label required">{{ transLang($value->attribute) }}</label>
                                                        @if($value->is_textarea)
                                                            <textarea rows="1" class="form-control {{ $value->is_simple ? '' : 'textarea' }}" name="field[{{$value->id}}][{{ $value->attribute }}]">{{ $value->value }}</textarea>
                                                        @elseif($value->is_radio)
                                                            <div class="form-inline">
                                                                @foreach (transLang('other_action') as $key => $val)
                                                                    <div class="radio">
                                                                        <label>
                                                                            <input type="radio" value="{{ $key }}" name="field[{{$value->id}}][{{ $value->attribute }}]" {{ $value->value == $key ? 'checked' : '' }}> {{ $val }}
                                                                        </label>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            <input type="text" class="form-control" name="field[{{$value->id}}][{{ $value->attribute }}]" value="{{ $value->value }}">
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="col-md-6">
                                                <div class="box-body" style="padding-top:0px;">
                                                    <div class="form-group" style="margin-bottom: 0px !important;">
                                                        <label class="control-label required">{{ transLang("ar_{$value->attribute}") }}</label>
                                                        @if($value->is_textarea)
                                                            <textarea rows="1" class="form-control {{ $value->is_simple ? '' : 'textarea' }}" name="field[{{$value->id}}][ar_{{ $value->attribute }}]">{{ $value->value }}</textarea>
                                                        @else
                                                            <input type="text" class="form-control" name="field[{{$value->id}}][ar_{{ $value->attribute }}]" value="{{ $value->value }}">
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="box-body" style="padding-top:0px;">
                                                    <div class="form-group" style="margin-bottom: 0px !important;">
                                                        <label class="control-label required">{{ transLang("en_{$value->attribute}") }}</label>
                                                        @if($value->is_textarea)
                                                            <textarea rows="1" class="form-control {{ $value->is_simple ? '' : 'textarea' }}" name="field[{{$value->id}}][en_{{ $value->attribute }}]">{{ $value->en_value }}</textarea>
                                                        @else
                                                            <input type="text" class="form-control" name="field[{{$value->id}}][en_{{ $value->attribute }}]" value="{{ $value->en_value }}">
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        </form>
                    </div>
                    <div class="box-footer">
                        <div class="col-sm-offset-1 col-sm-6">
                            <button type="button" class="btn btn-success" id="updateBtn"><i class="fa fa-check"></i> {{ transLang('update') }}</button>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </section>
@endsection
 
@section('scripts')

<script type="text/javascript">
    jQuery(function($) {
        $('textarea.textarea').summernote({
            height: 150,
            toolbar: [
                [
                    'mics', [
                        'bold', 'italic', 'underline',
                        'ul', 'ol',
                        // 'codeview'
                    ]
                ]
            ],
            lang: '{{ getSessionLang() == "ar" ? "ar-AR" : "en-US" }}',
        });
        
      $(document).on('click','#updateBtn',function(e) {
            e.preventDefault();
            const btn = $(this);
            const loader = $('.message_box');
            
            $.ajax({
                dataType: 'json',
                type: 'POST',
                url: "{{ route('admin.settings.update') }}",
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
    });

</script>
@endsection