@extends('admin.layouts.master') 

@section('title') {{ transLang('dashboard') }} @endsection
 
@section('content')
    <section class="content-header">
        <h1> {{ transLang('dashboard') }}</h1>
        <ol class="breadcrumb">
            <li class="active"><i class="fa fa-dashboard"></i> {{ transLang('dashboard') }}</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            @hasPermission('admin.users.index')
                <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                    <div class="small-box bg-green">
                        <div class="inner">
                            <h3 class="total_users"><i class="fa fa-spin fa-spinner"></i></h3>
                            <p>{{ transLang('users') }}</p>
                        </div>
                        <div class="icon"><i class="fa fa-users"></i></div>
                        <a href="{{ route('admin.users.index') }}" class="small-box-footer">{{ transLang('more_info') }} <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            @endhasPermission

            @hasPermission('admin.vendors.index')
                <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                    <div class="small-box bg-blue">
                        <div class="inner">
                            <h3 class="total_vendors"><i class="fa fa-spin fa-spinner"></i></h3>
                            <p>{{ transLang('vendors') }}</p>
                        </div>
                        <div class="icon"><i class="fa fa-users"></i></div>
                        <a href="{{ route('admin.vendors.index') }}" class="small-box-footer">{{ transLang('more_info') }} <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            @endhasPermission

            @hasPermission('admin.product_bookings.index')
                <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                    <div class="small-box bg-teal">
                        <div class="inner">
                            <h3 class="total_product_bookings"><i class="fa fa-spin fa-spinner"></i></h3>
                            <p>{{ transLang('product_bookings') }}</p>
                        </div>
                        <div class="icon"><i class="fa fa-cubes"></i></div>
                        <a href="{{ route('admin.product_bookings.index') }}" class="small-box-footer">{{ transLang('more_info') }} <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            @endhasPermission

            @hasPermission('admin.service_bookings.index')
                <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                    <div class="small-box bg-cyan">
                        <div class="inner">
                            <h3 class="total_service_bookings"><i class="fa fa-spin fa-spinner"></i></h3>
                            <p>{{ transLang('service_bookings') }}</p>
                        </div>
                        <div class="icon"><i class="fa fa-cogs"></i></div>
                        <a href="{{ route('admin.service_bookings.index') }}" class="small-box-footer">{{ transLang('more_info') }} <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            @endhasPermission

            @hasPermission('admin.disputes.index')
                <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                    <div class="small-box bg-aqua">
                        <div class="inner">
                            <h3 class="total_open_disputes"><i class="fa fa-spin fa-spinner"></i></h3>
                            <p>{{ transLang('open_disputes') }}</p>
                        </div>
                        <div class="icon"><i class="fa fa-gavel"></i></div>
                        <a href="{{ route('admin.disputes.index') }}" class="small-box-footer">{{ transLang('more_info') }} <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            @endhasPermission

            @hasPermission('admin.inquiries.users.index')
                <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                    <div class="small-box bg-purple">
                        <div class="inner">
                            <h3 class="total_user_inquiries"><i class="fa fa-spin fa-spinner"></i></h3>
                            <p>{{ transLang('user_inquiries') }}</p>
                        </div>
                        <div class="icon"><i class="fa fa-question-circle"></i></div>
                        <a href="{{ route('admin.inquiries.users.index') }}" class="small-box-footer">{{ transLang('more_info') }} <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            @endhasPermission

            @hasPermission('admin.inquiries.vendors.index')
                <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                    <div class="small-box bg-orange">
                        <div class="inner">
                            <h3 class="total_vendor_inquiries"><i class="fa fa-spin fa-spinner"></i></h3>
                            <p>{{ transLang('vendor_inquiries') }}</p>
                        </div>
                        <div class="icon"><i class="fa fa-question-circle"></i></div>
                        <a href="{{ route('admin.inquiries.vendors.index') }}" class="small-box-footer">{{ transLang('more_info') }} <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            @endhasPermission

            @if ($admin->id == 1)
                <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                    <div class="small-box bg-red">
                        <div class="small-box-footer" style="background-color: #e3342f;">&nbsp;</div>
                        <div class="inner">
                            <h3 style="font-size: 30px;">{{ transLang('sar') }} {{ $admin_amount }}</h3>
                            <p>{{ transLang('my_earnings') }}</p>
                        </div>
                        <div class="icon"><i class="fa fa-money"></i></div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Graphs -->
        <div class="row">
            <!-- Earnings Graph -->
            @if ($admin->id == 1)
                <div class="col-sm-12">
                    <div class="box box-danger">
                        <div class="box-header with-border">
                            <h3 class="box-title">
                                {{ transLang('earnings_graph') }}
                            </h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-danger earnings_date_range"><i class="fa fa-calendar"></i></button>
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="chart">
                                        <canvas id="earningsChart" height="250" style="height: 250px;"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Product Booking Graph -->
            @hasPermission('admin.product_bookings.index')
                <div class="col-sm-6">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">
                                {{ transLang('product_bookings_graph') }}
                            </h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-success pbooking_date_range"><i class="fa fa-calendar"></i></button>
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="chart">
                                        <canvas id="pbookingChart" height="250" style="height: 250px;"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endhasPermission

            <!-- Service Booking Graph -->
            @hasPermission('admin.service_bookings.index')
                <div class="col-sm-6">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">
                                {{ transLang('service_bookings_graph') }}
                            </h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-success sbooking_date_range"><i class="fa fa-calendar"></i></button>
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="chart">
                                        <canvas id="sbookingChart" height="250" style="height: 250px;"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endhasPermission

            <!-- Product Booking Table -->
            @hasPermission('admin.product_bookings.index')
                <div class="col-sm-6">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">
                                {{ transLang('product_bookings') }}
                            </h3>
                        </div>
                        <div class="box-body">
                            <table class="table table-striped table-bordered table-hover dataTable" id="product-bookings-table">
                                <thead>
                                    <tr>
                                        <th>{{ transLang('action') }}</th>
                                        <th>{{ transLang('booking_code') }}</th>
                                        <th>{{ transLang('total_amount') }}</th>
                                        <th>{{ transLang('payment_status') }}</th>
                                        <th>{{ transLang('status') }}</th>
                                        <th>{{ transLang('booking_date') }}</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endhasPermission

            <!-- Service Booking Table -->
            @hasPermission('admin.service_bookings.index')
                <div class="col-sm-6">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">
                                {{ transLang('service_bookings') }}
                            </h3>
                        </div>
                        <div class="box-body">
                            <table class="table table-striped table-bordered table-hover dataTable" id="service-bookings-table">
                                <thead>
                                    <tr>
                                        <th>{{ transLang('action') }}</th>
                                        <th>{{ transLang('booking_code') }}</th>
                                        <th>{{ transLang('total_amount') }}</th>
                                        <th>{{ transLang('payment_status') }}</th>
                                        <th>{{ transLang('status') }}</th>
                                        <th>{{ transLang('booking_date') }}</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endhasPermission

            <!-- Users Graph -->
            @hasPermission('admin.users.index')
                <div class="col-sm-6">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">
                                {{ transLang('users_registration') }}
                            </h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-success users_date_range"><i class="fa fa-calendar"></i></button>
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="chart">
                                        <canvas id="usersChart" height="250" style="height: 250px;"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endhasPermission

            <!-- Vendors Graph -->
            @hasPermission('admin.vendors.index')
                <div class="col-sm-6">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">
                                {{ transLang('vendors_registration') }}
                            </h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-success vendors_date_range"><i class="fa fa-calendar"></i></button>
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="chart">
                                        <canvas id="vendorsChart" height="250" style="height: 250px;"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endhasPermission

            <!-- Users Table -->
            @hasPermission('admin.users.index')
                <div class="col-sm-6">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">
                                {{ transLang('users') }}
                            </h3>
                        </div>
                        <div class="box-body">
                            <table class="table table-striped table-bordered table-hover dataTable" id="users-table">
                                <thead>
                                    <tr>
                                        <th>{{ transLang('action') }}</th>
                                        <th>{{ transLang('name') }}</th>
                                        <th>{{ transLang('email') }}</th>
                                        <th>{{ transLang('mobile') }}</th>
                                        <th>{{ transLang('status') }}</th>
                                        <th>{{ transLang('registered_on') }}</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endhasPermission

            <!-- Users Table -->
            @hasPermission('admin.vendors.index')
                <div class="col-sm-6">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">
                                {{ transLang('vendors') }}
                            </h3>
                        </div>
                        <div class="box-body">
                            <table class="table table-striped table-bordered table-hover dataTable" id="vendors-table">
                                <thead>
                                    <tr>
                                        <th>{{ transLang('action') }}</th>
                                        <th>{{ transLang('name') }}</th>
                                        <th>{{ transLang('email') }}</th>
                                        <th>{{ transLang('mobile') }}</th>
                                        <th>{{ transLang('status') }}</th>
                                        <th>{{ transLang('registered_on') }}</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endhasPermission
        </div>
    </section>
