<div id="email_wrapper" class="modal-content">
    <div class="modal-header">
       <button type="button" class="close" data-dismiss="modal" aria-label="Close">
       <span aria-hidden="true">&times;</span></button>
       <h4 class="modal-title">{{ transLang('send_email') }}</h4>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-xs-12 col-md-12">
                <p class="alert message_box hide"></p>
                <form id="save-frm">
                    @csrf
                    <div class="form-group">
                        <label class="control-labelrequired">{{ transLang('to_email') }}</label>
                        <div>
                            <input type="text" class="form-control" placeholder="{{ transLang('to_email') }}" value="{{ $result->email }}" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label required">{{ transLang('subject') }}</label>
                        <div>
                            <input type="text" name="subject" class="form-control" placeholder="{{ transLang('subject') }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label required">{{ transLang('message') }}</label>
                        <div>
                            <textarea name="message" class="form-control" placeholder="{{ transLang('message') }}"></textarea>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="box-footer text-right">
        <button type="button" class="btn btn-success" id="sendBtn"><i class="fa fa-check"></i>{{ transLang('send') }}</button>
        <button type="button" class="btn btn-danger closeBtn" data-dismiss="modal"><i class="fa fa-times"></i>{{ transLang('close') }}</button>
    </div>
 </div>
<script type="text/javascript">
    $('#email_wrapper [name="payment_date"]').datepicker({
        format: "yyyy-mm-dd",
        autoclose: true,
        todayHighlight: true,
    });
    
    $('#email_wrapper').on('click', '#sendBtn', function(e) {
        e.preventDefault();
        let btn = $(this);
        let loader = $('#email_wrapper .message_box');

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "{{ route('admin.inquiries.send.email', $result->id) }}",
            data: $('#email_wrapper #save-frm').serialize(),
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
                $('#email_wrapper .closeBtn').click();
            }
        });
    });
</script>