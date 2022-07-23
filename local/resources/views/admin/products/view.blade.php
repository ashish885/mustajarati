@extends('admin.layouts.master')

@section('title') {{ transLang('detail') }} @endsection

@section('content')
    <section class="content-header">
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i>{{ transLang('dashboard') }}</a>
            </li>
            <li><a href="{{ route('admin.products.index') }}"> {{ transLang('all_products') }} </a></li>
            <li class="active">{{ transLang('detail') }}</li>
        </ol>
    </section>

    <section class="content">
        @hasPermission('admin.products.update')
        <p><a class="btn btn-success"
                href="{{ route('admin.products.update', $product->id) }}">{{ transLang('update') }}</a></p>
        @endhasPermission

        <div class="row">
            <div class="col-md-12">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab_basic" data-toggle="tab">{{ transLang('basic_info') }}</a></li>
                        @hasPermission('admin.products.index')
                            <li><a href="#images" data-tbl="images" class="reload-tbl" data-toggle="tab">{{ transLang('images') }}</a></li>
                        @endhasPermission
                        @hasPermission('admin.product_bookings.index')
                            <li><a href="#bookings" class="reload-tbl" data-tbl="bookings" data-toggle="tab">{{ transLang('bookings') }}</a></li>
                        @endhasPermission
                        @hasPermission('admin.products.index')
                            <li><a href="#product_reviews" class="reload-tbl" data-tbl="product-reviews" data-toggle="tab">{{ transLang('reviews') }}</a></li>
                        @endhasPermission
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab_basic">
                            <div class="row">
                                <div class="col-sm-6">
                                    <table class="table table-striped table-bordered detail-view">
                                        <tbody>
                                            <tr>
                                                <th width="30%">{{ transLang('vendor') }}</th>
                                                <td>
                                                    @if (hasPermission('admin.vendors.index'))
                                                        <a href="{{ route('admin.vendors.view', $product->vendor_id) }}"
                                                            target="_blank">{{ $product->vendor_name }}</a>
                                                    @else
                                                        {{ $product->vendor_name }}
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>{{ transLang('product_name') }}</th>
                                                <td>{{ $product->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ transLang('category') }}</th>
                                                <td>{{ $product->category }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ transLang('sub_category') }}</th>
                                                <td>{{ $product->sub_category }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ transLang('ar_description') }}</th>
                                                <td>{{ $product->description }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ transLang('en_description') }}</th>
                                                <td>{{ $product->en_description }}</td>
                                            </tr>
                                            @if ($product->is_new==1)
                                                <tr>
                                                    <th>{{ transLang('new_product_price') }}</th>
                                                    <td>{{ $product->new_product_price }}</td>
                                                </tr>
                                            @else
                                                <tr>
                                                    <th>{{ transLang('manufacturing_date') }}</th>
                                                    <td>{{ date('d M, Y', strtotime($product->manufacturing_date)) }}
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-sm-6">
                                    <table class="table table-striped table-bordered detail-view">
                                        <tbody>
                                            <tr>
                                                <th width="25%">{{ transLang('amount') }}</th>
                                                <td>{{ number_format($product->amount) }} {{ transLang('sar') }} /
                                                    {{ transLang('amount_type_arr')[$product->amount_type] }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ transLang('delay_charges') }}</th>
                                                <td>{{ number_format($product->delay_charges) }}
                                                    {{ transLang('sar') }} /
                                                    {{ transLang('delay_charges_arr')[$product->delay_charges_type] }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>{{ transLang('security_amount') }}</th>
                                                <td>{{ number_format($product->security_amount) }}
                                                    {{ transLang('sar') }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ transLang('is_delivery_available') }}</th>
                                                <td>{{ $product->is_delivery_available }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ transLang('delivery_charges') }}</th>
                                                <td>{{ $product->delivery_charges }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ transLang('location') }}</th>
                                                <td>{{ $product->location }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="images">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert message_box hide"></div>
                                    @hasPermission('admin.products.images.create')
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <form id="save-frm" class="form-inline">
                                                    @csrf
                                                    <div class="form-group image-wrapper">
                                                        <label class="col-sm-3 control-label required">{{ transLang('image') }}</label>
                                                        <div class="col-sm-6">
                                                            <img id="image-cropper-preview" style="display:none; float:left;" width="60">
                                                            <input type="hidden" name="image">
                                                            <input type="file" class="image-cropper" data-width="{{ $img_width }}" data-height="{{ $img_height }}" data-name="image">
                                                            <small class="grey">{{ transLang('image_dimension') }}: <span class='dir-ltr'>{{ $img_width }} x {{ $img_height }}</span></small>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <button type="button" class="btn btn-success" id="addImageBtn"><i class="fa fa-plus"></i> {{ transLang('add_new') }}
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <br>
                                    @endhasPermission
                                    <ul class="todo-list product-images-section">
                                        @forelse ($product_image as $item)
                                            <li data-id="{{ $item->id }}">
                                                <span class="handle">
                                                    <i class="fa fa-ellipsis-v"></i>
                                                    <i class="fa fa-ellipsis-v"></i>
                                                </span>
                                                @if ($item->image)
                                                    <span class="text">
                                                        <img src="{{ imageBasePath($item->image) }}" width="40" />
                                                    </span>
                                                @endif
                                                <div class="pull-right">
                                                    <div class="tools">
                                                        @hasPermission("admin.products.images.delete")
                                                            <a title="{{ transLang('delete') }}" href="{{ route('admin.products.images.delete', ['product_id' => $product->id, 'id' => $item->id]) }}" class="delete-entry" data-reload_page="1"><i class="fa fa-trash fa-fw"></i></a>
                                                        @endhasPermission
                                                    </div>
                                                </div>
                                            </li>
                                        @empty
                                            <li data-id="0">
                                                <span class="text">{{ transLang('no_record_found') }}</span>
                                            </li>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="bookings">
                            <div class="row">
                                <div class="col-xs-12">
                                    <table class="table table-striped table-bordered table-hover dataTable" id="bookings-table">
                                        <thead>
                                            <tr>
                                                <th>{{ transLang('booking_code') }}</th>
                                                <th>{{ transLang('user') }}</th>
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
                        <div class="tab-pane" id="product_reviews">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-striped table-bordered table-hover dataTable" id="product-reviews-table">
                                        <thead>
                                            <tr>
                                                <th>{{ transLang('booking_code') }}</th>
                                                <th>{{ transLang('user') }}</th>
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
    </section>
@endsection

@section('scripts')
    <script type="text/javascript">
        $(function() {
            $(document).on('click', '.reload-tbl', function(e) {
                let { tbl } = $(this).data();
                reloadTable(`${tbl}-table`);
                $(this).removeClass('reload-tbl');
            });

            $('.product-images-section').sortable({
                placeholder: 'sort-highlight',
                handle: '.handle',
                forcePlaceholderSize: true,
                zIndex: 999999,
                stop: function(event, ui) {
                    let order = $(".product-images-section").sortable('toArray', { attribute: 'data-id' });
                    $.post(`{{ route('admin.products.images.order.update') }}`, {order, '_token': '{{ csrf_token() }}'});
                }
            });

            $('#bookings-table').DataTable({
                processing: true,
                serverSide: true,
                deferLoading: false,
                ajax: '{{ route("admin.product_bookings.list", ["product_id" => $product->id]) }}',
                columns : [
                    { data: "booking_code", name: "product_bookings.booking_code" },
                    @if (hasPermission('admin.users.view'))
                        {
                            data: "user",
                            name: "users.name",
                            mRender: (data, type, row) => row.user_id ? `<a href="{{ route('admin.users.view') }}/${row.user_id}" target="_blank">${data}</a>` : '',
                        },
                    @else
                        { data: "user", name: "users.name" },
                    @endif
                    @if (hasPermission('admin.vendors.view'))
                        {
                            data: "vendor",
                            name: "vendors.name",
                            mRender: (data, type, row) => row.vendor_id ? `<a href="{{ route('admin.vendors.view') }}/${row.vendor_id}" target="_blank">${data}</a>` : '',
                        },
                    @else
                        { data: "vendor", name: "vendors.name" },
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
                                    <a href="{{ route('admin.product_bookings.delete') }}/${row.id}" class="delete-entry" data-tbl="bookings"><i
                                            class="fa fa-trash fa-fw"></i></a>
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
                ajax: '{{ route('admin.products.reviews.list', ['id' => $product->id]) }}',
                columns: [
                    @if (hasPermission('admin.product_bookings.view'))
                        {
                        data: "booking_code",
                        name: "product_bookings.booking_code",
                        mRender: (data, type, row) => row.product_booking_id ? `<a
                            href="{{ route('admin.product_bookings.view') }}/${row.product_booking_id}" target="_blank">${data}</a>` :
                        ``,
                        },
                    @else
                        { data: "booking_code", name: "product_bookings.booking_code" },
                    @endif
                    @if (hasPermission('admin.users.view'))
                        {
                            data: "user",
                            name: "users.name",
                            mRender: (data, type, row) => row.user_id ? `<a href="{{ route('admin.users.view') }}/${row.user_id}" target="_blank">${data}</a>` : '',
                        },
                    @else
                        { data: "user", name: "users.name" },
                    @endif
                    {
                        data: "comments",
                        name: "product_reviews.comments",
                        mRender: data => data ? data.trimToLength(50) : ''
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

            $(document).on('click', '#addImageBtn', function(e) {
                e.preventDefault();
                let btn = $(this);
                let loader = $('.message_box');

                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: "{{ route('admin.products.images.create', $product->id) }}",
                    data: new FormData($('#save-frm')[0]),
                    processData: false,
                    contentType: false,
                    beforeSend: () => {
                        btn.attr('disabled', true);
                        loader.html(`{!! transLang('loader_message') !!}`).removeClass('hide alert-danger alert-success').addClass('alert-info');
                    },
                    error: (jqXHR, exception) => {
                        btn.attr('disabled', false);
                        loader.html(formatErrorMessage(jqXHR, exception)).removeClass('alert-info').addClass('alert-danger');
                    },
                    success: response => {
                        btn.attr('disabled', false);
                        loader.html(response.message).removeClass('alert-info').addClass('alert-success');
                        location.reload(true);
                    }
                });
            });
        });
    </script>
@endsection
