@extends('admin.layouts.master')

@section('title') {{ transLang('detail') }} @endsection

@section('content')
    <section class="content-header">
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i>
                    {{ transLang('dashboard') }}</a></li>
            <li><a href="{{ route('admin.product_bookings.index') }}"> {{ transLang('all_product_bookings') }} </a></li>
            <li class="active">{{ transLang('detail') }}</li>
        </ol>
    </section>

    <section class="content">
        <!-- 1.Pending, 2.Ongoing, 3.Completed, 4.Rejected, 5.Cancelled -->
        <p>
            @if ($booking->status < 3)
                <a class="btn btn-warning" href="{{ route('admin.product_bookings.cancel.index', $booking->id) }}" data-toggle="modal" data-target="#remote_model"><i class="fa fa-times"></i> {{ transLang('cancel_booking') }}</a>
            @endif

            <a class="btn btn-danger delete-booking" href="{{ route('admin.product_bookings.delete', $booking->id) }}"><i class="fa fa-trash"></i> {{ transLang('delete_booking') }}</a>

            @if ($booking->payment_init_id && !$booking->is_refund_initiated && $booking->status == 3)
                <a class="btn btn-warning" href="{{ route('admin.product_bookings.refund', $booking->id) }}" data-toggle="modal" data-target="#remote_model"><i class="fa fa-undo"></i> {{ transLang('refund_money') }}</a>
            @endif

            @if ($booking->payment_init_id && !$booking->is_cancelled_amount_refunded && in_array($booking->status, [4, 5]))
                <a id="refundCancelBtn" class="btn btn-warning"><i class="fa fa-undo"></i> {{ transLang('refund_money') }}</a>
            @endif
        </p>
        <div class="row">
            <div class="col-md-12">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab_basic" data-toggle="tab">{{ transLang('booking_info') }}</a></li>
                        <li><a href="#details-tab"  data-toggle="tab" >{{ transLang('product') }}</a></li>
                        @if (in_array($booking->status, [2, 3]))
                            <li><a href="#questions-tab"  data-toggle="tab" >{{ transLang('questions') }}</a></li>
                            <li><a href="#action-tab"  data-toggle="tab" >{{ transLang('booking_details') }}</a></li>
                        @endif
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
                                                    <b>{{ transLang('total_hours') }}</b>: {{ $booking->total_hours }}
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

                                    <h4 style="font-weight: 600;">{{ transLang('extra_charges') }}</h4>
                                    <table class="table table-striped table-bordered detail-view">
                                        <tr>
                                            <td width="33%"><b>{{ transLang('drop_charges') }}</b>: {{ number_format($booking->drop_charges, 2) }} {{ transLang('sar') }}</td>
                                            <td width="33%"><b>{{ transLang('extra_hours') }}</b>: {{ $booking->extra_hours }}</td>
                                            <td width="33%"><b>{{ transLang('extra_hours_charges') }}</b>: {{ number_format($booking->extra_hours_charges, 2) }} {{ transLang('sar') }}</td>
                                        </tr>
                                        <tr>
                                            <td><b>{{ transLang('damage_charges') }}</b>: {{ number_format($booking->damage_charges, 2) }} {{ transLang('sar') }}</td>
                                            <td><b>{{ transLang('cancellation_charges') }}</b>: {{ number_format($booking->cancellation_charges, 2) }} {{ transLang('sar') }}</td>
                                        </tr>
                                    </table>

                                    <h4 style="font-weight: 600;">{{ transLang('booking_summary') }}</h4>
                                    <table class="table table-striped table-bordered detail-view">
                                        <tr>
                                            <th width="20%">{{ transLang('sub_total') }}</th>
                                            <td>{{ number_format($booking->subtotal, 2) }} {{ transLang('sar') }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ transLang('drop_charges') }}</th>
                                            <td>{{ number_format($booking->drop_charges, 2) }} {{ transLang('sar') }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ transLang('extra_charges') }}</th>
                                            <td>{{ number_format($booking->extra_hours_charges, 2) }} {{ transLang('sar') }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ transLang('damage_charges') }}</th>
                                            <td>{{ number_format($booking->damage_charges, 2) }} {{ transLang('sar') }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ transLang('tax_amount') }} ({{ str_replace('.00', '', $booking->tax_percentage) }}%)</th>
                                            <td>{{ number_format($booking->tax_amount, 2) }} {{ transLang('sar') }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ transLang('security_amount') }}</th>
                                            <td>{{ number_format($booking->security_amount, 2) }} {{ transLang('sar') }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ transLang('total_amount') }}</th>
                                            <td>{{ number_format($booking->total_amount, 2) }} {{ transLang('sar') }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ transLang('paid_amount') }}</th>
                                            <td>{{ number_format($booking->paid_amount, 2) }} {{ transLang('sar') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane" id="details-tab">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-striped table-bordered detail-view">
                                        <tbody>
                                            <tr>
                                                <th width="20%">{{ transLang('product') }}</th>
                                                <td>{{ $booking->details->{"{$ql}name"} }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ transLang('category') }}</th>
                                                <td>{{ $booking->details->{"{$ql}category_name"} }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ transLang('sub_category') }}</th>
                                                <td>{{ $booking->details->{"{$ql}sub_category_name"} }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ transLang('amount') }}</th>
                                                <td>{{ number_format($booking->details->amount, 2) }} {{ transLang('sar') }} / {{ transLang('amount_type_arr')[$booking->details->amount_type] }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ transLang('delay_charges') }}</th>
                                                <td>{{ number_format($booking->details->delay_charges, 2) }} {{ transLang('sar') }} / {{ @transLang('delay_charges_arr')[$booking->details->delay_charges_type] }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ transLang('delivery_type') }}</th>
                                                <td>{{ transLang('pickup_type_arr')[$booking->details->pickup_type] }}</td>
                                            </tr>
                                            {{-- 1.Pickup, 2.Drop --}}
                                            <tr>
                                                <th>{{ transLang($booking->details->pickup_type == 1 ? 'customer_address' : 'vendor_address') }}</th>
                                                @if($booking->details->pickup_type == 1)
                                                    <td>{{$booking->details->pickup_location }}</td>
                                                @else 
                                                    <td>{{$booking->details->drop_location }}</td>
                                                @endif
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane" id="questions-tab">
                            @if ($booking->questions->where('type', 1)->count())
                                <h4>{{ transLang('ans_by_user') }}</h4>
                                <div class="row">
                                    <div class="col-xs-12">
                                        <table class="table table-striped table-bordered detail-view">
                                            @foreach ($booking->questions->where('type', 1) as $row)
                                                <tr><th>{{ transLang('que', ['no' => $loop->iteration]) }} {{ $row->{"{$ql}question"} }}</th></tr>
                                                <tr><td><b>{{ transLang('answer') }}</b> {{ transLang('other_action')[$row->answer] }}</td></tr>
                                            @endforeach
                                        </table>
                                    </div>
                                </div>
                            @endif
                            @if ($booking->questions->where('type', 2)->count())
                                <h4>{{ transLang('ans_by_vendor') }}</h4>
                                <div class="row">
                                    <div class="col-xs-12">
                                        <table class="table table-striped table-bordered detail-view">
                                            @foreach ($booking->questions->where('type', 2) as $row)
                                                <tr><th>{{ transLang('que', ['no' => $loop->iteration]) }} {{ $row->{"{$ql}question"} }}</th></tr>
                                                <tr><td><b>{{ transLang('answer') }}</b> {{ transLang('other_action')[$row->answer] }}</td></tr>
                                            @endforeach
                                        </table>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="tab-pane" id="action-tab">
                            <div class="row">
                                <div class="col-xs-12">
                                    <table class="table table-striped table-bordered detail-view">
                                        <tr>
                                            <td width="24%">&nbsp;</td>
                                            <th width="38%">{{ transLang('handing_over_product') }}</th>
                                            <th width="38%">{{ transLang('receiving_product') }}</th>
                                        </tr>
                                        <tr>
                                            <th>{{ transLang('date_time') }}</th>
                                            <td class="date-class" data-date="{{ @$booking->vendor_action->action_datetime }}">{{ @$booking->vendor_action->action_datetime }}</td>
                                            <td class="date-class" data-date="{{ @$booking->user_action->action_datetime }}">{{ @$booking->user_action->action_datetime }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ transLang('notes') }}</th>
                                            <td>{{ @$booking->vendor_action->notes }}</td>
                                            <td>{{ @$booking->user_action->notes }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ transLang('images') }}</th>
                                            <td>
                                                @if (@$booking->vendor_action->first_image)
                                                    <a href="{{ imageBasePath($booking->vendor_action->first_image) }}" data-fancybox="receive" data-id="{{ $booking->vendor_action->first_image }}"><img src="{{ imageBasePath($booking->vendor_action->first_image) }}" width="80"></a>
                                                @endif
                                                @if (@$booking->vendor_action->second_image)
                                                    <a href="{{ imageBasePath($booking->vendor_action->second_image) }}" data-fancybox="receive" data-id="{{ $booking->vendor_action->first_image }}"><img src="{{ imageBasePath($booking->vendor_action->second_image) }}" width="80"></a>
                                                @endif
                                                @if (@$booking->vendor_action->third_image)
                                                    <a href="{{ imageBasePath($booking->vendor_action->third_image) }}" data-fancybox="receive" data-id="{{ $booking->vendor_action->first_image }}"><img src="{{ imageBasePath($booking->vendor_action->third_image) }}" width="80"></a>
                                                @endif
                                                @if (@$booking->vendor_action->forth_image)
                                                    <a href="{{ imageBasePath($booking->vendor_action->forth_image) }}" data-fancybox="receive" data-id="{{ $booking->vendor_action->first_image }}"><img src="{{ imageBasePath($booking->vendor_action->forth_image) }}" width="80"></a>
                                                @endif
                                            </td>
                                            <td>
                                                @if (@$booking->user_action->first_image)
                                                    <a href="{{ imageBasePath($booking->user_action->first_image) }}" data-fancybox="handover" data-id="{{ $booking->user_action->first_image }}"><img src="{{ imageBasePath($booking->user_action->first_image) }}" width="80"></a>
                                                @endif
                                                @if (@$booking->user_action->second_image)
                                                    <a href="{{ imageBasePath($booking->user_action->second_image) }}" data-fancybox="handover" data-id="{{ $booking->user_action->first_image }}"><img src="{{ imageBasePath($booking->user_action->second_image) }}" width="80"></a>
                                                @endif
                                                @if (@$booking->user_action->third_image)
                                                    <a href="{{ imageBasePath($booking->user_action->third_image) }}" data-fancybox="handover" data-id="{{ $booking->user_action->first_image }}"><img src="{{ imageBasePath($booking->user_action->third_image) }}" width="80"></a>
                                                @endif
                                                @if (@$booking->user_action->forth_image)
                                                    <a href="{{ imageBasePath($booking->user_action->forth_image) }}" data-fancybox="handover" data-id="{{ $booking->user_action->first_image }}"><img src="{{ imageBasePath($booking->user_action->forth_image) }}" width="80"></a>
                                                @endif
                                            </td>
                                        </tr>
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
    <script>
        jQuery(function ($) {
            (function () {
                $('.date-class').each((i, el) => {
                    if ($(el).data('date') != '') {
                        $(el).text(formatDate($(el).data('date')));
                    }
                });
            })();

            $(document).on('click', '#refundCancelBtn', async function(e) {
                e.preventDefault();
                let btn = $(this);

                if (await confirmAlert()) {
                    $.ajax({
                        type: 'GET',
                        dataType: 'json',
                        url: "{{ route('admin.product_bookings.refund.cancellation', $booking->id) }}",
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
                        location.replace(`{{ route('admin.product_bookings.index') }}`);
                    })
                    .fail((jqXHR, exception) => infoAlert($(formatErrorMessage(jqXHR, exception)).text()));
                }
            });
        });
    </script>
@endsection