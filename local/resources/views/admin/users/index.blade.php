@extends('admin.layouts.master')

@section('title') {{ transLang('all_users') }} @endsection

@section('content')
    <section class="content-header">
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i> {{ transLang('dashboard') }}</a></li>
            <li class="active">{{ transLang('all_users') }}</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="row">
                            <div class="col-xs-12 col-sm-6">
                                <h3 class="box-title">{{ transLang('all_users') }}</h3>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                @hasPermission('admin.users.create')
                                    <a href="{{ route('admin.users.create') }}" class="btn btn-success pull-right">{{ transLang('create_new') }}</a>
                                @endhasPermission
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <table class="table table-striped table-bordered table-hover dataTable" id="data-table">
                            <thead>
                                <tr>
                                    <th>{{ transLang('name') }}</th>
                                    <th>{{ transLang('email') }}</th>
                                    <th>{{ transLang('mobile') }}</th>
                                    <th>{{ transLang('status') }}</th>
                                    <th>{{ transLang('registered_on') }}</th>
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
                ajax: '{{ route("admin.users.list") }}',
                columns : [
                    { data: "name" },
                    { data: "email" },
                    { data: "mobile", mRender: (data, type, row) => `+${row.dial_code} ${row.mobile}`, class:'dir-ltr' },
                    { data: "status_text", name: "status" },
                    { data: "created_at", mRender: data => formatDate(data) },
                    {
                        mRender: (data, type, row) => {
                            return `
                                @if (hasPermission('admin.users.update'))
                                    <a href="{{ route("admin.users.update") }}/${row.id}"><i class="fa fa-edit fa-fw"></i></a>
                                @endif
                                @if (hasPermission('admin.users.delete'))
                                    <a href="{{ route("admin.users.delete") }}/${row.id}" class="delete-entry" data-tbl="data"><i class="fa fa-trash fa-fw"></i></a>
                                @endif
                                @if (hasPermission('admin.users.password_reset'))
                                    <a href="{{ route("admin.users.password_reset") }}/${row.id}" class="danger"><i class="fa fa-key fa-fw"></i></a>
                                @endif
                                <a href="{{ route("admin.users.view") }}/${row.id}"><i class="fa fa-eye fa-fw"></i></a>
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