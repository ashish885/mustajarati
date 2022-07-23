<div id="refund_wrapper" class="modal-content">
    <div class="modal-header">
       <button type="button" class="close" data-dismiss="modal" aria-label="Close">
       <span aria-hidden="true">&times;</span></button>
       <h4 class="modal-title">{{ transLang('refund_money') }}</h4>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-xs-12 col-md-12">
                <p class="alert message_box hide"></p>
                <form class="form-horizontal">
                    @csrf
                    <div class="form-group">
                        <label class="control-label col-md-4 required">{{ transLang('refundable_amount') }}</label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" placeholder="{{ transLang('refundable_amount') }}" value="{{ $booking->refundable_amount }}" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4 required">{{ transLang('damage_charges') }}</label>
                        <div class="col-md-7">
                            <input type="text" name="damage_charges" class="form-control" placeholder="{{ transLang('damage_charges') }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4 required">{{ transLang('new_refundable_amount') }}</label>
                        <div class="col-md-7">
                            <input type="text" id="new_refundable_amount" class="form-control" placeholder="{{ transLang('new_refundable_amount') }}" value="{{ $booking->refundable_amount }}" readonly>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="box-footer text-right">
        <button type="button" class="btn btn-success" id="refundBtn"><i class="fa fa-check"></i>{{ transLang('refund') }}</button>
        <button type="button" class="btn btn-danger closeBtn" data-dismiss="modal"><i class="fa fa-times"></i>{{ transLang('close') }}</button>
    </div>
 </div>
<script type="text/javascript">
    $('#refund_wrapper').on('keyup', '[name="damage_charges"]', function(e) {
        let damage_charges = $(this).val(),
            refundable_amount = parseFloat('{{ $booking->refundable_amount }}');

        damage_charges = isNaN(parseFloat(damage_charges)) ? 0 : parseFloat(damage_charges);

        $('#refund_wrapper #new_refundable_amount').val((refundable_amount > damage_charges ? refundable_amount - damage_charges : 0).toFixed(2));
    });

    $('#refund_wrapper').on('click', '#refundBtn', function(e) {
        e.preventDefault();
        let btn = $(this);
        let loader = $('#refund_wrapper .message_box');

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "{{ route('admin.product_bookings.refund', $booking->id) }}",
            data: $('#refund_wrapper form').serialize(),
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
                $('#refund_wrapper .closeBtn').click();
                location.reload(true);
            }
        });
    });
</script>
 
