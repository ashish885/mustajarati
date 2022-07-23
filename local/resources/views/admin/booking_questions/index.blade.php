@extends('admin.layouts.master')

@section('title') {{ transLang('all_booking_question') }} @endsection

@section('content')
    <section class="content-header">
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i> {{ transLang('dashboard') }}</a></li>
            <li class="active">{{ transLang('all_booking_question') }}</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="row">
                            <div class="col-xs-12 col-sm-6">
                                <h3 class="box-title">{{ transLang('all_booking_question') }}</h3>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                @hasPermission('admin.booking_questions.create')
                                    <a href="{{ route('admin.booking_questions.create') }}" class="btn btn-success pull-right">{{ transLang('create_new') }}</a>
                                @endhasPermission
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <table class="table table-striped table-bordered table-hover dataTable" id="data-table">
                            <thead>
                                <tr>
                                    <th>{{ transLang('question_for') }}</th>
                                    <th>{{ transLang('question') }}</th>
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
                ajax: '{{ route("admin.booking_questions.list") }}',
                columns : [
                    { data: "type_text", name: "type" },
                    { data: "question", name: "{{ $ql }}question" },
                    {
                        mRender: (data, type, row) => {
                            return `
                                @if (hasPermission('admin.booking_questions.update'))
                                    <a href="{{ route("admin.booking_questions.update") }}/${row.id}"><i class="fa fa-edit fa-fw"></i></a>
                                @endif
                                @if (hasPermission('admin.booking_questions.delete'))
                                    <a href="{{ route("admin.booking_questions.delete") }}/${row.id}" class="delete-entry" data-tbl="data"><i class="fa fa-trash fa-fw"></i></a>
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