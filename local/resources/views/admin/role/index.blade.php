@extends('admin.layouts.master')

@section('title') {{ transLang('all_role') }} @endsection

@section('content')
    <section class="content-header">
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i> {{ transLang('dashboard') }}</a></li>
            <li class="active">{{ transLang('all_role') }}</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="row">
                            <div class="col-xs-12 col-sm-6">
                                <h3 class="box-title">{{ transLang('all_role') }}</h3>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                @hasPermission('admin.role.create')
                                    <a href="{{ route('admin.role.create') }}" class="btn btn-success pull-right">{{ transLang('create_new') }}</a>
                                @endhasPermission
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <table class="table table-striped table-bordered table-hover dataTable" id="role-table">
                            <thead>
                                <tr>
                                    <th>{{ transLang('name') }}</th>
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
        $('#role-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("admin.role.list") }}',
            columns : [
                { data: "name"},
                { data: "status_text", name: "status" },
                {
                    mRender: (data, type, row) => {
                        return `
                            @if (hasPermission('admin.role.update'))
                                <a href="{{ route("admin.role.update") }}/${row.id}"><i class="fa fa-edit fa-fw"></i></a>
                            @endif
                            @if (hasPermission('admin.role.permission'))
                                <a href="{{ route("admin.role.permission") }}/${row.id}"><i class="fa fa-universal-access fa-fw"></i></a>
                            @endif
                        `;
                    }, 
                    orderable: false
                }
	        ],
        });
    });

</script>
@endsection