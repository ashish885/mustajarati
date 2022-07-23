@extends('admin.layouts.master')

@section('title') {{ transLang('send_notifications') }} @endsection

@section('content')
    <section class="content-header">
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i> {{ transLang('dashboard') }}</a></li>
            <li class="active">{{ transLang('send_notifications') }}</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{ transLang('send_notifications') }}</h3>
                    </div>
                    <div class="box-body">
                        <p class="alert message_box hide"></p>
                        <form id="save-frm" class="form-horizontal">
                            @csrf
                            <div class="form-group">
                                <label class="col-sm-2 control-label required">{{ transLang('send_to') }}</label>
                                <div class="col-sm-6 form-inline">
                                    <div class="radio">
                                        <label><input type="radio" name="send_to" value="user" checked> {{ transLang('user') }}</label>
                                    </div>
                                    <div class="radio">
                                        <label><input type="radio" name="send_to" value="vendor"> {{ transLang('vendor') }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group user-wrapper send-to-wrapper">
                                <label class="col-sm-2 control-label">{{ transLang('users') }}</label>
                                <div class="col-sm-6">
                                    <select class="form-control" multiple name="user_id[]" data-placeholder="{{ transLang('all_users') }}">
                                    </select>
                                </div>
                            </div>
                            <div class="form-group vendor-wrapper send-to-wrapper" style="display: none;">
                                <label class="col-sm-2 control-label">{{ transLang('vendors') }}</label>
                                <div class="col-sm-6">
                                    <select class="form-control" multiple name="vendor_id[]" data-placeholder="{{ transLang('all_vendors') }}">
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label required">{{ transLang('title') }}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="title" placeholder="{{ transLang('title') }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label required">{{ transLang('en_title') }}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="en_title" placeholder="{{ transLang('en_title') }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label required">{{ transLang('ar_message') }}</label>
                                <div class="col-sm-6">
                                    <textarea class="form-control" name="ar_message" placeholder="{{ transLang('ar_message') }}"></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label required">{{ transLang('en_message') }}</label>
                                <div class="col-sm-6">
                                    <textarea class="form-control" name="en_message" placeholder="{{ transLang('en_message') }}"></textarea>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="box-footer">
                        <div class="col-sm-offset-1 col-sm-6">
                            <button type="button" class="btn btn-success" id="send-btn">
                                <i class="fa fa-send"></i> {{ transLang('send') }}
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
    $(function() {
        $(document).on('click', '[name="send_to"]', function (e) {
            let val = $('[name="send_to"]:checked').val();
            
            $('.send-to-wrapper').hide();
            $(`.${val}-wrapper`).show();
        });
        
        
        $('[name="user_id[]"]').select2({
            width: '100%',
            allowClear: true,
            minimumInputLength: 1,
            ajax: {
                url: "{{ route('admin.notifications.users.list') }}",
                dataType: 'json',
                type: 'POST',
                delay: 250,
                data: function(params) {
                    return {
                        term: params.term,
                        page: params.page,
                        _token: '{{ csrf_token() }}'
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.items,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            escapeMarkup: function(markup) {
                return markup;
            },
            templateResult: repo => {
                if (repo.loading) return repo.text;

                var markup = "<div class='select2-result-repository clearfix'>" +
                    "<div class='select2-result-repository__meta'>" +
                    "<div class='select2-result-repository__title'><i aria-hidden='true' class='fa fa-user'></i> " + repo.name + "</div>" +
                    "</div></div>";

                return markup;
            },
            templateSelection: repo => repo.name || repo.text
        });

        $('[name="vendor_id[]"]').select2({
            width: '100%',
            allowClear: true,
            minimumInputLength: 1,
            ajax: {
                url: "{{ route('admin.notifications.vendors.list') }}",
                dataType: 'json',
                type: 'POST',
                delay: 250,
                data: function(params) {
                    return {
                        term: params.term,
                        page: params.page,
                        _token: '{{ csrf_token() }}'
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.items,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            escapeMarkup: function(markup) {
                return markup;
            },
            templateResult: repo => {
                if (repo.loading) return repo.text;

                var markup = "<div class='select2-result-repository clearfix'>" +
                    "<div class='select2-result-repository__meta'>" +
                    "<div class='select2-result-repository__title'><i aria-hidden='true' class='fa fa-user'></i> " + repo.name + "</div>" +
                    "</div></div>";

                return markup;
            },
            templateSelection: repo => repo.name || repo.text
        });

        $(document).on('click', '#send-btn', function(e) {
            e.preventDefault();
            const btn = $(this);
            const loader = $('.message_box');
            
            $.ajax({
                dataType: 'json',
                type: 'POST',
                url: "{{ route('admin.notifications.send') }}",
                data: $('#save-frm').serialize(),
                beforeSend: function() {
                    btn.attr('disabled', true);
                    loader.html('{!! transLang("loader_message") !!}').removeClass('alert-success alert-danger hide').addClass('alert-info');
                },
                error: function(jqXHR, exception) {
                    btn.attr('disabled', false);
                    loader.html(formatErrorMessage(jqXHR, exception)).removeClass('alert-info').addClass('alert-danger');
                },
                success: function (data) {
                    btn.attr('disabled', false);
                    loader.html(data.message).removeClass('alert-info').addClass('alert-success');
                    // $('#save-frm')[0].reset();
                    // $('[name="user_id[]"]').empty().trigger('change');
                }
            });
        });
    });
</script>
@endsection