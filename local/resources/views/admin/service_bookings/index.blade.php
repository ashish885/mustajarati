@extends('admin.layouts.master')

@section('title') {{ transLang('all_service_booking') }} @endsection

@section('content')
    <section class="content-header">
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i>
                    {{ transLang('dashboard') }}</a></li>
            <li class="active">{{ transLang('all_service_booking') }}</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="row">
                            <div class="col-xs-12 col-sm-6">
                                <h3 class="box-title">{{ transLang('all_service_booking') }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-sm-3">  
                                <label class="control-label">{{ transLang('service') }}</label>
                                <div class="form-group">
                                    <select name="service_id" class="form-control select2-class2 reload-tbl" data-placeholder="{{ transLang('choose') }}">
                                        <option value=""></option>
                                        @if ($services->count())
                                            @foreach ($services as $service)
                                                <option value="{{ $service->id }}">{{ $service->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <label class="control-label">{{ transLang('user') }}</label>
                                <div class="form-group">
                                    <select name="user_id" class="form-control select2-class2 reload-tbl"
                                        data-placeholder="{{ transLang('choose') }}">
                                        <option value=""></option>
                                        @if ($users->count())
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <label class="control-label">{{ transLang('vendor') }}</label>
                                <div class="form-group">
                                    <select name="vendor_id" class="form-control select2-class2 reload-tbl"
                                        data-placeholder="{{ transLang('choose') }}">
                                        <option value=""></option>
                                        @if ($vendors->count())
                                            @foreach ($vendors as $vendor)
                                                <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <label class="control-label">{{ transLang('booking_status') }}</label>
                                <div class="form-group">
                                    <select name="booking_status" class="form-control select2-class2 reload-tbl"
                                        data-placeholder="{{ transLang('choose') }}">
                                        <option value=""></option>
                                        @foreach (transLang('service_booking_status_arr') as $key => $val)
                                            <option value="{{ $key }}">{{ $val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <label class="control-label">{{ transLang('payment_status') }}</label>
                                <div class="form-group">
                                    <select name="payment_status" class="form-control select2-class2 reload-tbl"
                                        data-placeholder="{{ transLang('choose') }}">
                                        <option value=""></option>
                                        @foreach (transLang('payment_status_arr') as $key => $val)
                                            <option value="{{ $key }}">{{ $val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3"> 
                                <label class="control-label">{{ transLang('date_range') }}</label> 
                                 <div class="form-group">
                                    <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                                        <i class="fa fa-calendar"></i>&nbsp;
                                        <span></span> <i class="fa fa-caret-down"></i>
                                    </div>
                                </div>
                            </div> 
                        </div>
                        <br>
                        <table class="table table-striped table-bordered table-hover dataTable" id="data-table">
                            <thead>
                                <tr>
                                    <th>{{ transLang('booking_code') }}</th>
                                    <th>{{ transLang('user') }}</th>
                                    <th>{{ transLang('vendor') }}</th>
                                    <th>{{ transLang('service') }}</th>
                                    <th>{{ transLang('total_amount') }}</th>
                                    <th>{{ transLang('payment_status') }}</th>
                                    <th>{{ transLang('submitted_at') }}</th>
                                    <th>{{ transLang('status') }}</th>
                                    <th>{{ transLang('action') }}</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script type="text/javascript">
        $(function() {
            const datePickerRange = {
                start: moment().add(1, 'days').subtract(7, 'days'), 
                end: moment()
            };

            function cb(start, end, reload_tbl = true) {
                datePickerRange.start = start;
                datePickerRange.end = end;

                $('#reportrange span').html(start.format('D MMM , YYYY') + ' - ' + end.format('D MMM, YYYY'));
                if (reload_tbl) {
                    reloadTable('data-table');
                }
            }

            $('#reportrange').daterangepicker({
                opens: '{{ getSessionLang() == "ar" ? 'right' : 'left'}}',
                dateLimit: { days: 30 },
                startDate: datePickerRange.start,
                endDate: datePickerRange.end,
                locale: {
                    format: 'YYYY-MM-DD',
                    applyLabel: '{{ transLang("apply") }}',
                    cancelLabel: '{{ transLang("cancel") }}',
                    customRangeLabel: '{{ transLang("custom") }}',
                },
                ranges: {
                    '{{ transLang("today") }}': [moment(), moment()],
                    '{{ transLang("yesterday") }}': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    '{{ transLang("last_7_days") }}': [moment().subtract(6, 'days'), moment()],
                    '{{ transLang("last_15_days") }}': [moment().subtract(15, 'days'), moment()],
                    '{{ transLang("this_month") }}': [moment().startOf('month'), moment().endOf('month')],
                    '{{ transLang("last_month") }}': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                },
            }, cb);
            cb(datePickerRange.start, datePickerRange.end, false);
            
            $(document).on('change', '.reload-tbl', function (e) {
                reloadTable('data-table');
            });

            $('#data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    type: 'GET',
                    url: '{{ route("admin.service_bookings.list") }}',
                    data: function(params) {
                        params.service_id = $('[name="service_id"]').val();
                        params.user_id = $('[name="user_id"]').val();
                        params.vendor_id = $('[name="vendor_id"]').val();
                        params.booking_status = $('[name="booking_status"]').val();
                        params.payment_status = $('[name="payment_status"]').val();
                        params.from_date = datePickerRange.start.lang('en').format('YYYY-MM-DD');
                        params.to_date = datePickerRange.end.lang('en').format('YYYY-MM-DD');
                    }
                },
                columns: [
                    @if (hasPermission('admin.service_bookings.view'))
                        {
                            data: "booking_code",
                            name: "service_bookings.booking_code",
                            mRender: (data, type, row) => `<a href="{{ route('admin.service_bookings.view') }}/${row.id}">${data}</a>`
                        },
                    @else
                        { data: "booking_code", name: "service_bookings.booking_code" },
                    @endif
                    @if (hasPermission('admin.users.view'))
                        {
                            data: "user_name",
                            name: "users.name",
                            mRender: (data, type, row) => row.user_id ? `<a href="{{ route('admin.users.view') }}/${row.user_id}" target="_blank">${data}</a>` : ``,
                        },
                    @else
                        { data: "user_name", name: "users.name"},
                    @endif
                    @if (hasPermission('admin.vendors.view'))
                        {
                            data: "vendor_name",
                            name: "vendors.name",
                            mRender: (data, type, row) => row.vendor_id ? `<a href="{{ route('admin.vendors.view') }}/${row.vendor_id}" target="_blank">${data}</a>` : ``,
                        },
                    @else
                        { data: "vendor_name", name: "vendors.name", },
                    @endif
                    @if (hasPermission('admin.services.view'))
                        {
                            data: "service_name",
                            name: "services.{{ $ql }}name",
                            mRender: (data, type, row) => row.service_id ? `<a href="{{ route('admin.services.view') }}/${row.service_id}" target="_blank">${data}</a>` : ``,
                        },
                    @else
                        { data: "service_name", name: "services.{{ $ql }}name" },
                    @endif
                    {
                        data: "total_amount",
                        name: "service_bookings.total_amount",
                        mRender: data => `${formatMoney(data)} {{ transLang('sar') }}`
                    },
                    {
                        data: "payment_status_text",
                        name: "service_bookings.payment_status"
                    },
                    {
                        data: "created_at",
                        mRender: data => formatDate(data)
                    },
                    {
                        data: "status_text",
                        name: "service_bookings.status"
                    },
                    {
                        mRender: (data, type, row) => {
                            return `
                                @if (hasPermission('admin.service_bookings.view'))
                                    <a href="{{ route('admin.service_bookings.view') }}/${row.id}"><i class="fa fa-eye fa-fw"></i></a>
                                @endif
                                
                                @if (hasPermission('admin.service_bookings.delete'))
                                    <a href="{{ route('admin.service_bookings.delete') }}/${row.id}" class="delete-entry" data-tbl="data"><i
                                            class="fa fa-trash fa-fw"></i></a>
                                @endif
                            `;
                        },
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        });
    </script>
@endsection