@endsection

@section('scripts')
    <script type="text/javascript">
        $(function() {
            let datePickerRange = {
                '{{ transLang("today") }}': [moment(), moment()],
                '{{ transLang("yesterday") }}': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                '{{ transLang("last_7_days") }}': [moment().subtract(6, 'days'), moment()],
                '{{ transLang("last_15_days") }}': [moment().subtract(15, 'days'), moment()],
                '{{ transLang("this_month") }}': [moment().startOf('month'), moment().endOf('month')],
                '{{ transLang("last_month") }}': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            }, 
            datePickerLocale = {
                format: 'YYYY-MM-DD',
                applyLabel: '{{ transLang("apply") }}',
                cancelLabel: '{{ transLang("cancel") }}',
                customRangeLabel: '{{ transLang("custom") }}',
            }, 
            earningsDateRange = {
                start: moment().add(1, 'days').subtract(7, 'days'), 
                end: moment()
            }, productBookingDateRange = {
                start: moment().add(1, 'days').subtract(7, 'days'), 
                end: moment()
            }, serviceBookingDateRange = {
                start: moment().add(1, 'days').subtract(7, 'days'), 
                end: moment()
            }, usersDateRange = {
                start: moment().add(1, 'days').subtract(7, 'days'), 
                end: moment()
            }, vendorsDateRange = {
                start: moment().add(1, 'days').subtract(7, 'days'), 
                end: moment()
            };

            getStats();

            @if ($admin->id == 1)
                setTimeout(getEarningsGraphData, 100);

                // Earnings Chart
                const earningsChart = new Chart($('#earningsChart').get(0).getContext('2d'), {
                    type: 'line',
                    data: { labels: [], datasets: [] },
                    options: {
                        responsive: true,
                        tooltips: {
                            intersect: false,
                            position: 'nearest',
                            mode: 'index',
                        },
                    }
                });

                initDateRange($('.earnings_date_range'), earningsDateRange, (start, end) => {
                    earningsDateRange.start = start;
                    earningsDateRange.end = end;

                    getEarningsGraphData();
                });
                
                function getEarningsGraphData() {
                    $.ajax({
                        dataType: 'json',
                        type: 'GET',
                        url: '{{ route("admin.dashboard.earnings.graph") }}',
                        data: {
                            start_date: earningsDateRange.start.lang('en').format('YYYY-MM-DD'),
                            end_date: earningsDateRange.end.lang('en').format('YYYY-MM-DD'),
                        },
                        success: function (response) {
                            earningsChart.data = {
                                labels: response.labels,
                                datasets: [
                                    {
                                        label: '{{ translang("bookings") }}',
                                        data: response.bookings,
                                        borderColor: "rgb(52, 144, 220)",
                                        backgroundColor: 'rgb(52, 144, 220, .4)',
                                        borderWidth: 1,
                                    },
                                    {
                                        label: '{{ translang("earnings") }}',
                                        data: response.earnings,
                                        borderColor: "rgb(82, 142, 47)",
                                        backgroundColor: 'rgb(82, 142, 47, .4)',
                                        borderWidth: 1,
                                    }
                                ]
                            };
                            earningsChart.update();
                        }
                    });
                }
            @endif

            @if (hasPermission('admin.product_bookings.index'))
                setTimeout(() => {
                    getGraphData('{{ route("admin.dashboard.product_bookings.graph") }}', productBookingDateRange, pbookingChart, '{{ transLang("bookings") }}');
                    reloadTable('product-bookings-table');
                }, 400);

                // Product Booking Chart
                const pbookingChart = initGraph($('#pbookingChart'));

                initDateRange($('.pbooking_date_range'), productBookingDateRange, (start, end) => {
                    productBookingDateRange.start = start;
                    productBookingDateRange.end = end;

                    getGraphData('{{ route("admin.dashboard.product_bookings.graph") }}', productBookingDateRange, pbookingChart, '{{ transLang("bookings") }}');
                    reloadTable('product-bookings-table');
                });

                $('#product-bookings-table').DataTable({
                    processing: true,
                    serverSide: true,
                    deferLoading: false,
                    scrollY: '300px',
                    ajax: {
                        type: 'GET',
                        url: '{{ route("admin.product_bookings.list") }}',
                        data: function (params) {
                            params.from_date = productBookingDateRange.start.lang('en').format('YYYY-MM-DD');
                            params.to_date = productBookingDateRange.end.lang('en').format('YYYY-MM-DD');
                        }
                    },
                    columns : [
                        {
                            mRender: (data, type, row) => {
                                return `
                                    @if (hasPermission('admin.product_bookings.view'))
                                        <a href="{{ route("admin.product_bookings.view") }}/${row.id}" ><i class="fa fa-eye fa-fw"></i></a>
                                    @endif
                                `;
                            }, 
                            orderable: false,
                            searchable: false
                        },
                        { data: "booking_code", name: "product_bookings.booking_code" },
                        { data: "total_amount", name: "product_bookings.total_amount", mRender: data => `${formatMoney(data)} {{ transLang('sar') }}` },
                        { data: "payment_status_text", name: "product_bookings.payment_status" },
                        { data: "status_text", name: "product_bookings.status" },
                        { data: "created_at", mRender: data => formatDate(data, 'DD MMM, YYYY') },
                    ]
                });
            @endif

            @if (hasPermission('admin.service_bookings.index'))
                setTimeout(() => {
                    getGraphData('{{ route("admin.dashboard.service_bookings.graph") }}', serviceBookingDateRange, sbookingChart, '{{ transLang("bookings") }}');
                    reloadTable('service-bookings-table');
                }, 400);

                // Service Booking Chart
                const sbookingChart = initGraph($('#sbookingChart'));

                initDateRange($('.sbooking_date_range'), serviceBookingDateRange, (start, end) => {
                    serviceBookingDateRange.start = start;
                    serviceBookingDateRange.end = end;

                    getGraphData('{{ route("admin.dashboard.service_bookings.graph") }}', serviceBookingDateRange, sbookingChart, '{{ transLang("bookings") }}');
                    reloadTable('service-bookings-table');
                });

                $('#service-bookings-table').DataTable({
                    processing: true,
                    serverSide: true,
                    deferLoading: false,
                    scrollY: '300px',
                    ajax: {
                        type: 'GET',
                        url: '{{ route("admin.service_bookings.list") }}',
                        data: function(params) {
                            params.from_date = serviceBookingDateRange.start.lang('en').format('YYYY-MM-DD');
                            params.to_date = serviceBookingDateRange.end.lang('en').format('YYYY-MM-DD');
                        }
                    },
                    columns: [
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
                        },
                        { data: "booking_code", name: "service_bookings.booking_code" },
                        { data: "total_amount", name: "service_bookings.total_amount", mRender: data => `${formatMoney(data)} {{ transLang('sar') }}` },
                        { data: "payment_status_text", name: "service_bookings.payment_status" },
                        { data: "status_text", name: "service_bookings.status" },
                        { data: "created_at", mRender: data => formatDate(data, 'DD MMM, YYYY') },
                    ]
                });
            @endif

            @if (hasPermission('admin.users.index'))
                setTimeout(() => {
                    getGraphData('{{ route("admin.dashboard.users.graph") }}', usersDateRange, usersChart, '{{ transLang("users") }}');
                    reloadTable('users-table');
                }, 800);

                // Users Chart
                const usersChart = initGraph($('#usersChart'));

                initDateRange($('.users_date_range'), usersDateRange, (start, end) => {
                    usersDateRange.start = start;
                    usersDateRange.end = end;

                    getGraphData('{{ route("admin.dashboard.users.graph") }}', usersDateRange, usersChart, '{{ transLang("users") }}');
                    reloadTable('users-table');
                });

                $('#users-table').DataTable({
                    processing: true,
                    serverSide: true,
                    deferLoading: false,
                    scrollY: '300px',
                    ajax: {
                        type: 'GET',
                        url: '{{ route("admin.users.list") }}',
                        data: function(params) {
                            params.from_date = usersDateRange.start.lang('en').format('YYYY-MM-DD');
                            params.to_date = usersDateRange.end.lang('en').format('YYYY-MM-DD');
                        }
                    },
                    columns : [
                        {
                            mRender: (data, type, row) => {
                                return `
                                    @if (hasPermission('admin.users.update'))
                                        <a href="{{ route("admin.users.update") }}/${row.id}"><i class="fa fa-edit fa-fw"></i></a>
                                    @endif
                                    @if (hasPermission('admin.users.delete'))
                                        <a href="{{ route("admin.users.delete") }}/${row.id}" class="delete-entry" data-tbl="data"><i class="fa fa-trash fa-fw"></i></a>
                                    @endif
                                    @if (hasPermission('admin.users.password_reset'))
                                        <a href="{{ route("admin.users.password_reset") }}/${row.id}" class="danger"><i class="fa fa-key fa-fw"></i></a>
                                    @endif
                                    <a href="{{ route("admin.users.view") }}/${row.id}"><i class="fa fa-eye fa-fw"></i></a>
                                `;
                            }, 
                            orderable: false,
                            searchable: false
                        },
                        { data: "name" },
                        { data: "email" },
                        { data: "mobile", mRender: (data, type, row) => `+${row.dial_code} ${row.mobile}`, class:'dir-ltr' },
                        { data: "status_text", name: "status" },
                        { data: "created_at", mRender: data => formatDate(data) },
                    ]
                });
            @endif

            @if (hasPermission('admin.vendors.index'))
                setTimeout(() => {
                    getGraphData('{{ route("admin.dashboard.vendors.graph") }}', vendorsDateRange, vendorsChart, '{{ transLang("vendors") }}');
                    reloadTable('vendors-table');
                }, 800);
                
                // Vendors Chart
                const vendorsChart = initGraph($('#vendorsChart'));

                initDateRange($('.vendors_date_range'), vendorsDateRange, (start, end) => {
                    vendorsDateRange.start = start;
                    vendorsDateRange.end = end;

                    getGraphData('{{ route("admin.dashboard.vendors.graph") }}', vendorsDateRange, vendorsChart, '{{ transLang("vendors") }}');
                    reloadTable('vendors-table');
                });

                $('#vendors-table').DataTable({
                    processing: true,
                    serverSide: true,
                    deferLoading: false,
                    scrollY: '300px',
                    ajax: {
                        type: 'GET',
                        url: '{{ route("admin.vendors.list") }}',
                        data: function(params) {
                            params.from_date = vendorsDateRange.start.lang('en').format('YYYY-MM-DD');
                            params.to_date = vendorsDateRange.end.lang('en').format('YYYY-MM-DD');
                        }
                    },
                    columns : [
                        {
                            mRender: (data, type, row) => {
                                return `
                                    @if (hasPermission('admin.vendors.view'))
                                        <a href="{{ route("admin.vendors.view") }}/${row.id}"><i class="fa fa-eye fa-fw"></i></a>
                                    @endif
                                    @if (hasPermission('admin.vendors.update'))
                                        <a href="{{ route("admin.vendors.update") }}/${row.id}"><i class="fa fa-edit fa-fw"></i></a>
                                    @endif
                                    @if (hasPermission('admin.vendors.reset_password'))
                                        <a href="{{ route("admin.vendors.reset_password") }}/${row.id}"><i class="fa fa-key fa-fw"></i></a>
                                    @endif
                                    @if (hasPermission('admin.vendors.delete'))
                                        <a href="{{ route("admin.vendors.delete") }}/${row.id}" class="delete-entry" data-tbl="data"><i class="fa fa-trash fa-fw"></i></a>
                                    @endif
                                `;
                            }, 
                            orderable: false,
                            searchable: false
                        },
                        { data: "name" },
                        { data: "email"  },
                        { data: "mobile", mRender: (data, type, row) => `+${row.dial_code} ${row.mobile}`, class:'dir-ltr' },
                        { data: "status_text", name: "status" },
                        { data: "created_at", mRender: data => formatDate(data) },
                    ]
                });
            @endif
            
            function getStats() {
                $.ajax({
                    dataType: 'json',
                    type: 'GET',
                    url: '{{ route("admin.dashboard.stats") }}',
                    error: () => $('.total_users, .total_vendors, .total_product_bookings, .total_service_bookings, .total_user_inquiries, .total_vendor_inquiries, .total_open_disputes').text('0'),
                    success: response => $.each(response, (key, val) => $(`.total_${key}`).text(val))
                });
            }

            function initGraph($target) {
                return new Chart($target.get(0).getContext('2d'), {
                    type: 'line',
                    data: { labels: [], datasets: [] },
                    options: {
                        responsive: true,
                        tooltips: {
                            intersect: false,
                            position: 'nearest',
                            mode: 'index'
                        },
                    }
                });
            }

            function initDateRange($target, dateRange, cb) {
                $target.daterangepicker({
                    opens: '{{ getSessionLang() == "ar" ? 'right' : 'left'}}',
                    dateLimit: { days: 30 },
                    startDate: dateRange.start,
                    endDate: dateRange.end,
                    locale: datePickerLocale,
                    ranges: datePickerRange,
                }, cb);
            }

            function getGraphData(url, dateRange, $chart, graphFor) {
                $.ajax({
                    dataType: 'json',
                    type: 'GET',
                    url,
                    data: {
                        start_date: dateRange.start.lang('en').format('YYYY-MM-DD'),
                        end_date: dateRange.end.lang('en').format('YYYY-MM-DD'),
                    },
                    success: function (response) {
                        $chart.data = {
                            labels: response.labels,
                            datasets: [{
                                label: graphFor,
                                data: response.stats,
                                borderColor: "rgb(26, 34, 38)",
                                backgroundColor: 'rgb(26, 34, 38, .4)',
                                borderWidth: 1,
                            }]
                        };
                        $chart.update();
                    }
                });
            }
        });
    </script>
@endsection