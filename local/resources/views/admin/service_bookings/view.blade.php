@extends('admin.layouts.master')
@section('title') {{ transLang('detail') }} @endsection
@section('content')
    <section class="content-header">
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i>
                    {{ transLang('dashboard') }}</a>
            </li>
            <li><a href="{{ route('admin.service_bookings.index') }}"> {{ transLang('all_service_booking') }} </a></li>
            <li class="active">{{ transLang('detail') }}</li>
        </ol>
    </section>
    <section class="content">
        <!-- 1.Pending, 2.Ongoing, 3.Completed, 4.Rejected, 5.Cancelled -->
        <p>
            @if ($booking->status < 3)
                <a class="btn btn-warning" href="{{ route('admin.service_bookings.cancel.index', $booking->id) }}" data-toggle="modal" data-target="#remote_model"><i class="fa fa-times"></i> {{ transLang('cancel_booking') }}</a>
            @endif

            <a class="btn btn-danger delete-booking" href="{{ route('admin.service_bookings.delete', $booking->id) }}"><i class="fa fa-trash"></i> {{ transLang('delete_booking') }}</a>

            @if ($booking->payment_init_id && !$booking->is_cancelled_amount_refunded && !$booking->is_refund_initiated && in_array($booking->status, [3, 4, 5]))
                <a id="refundCancelBtn" class="btn btn-warning"><i class="fa fa-undo"></i> {{ transLang('refund_money') }}</a>
            @endif
        </p>
        <div class="row">
            <div class="col-md-12">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab_basic" data-toggle="tab">{{ transLang('booking_info') }}</a></li>
                        @hasPermission('admin.service_bookings.index')
                        <li><a href="#service_booking_details" data-toggle="tab">{{ transLang('service') }}</a></li>
                        @endhasPermission
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab_basic">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-striped table-bordered detail-view">
                                        <tbody>
                                            <tr>
                                                <td width="33%"><b>{{ transLang('booking_code') }}</b>:  {{ $booking->booking_code }}</td>
                                                <td width="33%"><b>{{ transLang('from_date') }}</b>: <span class="date-class" data-date="{{ $booking->from_date }}">{{ $booking->from_date }}</span></td>
                                                <td width="33%"><b>{{ transLang('to_date') }}</b>: <span class="date-class" data-date="{{ $booking->to_date }}">{{ $booking->to_date }}</span></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <b>{{ transLang('user') }}</b> :  
                                                    @if (hasPermission('admin.users.view'))
                                                        <a href="{{ route('admin.users.view', $booking->user_id) }}">{{ $booking->user }}</a>
                                                    @else
                                                        {{ $booking->user }}
                                                    @endif
                                                </td>
                                                <td>
                                                    <b>{{ transLang('vendor') }}</b>:  
                                                    @if (hasPermission('admin.users.view'))
                                                        <a href="{{ route('admin.vendors.view', $booking->vendor_id) }}">{{ $booking->vendor }}</a>
                                                    @else
                                                        {{ $booking->user }}
                                                    @endif
                                                </td>
                                                <td>
                                                    <b>{{ transLang('total_duration') }}</b>: {{ $booking->total_hours }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><b>{{ transLang('payment_status') }}</b>: {{ transLang('payment_status_arr') [$booking->payment_status] }}</td>
                                                <td><b>{{ transLang('booking_status') }}</b>: {{ transLang('product_booking_status_arr') [$booking->status] }}</td>
                                                <td><b>{{ transLang('admin_amount') }}</b>: {{ number_format($booking->admin_amount, 2) }} {{ transLang('sar') }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="3"><b>{{ transLang('vendor_amount') }}</b>: {{ number_format($booking->vendor_amount, 2) }} {{ transLang('sar') }}</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    @if ($booking->status == 5)
                                        <h4 style="font-weight: 600;">{{ transLang('cancellation_details') }}</h4>
                                        <table class="table table-striped table-bordered detail-view">
                                            <tbody>
                                                <tr>
                                                    <td width="33%"><b>{{ transLang('cancellation_reason') }}</b>: {{ $booking->cancellation_reason }}</td>
                                                    <td width="33%"><b>{{ transLang('cancellation_charges') }}</b>: {{ number_format($booking->cancellation_charges, 2) }} {{ transLang('sar') }}</td>
                                                    <td width="33%"><b>{{ transLang('cancellation_refund_amount') }}</b>: {{ number_format($booking->cancellation_refund_amount, 2) }} {{ transLang('sar') }}</td>
                                                </tr>
                                                <tr>
                                                    <td><b>{{ transLang('is_cancelled_amount_refunded') }}</b>: {{ transLang('other_action')[$booking->is_cancelled_amount_refunded] }}</td>
                                                    <td colspan="2"><b>{{ transLang('cancellation_refund_at') }}</b>: <span class="date-class" data-date="{{ $booking->cancellation_refund_at }}">{{ $booking->cancellation_refund_at }}</span></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    @endif

                                    <h4 style="font-weight: 600;">{{ transLang('booking_summary') }}</h4>
                                    <table class="table table-striped table-bordered detail-view">
                                        <tbody>
                                            <tr>
                                                <th>{{ transLang('paid_amount') }}</th>
                                                <td> {{ number_format($booking->paid_amount, 2) }} {{ transLang('sar') }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ transLang('sub_total') }}</th>
                                                <td>{{ number_format($booking->subtotal, 2) }} {{ transLang('sar') }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ transLang('tax_amount') }} ({{ str_replace('.00', '', $booking->tax_percentage) }}%)</th>
                                                <td>{{ number_format($booking->tax_amount, 2) }} {{ transLang('sar') }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ transLang('total_amount') }}</th>
                                                <td>{{ number_format($booking->total_amount, 2) }} {{ transLang('sar') }}</td>
                                            </tr>
                                            @if ($booking->status == 3)
                                                <tr>
                                                    <th>{{ transLang('completed_by') }}</th>
                                                    <td> {{ @transLang('action_by_arr')[$booking->completed_by] }}</td>
                                                </tr>
                                            @endif
                                            <tr>
                                                <th>{{ transLang('payment_status') }}</th>
                                                <td> {{ transLang('payment_status_arr')[$booking->payment_status] }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ transLang('status') }}</th>
                                                <td>{{ transLang('service_booking_status_arr')[$booking->status] }} </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="service_booking_details">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="box-body grid-view">
                                        <table class="table table-striped table-bordered detail-view">
                                            <tbody>
                                                <tr>
                                                    <th width="30%">{{ transLang('service') }} </th>
                                                    <td> {{ $booking->service->name }} </td>
                                                </tr>
                                                <tr>
                                                    <th>{{ transLang('category') }}</th>
                                                    <td>{{ $booking->service->{"{$ql}category_name"} }} </td>
                                                </tr>
                                                <tr>
                                                    <th>{{ transLang('sub_category') }}</th>
                                                    <td> {{ $booking->service->sub_category_name }} </td>
                                                </tr>
                                                <tr>
                                                    <th>{{ transLang('amount') }} </th>
                                                    <td> {{ number_format($booking->service->amount, 2) }} {{ transLang('sar') }} / {{ transLang('amount_type_arr') [$booking->service->amount_type] }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>{{ transLang('service_type_location') }}</th>
                                                    <td> {{ transLang('action_type')[$booking->service->service_type] }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>{{ transLang($booking->service->service_type == 1 ? 'customer_address' : 'vendor_address') }}</th>
                                                    <td>{{ $booking->service->address }} </td>
                                                </tr>
                                            </tbody>
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
    <script>
        jQuery(function ($) {
            (function () {
                $('.date-class').each((i, el) => {
                    if ($(el).data('date') != '') {
                        $(el).text(formatDate($(el).data('date')));
                    }
                })
            })();

            $(document).on('click', '#refundCancelBtn', async function(e) {
                e.preventDefault();
                let btn = $(this);

                if (await confirmAlert()) {
                    $.ajax({
                        type: 'GET',
                        dataType: 'json',
                        url: "{{ route('admin.service_bookings.refund', $booking->id) }}",
                        beforeSend: () => {
                            btn.attr('disabled', true);
                        },
                        error: (jqXHR, exception) => {
                            btn.attr('disabled', false);
                            infoAlert($(formatErrorMessage(jqXHR, exception)).text());
                        },
                        success: response => {
                            btn.attr('disabled', false);
                            location.reload(true);
                        }
                    });
                }
            });

            $(document).on('click', '.delete-booking', async function(e) {
                e.preventDefault();

                if (await confirmAlert()) {
                    var href = $(this).attr('href');
                    var { tbl, reload_page } = $(this).data();
                    $.get( href, () => {
                        location.replace(`{{ route('admin.service_bookings.index') }}`);
                    })
                    .fail((jqXHR, exception) => infoAlert($(formatErrorMessage(jqXHR, exception)).text()));
                }
            });
        });
    </script>
@endsection