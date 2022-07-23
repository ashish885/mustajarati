<div id="reject_app_wrapper" class="modal-content">
    <div class="modal-header">
       <button type="button" class="close" data-dismiss="modal" aria-label="Close">
       <span aria-hidden="true">&times;</span></button>
       <h4 class="modal-title">{{ transLang('reject_application') }}</h4>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-xs-12 col-md-12">
                <p class="alert message_box hide"></p>
                <form id="save-frm" class="form-horizontal">
                    @csrf
                    <textarea name="comments" class="form-control" rows="3" placeholder="{{ transLang('put_your_comments') }}"></textarea>

                    <div class="form-group" style="margin-top: 10px;">
                        <label class="col-sm-3 control-label">{{ transLang('allow_profile_editing') }}</label>
                        <div class="col-sm-6" style="padding-top: 6px;">
                            @foreach (transLang('other_action') as $val => $text)
                                <label>
                                    <input type="radio" name="allow_profile_editing" value="{{ $val }}" {{ $val == 1 ? 'checked' : '' }}> {{ $text }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="box-footer text-right">
        <button type="button" class="btn btn-success" id="submitBtn"><i class="fa fa-check"></i>{{ transLang('submit') }}</button>
        <button type="button" class="btn btn-danger closeBtn" data-dismiss="modal"><i class="fa fa-times"></i>{{ transLang('close') }}</button>
    </div>
 </div>
<script type="text/javascript">
    $('#reject_app_wrapper').on('click', '#submitBtn', function(e) {
        e.preventDefault();
        let btn = $(this);
        let loader = $('#reject_app_wrapper .message_box');

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "{{ route('admin.vendors.reject', $id) }}",
            data: $('#reject_app_wrapper #save-frm').serialize(),
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
                location.replace("{{ route('admin.vendors.index') }}");
            }
        });
    });
</script>
 
