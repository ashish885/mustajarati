@extends('admin.layouts.master')

@section('title') {{ transLang('all_testimonials') }} @endsection

@section('content')
    <section class="content-header">
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i> {{ transLang('dashboard') }}</a></li>
            <li class="active">{{ transLang('all_testimonials') }}</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="row">
                            <div class="col-xs-12 col-sm-6">
                                <h3 class="box-title">{{ transLang('all_testimonials') }}</h3>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                @hasPermission('admin.testimonial.create')
                                    <a href="{{ route('admin.testimonial.create') }}" class="btn btn-success pull-right">{{ transLang('create_new') }}</a>
                                @endhasPermission
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <table class="table table-striped table-bordered table-hover dataTable" id="data-table" style="white-space:nowrap">
                            <thead>
                                <tr>
                                    <th>{{ transLang('title') }}</th>
                                    <th>{{ transLang('description') }}</th>
                                    <th>{{ transLang('rating') }}</th> 
                                    <th>{{ transLang('customer_name') }}</th> 
                                    <th>{{ transLang('city_name') }}</th> 
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
                ajax: '{{ route("admin.testimonial.list") }}',
                columns : [
                    { data: "title",  name: "{{ $ql }}title" },
                    { data: "description" },
                    { data: "rating" },
                    { data: "customer_name" },
                    { data: "city_name", name: 'cities.{{ $ql }}name' },
            
                    {
                        mRender: (data, type, row) => {
                            return `
                                @if (hasPermission('admin.testimonial.update'))
                                    <a href="{{ route("admin.testimonial.update") }}/${row.id}"><i class="fa fa-edit fa-fw"></i></a>
                                @endif
                                @if (hasPermission('admin.testimonial.delete'))
                                    <a href="{{ route("admin.testimonial.delete") }}/${row.id}" class="delete-entry" data-tbl="data"><i class="fa fa-trash fa-fw"></i></a>
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