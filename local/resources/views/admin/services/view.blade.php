@extends('admin.layouts.master')

@section('title') {{ transLang('detail') }} @endsection

@section('content')
    <section class="content-header">
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i> {{ transLang('dashboard') }}</a></li>
            <li><a href="{{ route('admin.services.index') }}"> {{ transLang('all_services') }} </a></li>
            <li class="active">{{ transLang('detail') }}</li>
        </ol>
    </section>

    <section class="content">
        @hasPermission('admin.services.update')
            <p><a class="btn btn-success" href="{{ route('admin.services.update', ['id' => $service->id]) }}">{{ transLang('update') }}</a></p>
        @endhasPermission
        <div class="row">
            <div class="col-md-12">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab_basic" data-toggle="tab">{{ transLang('basic_info') }}</a></li>
                        @hasPermission('admin.services.index')
                            <li><a href="#images" data-tbl="images" class="reload-tbl" data-toggle="tab">{{ transLang('images') }}</a></li>
                        @endhasPermission
                        @hasPermission('admin.service_bookings.index')
                            <li><a href="#bookings" data-tbl="bookings" class="reload-tbl" data-toggle="tab">{{ transLang('bookings') }}</a></li>
                        @endhasPermission
                        @hasPermission('admin.services.index')
                            <li><a href="#reviews" data-tbl="reviews" class="reload-tbl" data-toggle="tab">{{ transLang('reviews') }}</a></li>
                        @endhasPermission
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab_basic">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-striped table-bordered detail-view">
                                        <tbody>
                                            <tr>
                                                <th width="25%">{{ transLang('vendor') }}</th>
                                                <td>{{ $service->vendor }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ transLang('category') }}</th>
                                                <td>{{ $service->category }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ transLang('sub_category') }}</th>
                                                <td>{{ $service->sub_category }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ transLang('ar_name') }}</th>
                                                <td>{{ $service->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ transLang('en_name') }}</th>
                                                <td>{{ $service->en_name }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ transLang('ar_description') }}</th>
                                                <td>{{ $service->description }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ transLang('en_description') }}</th>
                                                <td>{{ $service->en_description }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ transLang('amount') }}</th>
                                                <td>{{ $service->amount }} / {{ transLang('amount_type_arr')[$service->amount_type] }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ transLang('provide_service_customer_location') }}</th>
                                                <td>{{ translang('other_action')[$service->type] }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ transLang('address') }}</th>
                                                <td>{{ $service->address }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ transLang('status') }}</th>
                                                <td>{{ transLang('action_status')[$service->status] }}</td>
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
                                    @hasPermission('admin.services.images.create')
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
                                    <ul class="todo-list service-images-section">
                                        @forelse ($service_image as $item)
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
                                                        @hasPermission("admin.services.images.delete")
                                                            <a title="{{ transLang('delete') }}" href="{{ route('admin.services.images.delete', ['service_id' => $service->id, 'id' => $item->id]) }}" class="delete-entry" data-reload_page="1"><i class="fa fa-trash fa-fw"></i></a>
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
                        <div class="tab-pane" id="reviews">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-striped table-bordered table-hover dataTable" id="reviews-table">
                                        <thead>
                                            <tr>
                                                <th>{{ transLang('booking_code') }}</th>
                                                <th>{{ transLang('user') }}</th>
                                                <th>{{ transLang('rating') }}</th>
                                                <th>{{ transLang('comments') }}</th>
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
            $(document).on('click', '.reload-tbl', function (e) {
                let { tbl } = $(this).data();
                reloadTable(`${tbl}-table`);
                $(this).removeClass('reload-tbl');
            });

            $('.service-images-section').sortable({
                placeholder: 'sort-highlight',
                handle: '.handle',
                forcePlaceholderSize: true,
                zIndex: 999999,
                stop: function(event, ui) {
                    let order = $(".service-images-section").sortable('toArray', { attribute: 'data-id' });
                    $.post(`{{ route('admin.services.images.order.update') }}`, {order, '_token': '{{ csrf_token() }}'}, _ => location.reload(true), 'json');
                }
            });

            $('#bookings-table').DataTable({
                processing: true,
                serverSide: true,
                deferLoading: false,
                ajax: '{{ route("admin.service_bookings.list", ["service_id" => $service->id]) }}',
                columns: [{
                        data: "booking_code",
                        name: "service_bookings.booking_code"
                    },
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

            $('#reviews-table').DataTable({
                processing: true,
                serverSide: true,
                deferLoading: false,
                ajax: '{{ route("admin.services.reviews.list", ["id" => $service->id])}}',
                aaSorting: [[0, 'desc']],
                columns : [
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
                    @if(hasPermission('admin.users.view'))
                    {
                        data:'user',
                        name:'users.name',
                        mRender:(data,type,row)=>row.user_id ? `<a href="{{route('admin.users.view')}}/${row.user_id}" target="_blank">${data}</a>` :``},
                    @else 
                        { data: "user", name: "users.name" },
                    @endif
                    { data: "rating" },
                    { data: "comments" },
                    {
                        mRender: (data, type, row) => {
                            return `
                                @if (hasPermission('admin.services.reviews.delete'))
                                    <a href="{{ route("admin.services.reviews.delete") }}/${row.id}" class="delete-entry" data-tbl="reviews"><i class="fa fa-trash fa-fw"></i></a>
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
                    url: "{{ route('admin.services.images.create', $service->id) }}",
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
                        //$('#save-frm').trigger("reset");
                    }
                });
            });
        });
    </script>
@endsection