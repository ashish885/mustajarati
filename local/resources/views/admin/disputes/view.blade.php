@extends('admin.layouts.master')

@section('title') {{ transLang('detail') }} @endsection

@section('content')
    <section class="content-header">
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i>{{ transLang('dashboard') }}</a>
            </li>
            <li><a href="{{ route("admin.disputes.index") }}"> {{ transLang('all_dispute') }} </a></li>
            <li class="active">{{ transLang('detail') }}</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="row">
                            <div class="col-sm-7">
                                <h3 class="box-title">{{ transLang('ticket_no') }} #{{ $dispute->ticket_no }}</h3>
                                <div style="margin-top: 5px">{{ transLang('ticket_status') }}: {{ $dispute->status == 3 ? transLang('dispute_status_arr')[3] : ($dispute->last_message_by == 1 ? transLang('dispute_status_arr')[2] : transLang('dispute_status_arr')[1]) }}</div>

                                @php $postedByLink = $dispute->raised_by == 1 ? route('admin.users.view', $dispute->user_id) : route('admin.vendors.view', $dispute->vendor_id); @endphp
                                <div style="margin-top: 5px">{{ transLang('ticket_raised_by') }}: <a href="{{ $postedByLink }}" target="_blank">{{ $dispute->posted_by }}</a> ({{ transLang($dispute->raised_by == 1 ? 'user' : 'vendor') }})</div>
                            </div>
                            <div class="col-sm-5 text-right">
                                <a href="{{ route('admin.disputes.post.reply', $dispute->id) }}" data-toggle="modal" data-target="#remote_model" class="btn btn-primary"><i class="fa fa-pencil"></i> {{ transLang('reply') }}</a>
                                @if ($dispute->status != 3)
                                    <button class="btn btn-warning closeTicketBtn"><i class="fa fa-check"></i> {{ transLang('close_ticket') }}</button>
                                @endif
                            </div>
                        </div>
                        <h4>{{ transLang('subject') }}: {{ $dispute->subject }}</h4>
                    </div>
                    <div class="box-body">
                        <ul id="timeline-wrapper" class="timeline timeline-inverse"></ul>
                    </div>
                    <div class="box-footer text-right">
                        <a href="{{ route('admin.disputes.post.reply', $dispute->id) }}" data-toggle="modal" data-target="#remote_model" class="btn btn-primary"><i class="fa fa-pencil"></i> {{ transLang('reply') }}</a>
                        @if ($dispute->status != 3)
                            <button class="btn btn-warning closeTicketBtn"><i class="fa fa-check"></i> {{ transLang('close_ticket') }}</button>
                        @endif
                    </div>
                </div>
            </div>
    </section>
@endsection
 
@section('scripts')
    <script>
        jQuery(function ($) {
            (function () {
                const DISPUTE_MESSAGES = @json($dispute->details);
                let $target = $('#timeline-wrapper');
                let lastDate = '';
                let postedByMsg = '{!! transLang("posted_by") !!}';

                DISPUTE_MESSAGES.forEach(el => {
                    let postedBy = el.is_from_admin ? '{{ transLang("admin") }}' : '{{ $dispute->posted_by }}'

                    $target.append(`
                        ${ lastDate == '' || lastDate != formatDate(el.created_at, 'DD MMM, YYYY') ? `<li class="time-label"><span class="bg-red">${formatDate(el.created_at, 'DD MMM, YYYY')}</span></li>` : ''}
                        
                        <li>
                            <i class="fa fa-envelope bg-blue"></i>
                            
                            <div class="timeline-item">
                                <span class="time"><i class="fa fa-clock-o"></i> ${formatDate(el.created_at, 'hh:mm A')}</span>
                
                                <h3 class="timeline-header">${postedByMsg.replace(/:name/g, postedBy)} <small class="label label-${el.is_from_admin ? 'info' : 'success'}">${el.is_from_admin ? '{{ transLang("admin") }}' : '{{ transLang("owner") }}' }</small></h3>
                
                                <div class="timeline-body">${nl2br(el.message)}</div>
                                
                                ${ el.attachment ? `<div class="timeline-footer"><a href="{{ imageBasePath() }}/${el.attachment}" class="btn bg-maroon btn-xs" target="_blank" download><i class="fa fa-download"></i> {{ transLang('download_file') }}</a></div>` : '' }
                            </div>
                        </li>
                    `);
                    if (lastDate == '' || lastDate != formatDate(el.created_at, 'DD MMM, YYYY')) {
                        lastDate = formatDate(el.created_at, 'DD MMM, YYYY');
                    }
                });
                $target.append(`<li><i class="fa fa-clock-o bg-gray"></i></li>`);
            })();

            $(document).on('click', '.closeTicketBtn', async function (e) {
                e.preventDefault();

                if (await confirmAlert()) {
                    var href = $(this).attr('href');
                    $.get('{{ route("admin.disputes.close.ticket", $dispute->id) }}' , () => location.reload(true))
                    .fail((jqXHR, exception) => infoAlert($(formatErrorMessage(jqXHR, exception)).text()));
                }
            })
        });
    </script>
@endsection