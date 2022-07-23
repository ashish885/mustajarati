@extends('admin.layouts.master')

@section('title') {{ transLang('all_categories') }} @endsection

@section('content')
<section class="content-header">
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i> {{ transLang('dashboard') }}</a></li>
        <li class="active">{{ transLang('all_categories') }}</li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <div class="box box-danger direct-chat direct-chat-danger">
                <div class="box-body">

                    <div class="container">
                        <div class="row">
                            <div class="col-md-8 col-md-offset-2">
                                <div class="panel panel-default">
                                    <div id="app">
                                        <example-component></example-component>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                    </div>
                </div>
            </div>
</section>
@endsection