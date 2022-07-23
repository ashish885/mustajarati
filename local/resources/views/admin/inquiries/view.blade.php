<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    <h4 class="modal-title">{{ transLang('details') }}</h4>
</div>
<div class="modal-body">
    <table class="table table-striped table-bordered table-hover">
        <tbody>
            @if ($inquiry->user_id)
                <tr>
                    <th width="20%">{{ transLang('user') }}</th>
                    <td>{{ $inquiry->user }}</td>
                </tr>
            @else
                <tr>
                    <th width="20%">{{ transLang('vendor') }}</th>
                    <td>{{ $inquiry->vendor }}</td>
                </tr>
            @endif
            <tr>
                <th>{{ transLang('name') }}</th>
                <td>{{ $inquiry->name }}</td>
            </tr>
            <tr>
                <th>{{ transLang('email') }}</th>
                <td>{{ $inquiry->email }}</td>
            </tr>
            <tr>
                <th>{{ transLang('mobile') }}</th>
                <td class="dir-ltr">{{ "+{$inquiry->dial_code} {$inquiry->mobile}" }}</td>
            </tr>
            <tr>
                <th>{{ transLang('message') }}</th>
                <td>{{ $inquiry->message }}</td>
            </tr>
        </tbody>
    </table>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> {{ transLang('close') }}</button>
</div>
