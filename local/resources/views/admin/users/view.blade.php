@extends('admin.layouts.master')

@section('title') {{ transLang('detail') }} @endsection

@section('content')
    <section class="content-header">
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i>
                    {{ transLang('dashboard') }}</a></li>
            <li><a href="{{ route('admin.users.index') }}">{{ transLang('all_users') }}</a></li>
            <li class="active">{{ transLang('detail') }}</li>
        </ol>
    </section>

    <section class="content">
        <p>
            <a class="btn btn-success btn-floating"
                href="{{ route('admin.users.update', ['?id' => $user->id]) }}">{{ transLang('update') }}</a>
        </p>
        <div class="row">
            <div class="col-md-12">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#basic-info-tab">{{ transLang('basic_info') }}</a>
                        </li>
                        @hasPermission('admin.service_bookings.index')
                            <li><a href="#service_bookings" class="reload-tbls" data-tbl="service-bookings"  data-toggle="tab">{{ transLang('service_bookings') }}</a></li>
                        @endhasPermission
                        @hasPermission('admin.product_bookings.index')
                            <li><a href="#product_bookings" class="reload-tbls" data-tbl="product-bookings"  data-toggle="tab">{{ transLang('product_bookings') }}</a></li>
                        @endhasPermission
                        <li><a href="#service_reviews" class="reload-tbls" data-tbl="service-reviews"  data-toggle="tab">{{ transLang('service_reviews') }}</a></li>
                        <li><a href="#product_reviews" class="reload-tbls" data-tbl="product-reviews"  data-toggle="tab">{{ transLang('product_reviews') }}</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="basic-info-tab">
                            <table class="table table-striped table-bordered no-margin">
                                <tbody>
                                    <tr>
                                        <th width="20%">{{ transLang('name') }}</th>
                                        <td>{{ $user->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ transLang('mobile') }}</th>
                                        <td class="dir-ltr">{{ "+{$user->dial_code} {$user->mobile}" }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ transLang('email') }}</th>
                                        <td>{{ $user->email }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ transLang('status') }}</th>
                                        <td>{{ transLang('action_status')[$user->status] }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ transLang('profile_image') }}</th>
                                        <td>
                                            @if (!empty($user->profile_image))
                                                <img src="{{ imageBasePath($user->profile_image) }}" width="60" />
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane" id="service_bookings">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-striped table-bordered table-hover dataTable"
                                        id="service-bookings-table">
                                        <thead>
                                            <tr>
                                                <th>{{ transLang('booking_code') }}</th>
                                                <th>{{ transLang('vendor') }}</th>
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
                                    <table class="table table-striped table-bordered table-hover dataTable"
                                        id="product-bookings-table">
                                        <thead>
                                            <tr>
                                                <th>{{ transLang('booking_code') }}</th>
                                                <th>{{ transLang('vendor') }}</th>
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
                        <div class="tab-pane" id="service_reviews">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-striped table-bordered table-hover dataTable"
                                        id="service-reviews-table">
                                        <thead>
                                            <tr>
                                                <th>{{ transLang('booking_code') }}</th>
                                                <th>{{ transLang('service') }}</th>
                                                <th>{{ transLang('comments') }}</th>
                                                <th>{{ transLang('rating') }}</th>
                                                <th>{{ transLang('action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="product_reviews">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-striped table-bordered table-hover dataTable"
                                        id="product-reviews-table">
                                        <thead>
                                            <tr>
                                                <th>{{ transLang('booking_code') }}</th>
                                                <th>{{ transLang('product') }}</th>
                                                <th>{{ transLang('comments') }}</th>
                                                <th>{{ transLang('rating') }}</th>
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
        </div>
    </section>
@endsection
@section('scripts')
    <script type="text/javascript">
        $(function() {
            $(document).on('click', '.reload-tbls', function(e) {
                $(this).removeClass('reload-tbls');
                let tbl = $(this).data('tbl');
                reloadTable(`${tbl}-table`);
            });

            $('#service-bookings-table').DataTable({
                processing: true,
                serverSide: true,
                deferLoading: false,
                ajax: '{{ route("admin.service_bookings.list", ['user_id' => $user->id]) }}',
                columns: [
                    {
                        data: "booking_code",
                        name: "service_bookings.booking_code"
                    },
                    @if (hasPermission('admin.vendors.view'))
                        {
                            data: "vendor_name",
                            name: "vendors.name",
                            mRender: (data, type, row) => row.vendor_id ? `<a href="{{ route('admin.vendors.view') }}/${row.vendor_id}" target="_blank">${data}</a>` : ``,
                        },
                    @else
                        { data: "vendor_name", name: "vendors.name", },
                    @endif
                    {
                        data: "service_name",
                        name: "services.{{ $ql }}name"
                    },
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
                                    <a href="{{ route('admin.service_bookings.delete') }}/${row.id}" class="delete-entry" data-tbl="service-bookings"><i class="fa fa-trash fa-fw"></i></a>
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
                ajax: '{{ route("admin.product_bookings.list", ['user_id' => $user->id]) }}',
                columns: [
                    {
                        data: "booking_code",
                        name: "product_bookings.booking_code"
                    },
                    @if (hasPermission('admin.vendors.view'))
                        {
                            data: "vendor",
                            name: "vendors.name",
                            mRender: (data, type, row) => row.vendor_id ? `<a href="{{ route('admin.vendors.view') }}/${row.vendor_id}" target="_blank">${data}</a>` : '',
                        },
                    @else
                        { data: "vendor", name: "vendors.name" },
                    @endif
                    {
                        data: "total_amount",
                        name: "product_bookings.total_amount",
                        mRender: data => `${formatMoney(data)} {{ transLang('sar') }}`
                    },
                    {
                        data: "payment_status_text",
                        name: "product_bookings.payment_status"
                    },
                    {
                        data: "status_text",
                        name: "product_bookings.status"
                    },
                    {
                        data: "created_at",
                        mRender: data => formatDate(data)
                    },
                    {
                        mRender: (data, type, row) => {
                            return `
                                @if (hasPermission('admin.product_bookings.view'))
                                    <a href="{{ route('admin.product_bookings.view') }}/${row.id}"><i class="fa fa-eye fa-fw"></i></a>
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

            $('#service-reviews-table').DataTable({
                processing: true,
                serverSide: true,
                deferLoading: false,
                ajax: '{{ route("admin.services.reviews.list", ["user_id" => $user->id]) }}',
                columns: [
                    @if (hasPermission('admin.service_bookings.view'))
                        {
                        data: "booking_code",
                        name: "service_bookings.booking_code",
                        mRender: (data, type, row) => row.service_booking_id ? `<a href="{{ route('admin.service_bookings.view') }}/${row.service_booking_id}"
                            target="_blank">${data}</a>` : ``,
                        },
                    @else
                        { data: "booking_code", name: "service_bookings.booking_code" },
                    @endif

                    @if (hasPermission('admin.services.view'))
                        {
                            data: "service",
                            name: "services.{{ $ql }}name",
                            mRender: (data, type, row) => row.service_id ? `<a href="{{ route('admin.services.view') }}/${row.service_id}" target="_blank">${data}</a>` : ``,
                        },
                    @else
                        { data: "service", name: "services.{{ $ql }}name"},
                    @endif {
                        data: "comments",
                        name: "service_reviews.comments",
                        // mRender: data => data ? data.trimToLength(50) : ''
                    },
                    {
                        data: "rating",
                        name: "service_reviews.rating"
                    },
                    {
                        mRender: (data, type, row) => {
                            return `
                            @if (hasPermission('admin.services.reviews.delete'))
                                <a href="{{ route('admin.services.reviews.delete') }}/${row.id}" class="delete-entry" data-tbl="service-reviews"><i class="fa fa-trash fa-fw"></i></a>
                            @endif
                            `;
                        },
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $('#product-reviews-table').DataTable({
                processing: true,
                serverSide: true,
                deferLoading: false,
                ajax: '{{ route("admin.products.reviews.list", ["user_id" => $user->id]) }}',
                columns: [
                    @if (hasPermission('admin.product_bookings.view'))
                        {
                            data: "booking_code",
                            name: "product_bookings.booking_code",
                            mRender: (data, type, row) => row.product_booking_id ? `<a href="{{ route('admin.product_bookings.view') }}/${row.product_booking_id}"
                            target="_blank">${data}</a>` : ``,
                        },
                    @else
                        { data: "booking_code", name: "product_bookings.booking_code" },
                    @endif
                    @if (hasPermission('admin.products.view'))
                        {
                            data: "product",
                            name: "products.{{ $ql }}name",
                            mRender: (data, type, row) => row.product_id ? `<a href="{{ route('admin.products.view') }}/${row.product_id}" target="_blank">${data}</a>` : ``,
                        },
                    @else
                        { data: "product", name: "products.{{ $ql }}name"},
                    @endif
                    {
                        data: "comments",
                        name: "product_reviews.comments",
                        // mRender: data => data ? data.trimToLength(50) : ''
                    },
                    {
                        data: "rating",
                        name: "product_reviews.rating"
                    },
                    {
                        mRender: (data, type, row) => {
                            return `
                            @if (hasPermission('admin.products.reviews.delete'))
                                <a href="{{ route('admin.products.reviews.delete') }}/${row.id}" class="delete-entry" data-tbl="product-reviews"><i class="fa fa-trash fa-fw"></i></a>
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
