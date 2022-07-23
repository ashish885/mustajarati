@extends('admin.layouts.master')

@section('title') {{ transLang('all_products') }} @endsection

@section('content')
    <section class="content-header">
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i> {{ transLang('dashboard') }}</a></li>
            <li class="active">{{ transLang('all_products') }}</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="row">
                            <div class="col-xs-12 col-sm-6">
                                <h3 class="box-title">{{ transLang('all_products') }}</h3>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                @hasPermission('admin.products.create')
                                    <a href="{{ route('admin.products.create') }}" class="btn btn-success pull-right">{{ transLang('create_new') }}</a>
                                @endhasPermission
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <table class="table table-striped table-bordered table-hover dataTable" id="data-table" style="white-space:nowrap">
                            <thead>
                                <tr>
                                    <th>{{ transLang('image') }}</th>
                                    <th>{{ transLang('name') }}</th>
                                    <th>{{ transLang('vendor') }}</th>
                                    <th>{{ transLang('category') }}</th>
                                    <th>{{ transLang('sub_category') }}</th>
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
                ajax: '{{ route("admin.products.list") }}',
                aaSorting: [[1, 'asc']],
                columns : [
                    { data: "default_image", mRender: data => data ? `<img src="{{ imageBasePath('') }}/${data}" width="40px"/>` : `` },
                    { data: "name", name: "{{ $ql }}name" },
                    { 
                        data: "vendor_name",
                        name: "vendors.name",
                        @if(hasPermission('admin.vendors.index'))
                            mRender: (data, type, row) => row.vendor_id ? `<a href="{{ route('admin.vendors.view') }}/${row.vendor_id}" target="_blank">${data}</a>` : ``,
                        @endif
                    },
                    { data: "category", name: "categories.{{ $ql }}name" },
                    { data: "sub_category", name: "sub_categories.{{ $ql }}name" },
                    {
                        mRender: (data, type, row) => {
                            return `
                                @if (hasPermission('admin.products.view'))
                                    <a href="{{ route("admin.products.view") }}/${row.id}"><i class="fa fa-eye fa-fw"></i></a>
                                @endif
                                @if (hasPermission('admin.products.update'))
                                    <a href="{{ route("admin.products.update") }}/${row.id}"><i class="fa fa-edit fa-fw"></i></a>
                                @endif
                                @if (hasPermission('admin.products.delete'))
                                    <a href="{{ route("admin.products.delete") }}/${row.id}" class="delete-entry" data-tbl="data"><i class="fa fa-trash fa-fw"></i></a>
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