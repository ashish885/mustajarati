@extends('admin.layouts.master')

@section('title') {{ transLang('all_inquiry') }} - {{ transLang($type) }} @endsection

@section('content')
    <section class="content-header">
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i> {{ transLang('dashboard') }}</a></li>
            <li class="active">{{ transLang('all_inquiry') }} - {{ transLang($type) }}</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">   
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{ transLang('all_inquiry') }} - {{ transLang($type) }}</h3>
                    </div>
                    <div class="box-body">
                        <table class="table table-striped table-bordered table-hover dataTable" id="data-table">
                            <thead>
                                <tr>
                                    @if ($type == 'users')
                                        <th>{{ transLang('user') }}</th>
                                    @else
                                        <th>{{ transLang('vendor') }}</th>
                                    @endif
                                    <th>{{ transLang('name') }}</th>
                                    <th>{{ transLang('email') }}</th>
                                    <th>{{ transLang('mobile') }}</th>
                                    <th>{{ transLang('message') }}</th>
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
            $('#data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("admin.inquiries.list", $type) }}',
                columns : [
                    @if ($type == 'users')
                        @if (hasPermission('admin.users.view'))
                            {
                                data: "user",
                                name: "users.name",
                                mRender: (data, type, row) => row.user_id ? `<a href="{{ route('admin.users.view') }}/${row.user_id}" target="_blank">${data}</a>` : '',
                            },
                        @else
                            { data: "user", name: "users.name" },
                        @endif
                    @else
                        @if (hasPermission('admin.vendors.view'))
                            {
                                data: "vendor",
                                name: "vendors.name",
                                mRender: (data, type, row) => row.vendor_id ? `<a href="{{ route('admin.vendors.view') }}/${row.vendor_id}" target="_blank">${data}</a>` : '',
                            },
                        @else
                            { data: "vendor", name: "vendors.name" },
                        @endif
                    @endif
                    { data: "name" },
                    { data: "email" },
                    { 
                        class:'dir-ltr',
                        data : "mobile",
                        name : "mobile",
                        mRender: (data, type, row) => data ? `+${row.dial_code} ${data}` : ''
                    },
                    //  trimToLength
                    { data: "message", mRender: data => data ? data.trimToLength(50) : '' },
                    {
                        mRender: (data, type, row) => {
                            let keyId = '{{ $type == "users" ? "user_id" : "vendor_id" }}';
                            return `
                                <a href="{{ route("admin.inquiries.view") }}/${row.id}" data-toggle="modal" data-target="#remote_model"><i class="fa fa-eye fa-fw"></i></a>
                                <a href="{{ route("admin.inquiries.send.email") }}/${row.id}" data-toggle="modal" data-target="#remote_model"><i class="fa fa-envelope-o fa-fw"></i></a>
                                ${ row[keyId] ? `<a href="{{ route("admin.inquiries.send.notification") }}/${row.id}" data-toggle="modal" data-target="#remote_model"><i class="fa fa-bell-o fa-fw"></i></a>` : '' }
                                @if (hasPermission('admin.inquiries.delete'))
                                    <a href="{{ route("admin.inquiries.delete") }}/${row.id}" class="delete-entry" data-tbl="data"><i class="fa fa-trash fa-fw"></i></a>
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