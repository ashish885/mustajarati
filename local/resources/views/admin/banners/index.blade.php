@extends('admin.layouts.master')

@section('title') {{ transLang('all_banners') }} @endsection

@section('content')
    <section class="content-header">
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i> {{ transLang('dashboard') }}</a></li>
            <li class="active">{{ transLang('all_banners') }}</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="row">
                            <div class="col-xs-12 col-sm-6">
                                <h3 class="box-title">{{ transLang('all_banners') }}</h3>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                @hasPermission('admin.banners.create')
                                    <a href="{{ route('admin.banners.create') }}" class="btn btn-success pull-right">{{ transLang('create_new') }}</a>
                                @endhasPermission
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <table class="table table-striped table-bordered table-hover dataTable" id="data-table">
                            <thead>
                                <tr>
                                    <th>{{ transLang('image') }}</th>
                                    {{-- <th>{{ transLang('click_type') }}</th> --}}
                                    {{-- <th>{{ transLang('product') }}</th>
                                    <th>{{ transLang('service') }}</th> --}}
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
            $('#data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("admin.banners.list") }}',
                columns : [
                    { data: "image", name: "{{ $ql }}image", mRender: data => `<img src="{{ imageBasePath() }}/${data}" width="80">` },
                    // { data: "click_type_text", name: "click_type" },
                    // { 
                    //     data: "product",
                    //     name: "products.{{ $ql }}name",
                    //     @if (hasPermission('admin.products.view'))
                    //         mRender: (data, type, row) => `<a href="" target="_blank">${data}</a>`
                    //     @endif
                    // },
                    // { 
                    //     data: "service",
                    //     name: "services.{{ $ql }}name",
                    //     @if (hasPermission('admin.services.view'))
                    //         mRender: (data, type, row) => `<a href="" target="_blank">${data}</a>`
                    //     @endif
                    // },
                    { data: "status_text", name: "status" },
                    {
                        mRender: (data, type, row) => {
                            return `
                                @if (hasPermission('admin.banners.update'))
                                    <a href="{{ route("admin.banners.update") }}/${row.id}"><i class="fa fa-edit fa-fw"></i></a>
                                @endif
                                @if (hasPermission('admin.banners.delete'))
                                    <a href="{{ route("admin.banners.delete") }}/${row.id}" class="delete-entry" data-tbl="data"><i class="fa fa-trash fa-fw"></i></a>
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