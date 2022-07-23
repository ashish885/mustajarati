@extends('admin.layouts.master')

@section('title') {{ transLang('detail') }} @endsection

@section('content')
    <section class="content-header">
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i> {{ transLang('dashboard') }}</a></li>
            <li><a href="{{ route('admin.sub_admins.index') }}"> {{ transLang('all_admins') }} </a></li>
            <li class="active">{{ transLang('detail') }}</li>
        </ol>
    </section>

    <section class="content">
        <p>
            <a class="btn btn-success" href="{{ route('admin.sub_admins.update', ['id' => $admin->id]) }}">{{ transLang('update') }}</a>
        </p>
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{ transLang('detail') }}</h3>
                    </div>
                    <div class="box-body">
                        <div class="offers-view">
                            <table class="table table-striped table-bordered detail-view">
                                <tbody>
                                    </tr>
                                        <th width="20%">{{ transLang('role') }}</th>
                                        <td>{{ $admin->role }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ transLang('name') }}</th>
                                        <td>{{ $admin->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ transLang('email') }}</th>
                                        <td>{{ $admin->email }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ transLang('mobile') }}</th>
                                        <td class="dir-ltr">{{ "+{$admin->dial_code} {$admin->mobile}" }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ transLang('status') }}</th>
                                        <td>{{ transLang('action_status')[$admin->status] }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ transLang('profile_image') }}</th>
                                        <td>
                                            @if($admin->profile_image)
                                                <img src="{{ imageBasePath($admin->profile_image) }}" width="60" /> 
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
  </section>
@endsection