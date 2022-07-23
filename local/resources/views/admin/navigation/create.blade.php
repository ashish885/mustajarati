@extends('admin.layouts.master') 

@section('title') {{ transLang('create_navigation') }} @endsection
 
@section('content')
	<section class="content-header">
		<ol class="breadcrumb">
			<li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i> {{ transLang('dashboard') }}</a></li>
			<li><a href="{{ route('admin.navigation.index') }}"> {{ transLang('all_navigation') }} </a></li>
			<li class="active">{{ transLang('create_navigation') }}</li>
		</ol>
	</section>

	<section class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="box box-primary">
					<div class="box-header with-border">
						<h3 class="box-title">{{ transLang('create_navigation') }}</h3>
					</div>
					<div class="box-body">
						<p class="alert message_box hide"></p>
						<form class="form-horizontal" id="create-form">
                            @csrf
							<div class="form-group">
								<label class="col-sm-2 control-label required">{{ transLang('name') }}</label>
								<div class="col-sm-6">
									<input type="text" class="form-control" name="name" placeholder="{{ transLang('name') }}">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label required">{{ transLang('en_name') }}</label>
								<div class="col-sm-6">
									<input type="text" class="form-control" name="en_name" placeholder="{{ transLang('en_name') }}">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label required">{{ transLang('action_path') }}</label>
								<div class="col-sm-6">
									<input type="text" class="form-control" name="action_path" placeholder="{{ transLang('action_path') }}">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label">{{ transLang('icon') }}</label>
								<div class="col-sm-6">
									<input type="text" class="form-control" name="icon" placeholder="{{ transLang('icon') }}">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label required">{{ transLang('display_order') }}</label>
								<div class="col-sm-6">
									<input type="text" class="form-control" name="display_order" placeholder="{{ transLang('display_order') }}">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label required">{{ transLang('parent_id') }}</label>
								<div class="col-sm-6">
									<select class="form-control select2-class" name="parent_id">
										<option value="">{{ transLang('no_parent') }}</option>
										@if($parent_navigation->count())
											@foreach($parent_navigation as $key => $navigation)
												<option value="{{ $navigation->id }}">{{ $navigation->name }}</option>
											@endforeach
										@endif
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label required">{{ transLang('show_in_menu') }}</label>
								<div class="col-sm-6">
									<select class="form-control" name="show_in_menu">
										@foreach(transLang('other_action') as $key => $status)
											<option value="{{ $key }}">{{ $status }}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label required">{{ transLang('show_in_permission') }}</label>
								<div class="col-sm-6">
									<select class="form-control" name="show_in_permission">
										@foreach(transLang('other_action') as $key => $status)
											<option value="{{ $key }}">{{ $status }}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label required">{{ transLang('status') }}</label>
								<div class="col-sm-6">
									<select class="form-control" name="status">
										@foreach(transLang('action_status') as $key => $status)
											<option value="{{ $key }}">{{ $status }}</option>
										@endforeach
									</select>
								</div>
							</div>
						</form>
					</div>
					<div class="box-footer">
						<div class="col-sm-offset-1 col-sm-6">
							<button type="button" class="btn btn-success" id="createBtn"><i class="fa fa-check"></i> {{ transLang('create') }}</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
@endsection
 
@section('scripts')
	<script type="text/javascript">
		jQuery(function($) {
			$(document).on('click','#createBtn',function(e) {
				e.preventDefault();
                let btn = $(this);
                let loader = $('.message_box');

				$.ajax({
					dataType: 'json',
					type: 'POST',
					url: "{{ route('admin.navigation.create') }}",
					data: $('#create-form').serialize(),
                    beforeSend: () => {
                        btn.attr('disabled',true);
                        loader.html(`{!! transLang('loader_message') !!}`).removeClass('hide alert-danger alert-success').addClass('alert-info');
                    },
                    error: (jqXHR, exception) => {
                        btn.attr('disabled',false);
                        loader.html(formatErrorMessage(jqXHR, exception)).removeClass('alert-info').addClass('alert-danger');
                    },
                    success: response => {
                        btn.attr('disabled',false);
                        loader.html(response.message).removeClass('alert-info').addClass('alert-success');
                        location.replace('{{ route("admin.navigation.index")}}');
					}
				});
			});
		});
	</script>
@endsection