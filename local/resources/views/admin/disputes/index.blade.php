@extends('admin.layouts.master')

@section('title'){{ transLang('all_dispute') }}@endsection

@section('content')
    <section class="content-header">
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i> {{ transLang('dashboard') }}</a></li>
            <li class="active">{{ transLang('all_dispute') }}</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{ transLang('all_dispute') }}</h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-sm-3">  
                                <label class="control-label">{{ transLang('user') }}</label>
                                <div class="form-group">
                                    <select name="user_id" class="form-control select2-class2 reload-tbl" data-placeholder="{{ transLang('choose') }}">
                                        <option value=""></option>
                                        @if ($users->count())
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3"> 
                                <label class="control-label">{{ transLang('vendor') }}</label>
                                 <div class="form-group">
                                    <select name="vendor_id" class="form-control select2-class2 reload-tbl" data-placeholder="{{ transLang('choose') }}">
                                        <option value=""></option>
                                        @if ($vendors->count())
                                            @foreach ($vendors as $vendor)
                                                <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3"> 
                                <label class="control-label">{{ transLang('ticket_status') }}</label> 
                                 <div class="form-group">
                                    <select name="ticket_status" class="form-control select2-class2 reload-tbl" data-placeholder="{{ transLang('choose') }}">
                                        <option value=""></option>
                                        @foreach (transLang('dispute_status_arr') as $key => $val)
                                            <option value="{{ $key }}">{{ $val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> 
                        </div>
                        <br>
                        <table class="table table-striped table-bordered table-hover dataTable" id="data-table" style="white-space:nowrap">
                            <thead>
                                <tr>
                                    <th>{{ transLang('user') }}</th>
                                    <th>{{ transLang('vendor') }}</th>
                                    <th>{{ transLang('ticket_no') }}</th>
                                    <th>{{ transLang('subject') }}</th>
                                    <th>{{ transLang('last_updated') }}</th>
                                    <th>{{ transLang('status') }}</th>
                                    <th>{{ transLang('action') }}</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script type="text/javascript">
        $(function() {
            $(document).on('change', '.reload-tbl', function (e) {
                reloadTable('data-table');
            });

            $('#data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    type: 'GET',
                    url: '{{ route("admin.disputes.list") }}',
                    data: function (params) {
                        params.user_id = $('[name="user_id"]').val();
                        params.vendor_id = $('[name="vendor_id"]').val();
                        params.ticket_status = $('[name="ticket_status"]').val();
                    }
                },
                aaSorting: [[1, 'asc']],
                columns : [
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
                    { data: "ticket_no", name: "disputes.ticket_no" },
                    { data: "subject", name: "disputes.subject" },
                    { data: "updated_at", mRender: data => formatDate(data) },
                    { 
                        data: "dispute_status",
                        name: "disputes.status",
                        mRender: (data, type, row) => {
                            let statusArr = {1: 'danger', 2: 'success', 3: 'default'};
                            return `<small class="label label-${statusArr[row.status]}">${data}</small>`;
                        }
                    },
                    {
                        mRender: (data, type, row) => {
                            return `
                                @if (hasPermission('admin.disputes.view'))
                                    <a href="{{ route("admin.disputes.view") }}/${row.id}" ><i class="fa fa-eye fa-fw"></i></a>
                                @endif
                                @if (hasPermission('admin.disputes.delete'))
                                    <a href="{{ route("admin.disputes.delete") }}/${row.id}" class="delete-entry" data-tbl="data"><i class="fa fa-trash fa-fw"></i></a>
                                @endif
                            `;
                        }, 
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        });
    </script>
@endsection