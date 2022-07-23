@extends('admin.layouts.master')

@section('title') {{ transLang('detail') }} @endsection

@section('content')
    <section class="content-header">
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i>
                    {{ transLang('dashboard') }}</a></li>
            <li><a href="{{ route('admin.vendors.index') }}"> {{ transLang('all_vendors') }} </a></li>
            <li class="active">{{ transLang('detail') }}</li>
        </ol>
    </section>

    <section class="content"><br>
        <div class="row">
            <div class="col-md-4 col-sm-4 col-xs-12">
                <div class="info-box" style="min-height:60px">
                    <span class="info-box-icon bg-green" style="height: 60px;line-height: 60px;font-size: 31px;">SAR</span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Amount</span>
                        <span class="info-box-number total_amount">{{ $vendor->total_amount }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-12">
                <div class="info-box" style="min-height:60px">
                    <span class="info-box-icon bg-blue" style="height: 60px;line-height: 60px;font-size: 31px;">SAR</span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Paid Amount</span>
                        <span class="info-box-number total_paid_amount">{{ $vendor->total_paid_amount }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-12">
                <div class="info-box" style="min-height:60px">
                    <span class="info-box-icon bg-red" style="height: 60px;line-height: 60px;font-size: 31px;">SAR</span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Pending Amount</span>
                        <span class="info-box-number total_pending_amount">{{ $vendor->total_pending_amount }}</span>
                    </div>
                </div>
            </div>
        </div>
        @hasPermission('admin.vendors.update')
            <p><a class="btn btn-success" href="{{ route('admin.vendors.update', ['id' => $vendor->id]) }}">{{ transLang('update') }}</a></p>
        @endhasPermission
        <div class="row">
            <div class="col-md-12">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab_basic" data-toggle="tab">{{ transLang('basic_info') }}</a></li>
                        @hasPermission('admin.vendors.index')
                        <li><a href="#vendor" data-tbl="payment" class="reload-tbl" data-toggle="tab">{{ transLang('vendor_payment_history') }}</a></li>
                        @endhasPermission
                        @hasPermission('admin.service_bookings.index')
                        <li><a href="#service_bookings" class="reload-tbl" data-tbl="service-bookings" data-toggle="tab">{{ transLang('service_bookings') }}</a></li>
                        @endhasPermission
                        @hasPermission('admin.product_bookings.index')
                        <li><a href="#product_bookings" class="reload-tbl" data-tbl="product-bookings" data-toggle="tab">{{ transLang('product_bookings') }}</a></li>
                        @endhasPermission
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab_basic">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-striped table-bordered detail-view">
                                        <tbody>
                                            <tr>
                                                <th width="30%">{{ transLang('vendor') }}</th>
                                                <td>{{ $vendor->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ transLang('email') }}</th>
                                                <td>{{ $vendor->email }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ transLang('mobile') }}</th>
                                                <td class="dir-ltr">{{ $vendor->mobile ? "+{$vendor->dial_code} {$vendor->mobile}" : '' }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ transLang('verification_status') }}</th>
                                                <td>{{ transLang('verification_status_arr')[$vendor->verification_status] }}</td>
                                            </tr>
                                            @if ($vendor->verification_status != 1)
                                                <tr>
                                                    <th>{{ transLang('is_profile_editing_allowed') }}</th>
                                                    <td>{{ transLang('other_action')[$vendor->is_profile_editing_allowed] }}</td>
                                                </tr>
                                            @endif
                                            <tr>
                                                <th>{{ transLang('status') }}</th>
                                                <td>{{ transLang('action_status')[$vendor->status] }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-striped table-bordered detail-view">
                                        <tbody>
                                            <tr>
                                                <th width="30%">{{ transLang('national_id') }}</th>
                                                <td>{{ $vendor->national_id }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ transLang('national_id_image') }}</th>
                                                <td><img src="{{ imageBasePath($vendor->national_id_front_image) }}" width="40px"/></td>
                                            </tr>
                                            <tr>
                                                <th>{{ transLang('profile_image') }}</th>
                                                <td><img src="{{ imageBasePath($vendor->profile_image) }}" width="40px"/></td>
                                            </tr>
                                            <tr>
                                                <th>{{ transLang('is_withdrawal_requested') }}</th>
                                                <td>{{ transLang('other_action')[$vendor->is_withdrawal_requested] }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ transLang('withdrawal_request_date') }}</th>
                                                <td><span class="date-class" data-date="{{ $vendor->withdrawal_request_date }}">{{ $vendor->withdrawal_request_date }}</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <h4>{{ transLang('bank_details') }}</h4>
                            <div class="row">
                                <div class="col-xs-12">
                                    <table class="table table-striped table-bordered detail-view">
                                        <tbody>
                                            <tr>
                                                <th width="25%">{{ transLang('bank_name') }}</th>
                                                <td>{{ $vendor->bank_details->bank_name }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ transLang('bank_code') }}</th>
                                                <td>{{ $vendor->bank_details->iban_no }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ transLang('account_holder_name') }}</th>
                                                <td>{{ $vendor->bank_details->account_holder_name }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ transLang('account_no') }}</th>
                                                <td>{{ $vendor->bank_details->account_no }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane" id="vendor">
                            @hasPermission('admin.vendors.payment.create')
                                <p class="pull-right">
                                    <a href="{{ route("admin.vendors.payment.create", ['id' => $vendor->id]) }}" class="btn btn-success" data-toggle="modal" data-target="#remote_model">{{ transLang('add_new') }}</a>
                                </p><br>
                            @endhasPermission
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="box-body grid-view">
                                        <table class="table table-striped table-bordered table-hover dataTable" id="payment-table">
                                            <thead>
                                                <tr>
                                                    <th>{{ transLang('amount') }}</th>
                                                    <th>{{ transLang('transaction_id') }}</th>
                                                    <th>{{ transLang('comments') }}</th>
                                                    <th>{{ transLang('payment_date') }}</th>
                                                    <th>{{ transLang('attachment') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="service_bookings">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-striped table-bordered table-hover dataTable" id="service-bookings-table">
                                        <thead>
                                            <tr>
                                                <th>{{ transLang('booking_code') }}</th>
                                                <th>{{ transLang('user') }}</th>
                                                <th>{{ transLang('service') }}</th>
                                                <th>{{ transLang('total_amount') }}</th>
                                                <th>{{ transLang('payment_status') }}</th>
                                                <th>{{ transLang('created_at') }}</th>
                                                <th>{{ transLang('status') }}</th>
                                                <th>{{ transLang('action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="product_bookings">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-striped table-bordered table-hover dataTable" id="product-bookings-table">
                                        <thead>
                                            <tr>
                                                <th>{{ transLang('booking_code') }}</th>
                                                <th>{{ transLang('user') }}</th>
                                                <th>{{ transLang('total_amount') }}</th>
                                                <th>{{ transLang('payment_status') }}</th>
                                                <th>{{ transLang('status') }}</th>
                                                <th>{{ transLang('submitted_at') }}</th>
                                                <th>{{ transLang('action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </section>
@endsection
@section('scripts')
    <script type="text/javascript">
       function getPaymentStats() {
            $.get(`{{ route('admin.vendors.payment.stats', $vendor->id) }}`, response => {
                $('.total_amount').text(formatMoney(response.total_amount));
                $('.total_paid_amount').text(formatMoney(response.total_paid_amount));
                $('.total_pending_amount').text(formatMoney(response.total_pending_amount));
            }, 'json');
        }

        $(function() {
            $('.date-class').each((i, el) => {
                if ($(el).data('date') != '') {
                    $(el).text(formatDate($(el).data('date')));
                }
            });

            $(document).on('click', '.reload-tbl', function (e) {
                let { tbl } = $(this).data();
                reloadTable(`${tbl}-table`);
                $(this).removeClass('reload-tbl');
            });
            
            $('#payment-table').DataTable({
                processing: true,
                serverSide: true,
                deferLoading: false,
                ajax: '{{ route("admin.vendors.payment.list", $vendor->id)}}',
                aaSorting: [[0, 'desc']],
                columns : [
                    { data: "amount" },
                    { data: "transaction_id" },
                    { data: "comments" },
                    { data: "payment_date" },
                    {
                        data: "attachment",
                        mRender: data => data ? `<a href="{{ imageBasePath() }}/${data}" download target="_blank"><i class="fa fa-download"></i></a>` : ''
                    },
                ]
            });

            $('#service-bookings-table').DataTable({
                processing: true,
                serverSide: true,
                deferLoading: false,
                ajax: '{{ route("admin.service_bookings.list", ["vendor_id" => $vendor->id]) }}',
                columns : [
                    { data: "booking_code", name: "service_bookings.booking_code" },
                    @if (hasPermission('admin.users.view'))
                        {
                            data: "user_name",
                            name: "users.name",
                            mRender: (data, type, row) => row.user_id ? `<a href="{{ route('admin.users.view') }}/${row.user_id}" target="_blank">${data}</a>` : ``,
                        },
                    @else
                        { data: "user_name", name: "users.name"},
                    @endif
                    { data: "service_name", name: "services.{{$ql}}name" },
                    { data: "total_amount", name: "service_bookings.total_amount", mRender: data => `${formatMoney(data)} {{ transLang('sar') }}` },
                    { data: "payment_status_text", name: "service_bookings.payment_status" },
                    { data: "created_at", mRender: data => formatDate(data) },
                    { data: "status_text", name: "service_bookings.status" },
                    {
                        mRender: (data, type, row) => {
                            return `
                                @if (hasPermission('admin.service_bookings.view'))
                                    <a href="{{ route("admin.service_bookings.view") }}/${row.id}" ><i class="fa fa-eye fa-fw"></i></a>
                                @endif
                                
                                @if (hasPermission('admin.service_bookings.delete'))
                                    <a href="{{ route("admin.service_bookings.delete") }}/${row.id}" class="delete-entry"   data-tbl="service-bookings" ><i class="fa fa-trash fa-fw"></i></a>
                                @endif
                            `;
                        }, 
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $('#product-bookings-table').DataTable({
                processing: true,
                serverSide: true,
                deferLoading: false,
                ajax: '{{ route("admin.product_bookings.list", ["vendor_id" => $vendor->id]) }}',
                columns : [
                    { data: "booking_code", name: "product_bookings.booking_code" },
                    @if (hasPermission('admin.users.view'))
                        {
                            data: "user",
                            name: "users.name",
                            mRender: (data, type, row) => row.user_id ? `<a href="{{ route('admin.users.view') }}/${row.user_id}" target="_blank">${data}</a>` : ``,
                        },
                    @else
                        { data: "user", name: "users.name"},
                    @endif
                    { data: "total_amount", name: "product_bookings.total_amount", mRender: data => `${formatMoney(data)} {{ transLang('sar') }}` },
                    { data: "payment_status_text", name: "product_bookings.payment_status" },
                    { data: "status_text", name: "product_bookings.status" },
                    { data: "created_at", mRender: data => formatDate(data) },
                    {
                        mRender: (data, type, row) => {
                            return `
                                @if (hasPermission('admin.product_bookings.view'))
                                    <a href="{{ route("admin.product_bookings.view") }}/${row.id}" ><i class="fa fa-eye fa-fw"></i></a>
                                @endif
                                
                                @if (hasPermission('admin.product_bookings.delete'))
                                    <a href="{{ route('admin.product_bookings.delete') }}/${row.id}" class="delete-entry" data-tbl="product-bookings"><i class="fa fa-trash fa-fw"></i></a>
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