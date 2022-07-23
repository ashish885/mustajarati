@extends('admin.layouts.master')

@section('title') {{ transLang('update_booking_questions') }} @endsection

@section('content')
    <section class="content-header">
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i> {{ transLang('dashboard') }}</a></li>
            <li><a href="{{ route('admin.booking_questions.index') }}">{{ transLang('all_booking_cancellation_question') }}</a></li>
            <li class="active">{{ transLang('update_booking_questions') }}</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{ transLang('update_booking_questions') }}</h3>
                    </div>
                    <div class="box-body">
                        <p class="alert message_box hide"></p>
                        <form id="save-frm" class="form-horizontal">
                            @csrf
                            <div class="form-group">
                                <label class="col-sm-2 control-label required">{{ transLang('question_for') }}</label>
                                <div class="col-sm-6">
                                    <select class="form-control select2-class" name="question_for" data-placeholder="{{ transLang('choose') }}">
                                        <option value=""></option>
                                        @foreach(transLang('booking_question_types') as $key => $val)
                                            <option value="{{ $key }}" {{ $booking_questions->type == $key ? 'selected' : '' }}>{{ $val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label required">{{ transLang('name') }}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="question" placeholder="{{ transLang('name') }}" value="{{ $booking_questions->question }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label required">{{ transLang('en_name') }}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="en_question" placeholder="{{ transLang('en_name') }}" value="{{ $booking_questions->en_question }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label required">{{ transLang('status') }}</label>
                                <div class="col-sm-6">
                                    <select class="form-control" name="status">
                                        @foreach(transLang('action_status') as $key => $status)
                                        <option value="{{ $key }}" {{ $key == $booking_questions->status ? 'selected' : '' }}>{{ $status }}</option>
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
            $(document).on('click','#updateBtn', function (e) {
                e.preventDefault();
                let btn = $(this);
                let loader = $('.message_box');
                
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: "{{ route('admin.booking_questions.update', $booking_questions->id) }}",
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
                        location.replace('{{ route("admin.booking_questions.index")}}');
                    }
                });
            });
        });
    </script>
@endsection