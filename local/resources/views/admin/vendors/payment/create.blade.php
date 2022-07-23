<div id="payment_wrapper" class="modal-content">
    <div class="modal-header">
       <button type="button" class="close" data-dismiss="modal" aria-label="Close">
       <span aria-hidden="true">&times;</span></button>
       <h4 class="modal-title">{{ transLang('add_payment_history') }}</h4>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-xs-12 col-md-12">
                <p class="alert message_box hide"></p>
                <form id="save-frm" class="form-horizontal">
                    @csrf
                    <div class="form-group">
                        <label class="control-label col-md-3 required">{{ transLang('amount') }}</label>
                        <div class="col-md-7">
                            <input type="text" name="amount" class="form-control" placeholder="{{ transLang('amount') }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 required">{{ transLang('transaction_id') }}</label>
                        <div class="col-md-7">
                            <input type="text" name="transaction_id" class="form-control" placeholder="{{ transLang('transaction_id') }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 required">{{ transLang('payment_date') }}</label>
                        <div class="col-md-7">
                            <input type="text" name="payment_date" class="form-control date-picker" placeholder="{{ transLang('payment_date') }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">{{ transLang('comments') }}</label>
                        <div class="col-md-7">
                            <textarea name="comments" class="form-control" placeholder="{{ transLang('comments') }}"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">{{ transLang('attachment') }}</label>
                        <div class="col-md-7">
                            <input type="file" name="attachment">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="box-footer text-right">
        <button type="button" class="btn btn-success" id="createBtn"><i class="fa fa-check"></i>{{ transLang('create') }}</button>
        <button type="button" class="btn btn-danger closeBtn" data-dismiss="modal"><i class="fa fa-times"></i>{{ transLang('close') }}</button>
    </div>
 </div>
<script type="text/javascript">
    $('#payment_wrapper [name="payment_date"]').datepicker({
        format: "yyyy-mm-dd",
        autoclose: true,
        todayHighlight: true,
    });
    
    $('#payment_wrapper').on('click', '#createBtn', function(e) {
        e.preventDefault();
        let btn = $(this);
        let loader = $('#payment_wrapper .message_box');

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "{{ route('admin.vendors.payment.create',$id) }}",
            data: new FormData($('#payment_wrapper #save-frm')[0]),
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
                reloadTable(`payment-table`);
                getPaymentStats();
                $('#payment_wrapper .closeBtn').click();
            }
        });
    });
</script>
 
