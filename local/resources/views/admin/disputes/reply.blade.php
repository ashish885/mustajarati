<div id="reply_warpper">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">{{ transLang('reply') }}</h4>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="message_box alert hide"></div>
                <form id="replyFrm">
                    @csrf
                    <div class="form-group">
                        <label class="required">{{ transLang('message') }}</label>
                        <div>
                            <textarea class="form-control" name="message" placeholder="{{ transLang('message') }}"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>{{ transLang('attachment') }}</label>
                        <div>
                            <input type="file" name="attachment">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button id="replyBtn" class="btn btn-success" type="button"><i class="fa fa-check"></i> {{ transLang('submit') }}</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> {{ transLang('close') }}</button>
    </div>
</div>

<script>
    $('#reply_warpper').on('click','#replyBtn', function (e) {
        e.preventDefault();
        let btn = $(this);
        let loader = $('.message_box');
        
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "{{ route('admin.disputes.post.reply', $id) }}",
            data: new FormData($('#reply_warpper form')[0]),
            processData: false,
            contentType: false,
            beforeSend: () => {
                btn.attr('disabled',true);
                loader.html(`{!! transLang('loader_message') !!}`).removeClass('hide alert-danger alert-success').addClass('alert-info');
            },
            error: (jqXHR, exception) => {
                btn.attr('disabled',false);
                loader.html(formatErrorMessage(jqXHR, exception)).removeClass('alert-info').addClass('alert-danger');
            },
            success: response => {
                btn.attr('disabled',false);
                loader.html(response.message).removeClass('alert-info').addClass('alert-success');
                location.reload(true);
            }
        });
    });
</script>