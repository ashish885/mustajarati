@extends('admin.layouts.master') 

@section('title') {{ transLang('manage_permissions') }} @endsection
 
@section('content')
    <section class="content-header">
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i> {{ transLang('dashboard') }}</a></li>
            <li><a href="{{ route('admin.role.index') }}"> {{ transLang('all_role') }} </a></li>
            <li class="active">{{ transLang('manage_permissions') }}</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{ transLang('manage_permissions') }}</h3>
                    </div>
                    <div class="box-body">
                        <p class="alert message_box hide"></p>
                        <form class="form-horizontal" id="update-form">
                            @csrf
                            <input type="hidden" name="navigation_id[]" value="1"> 
                            <?php if(count($navigation)) { ?> 
                                <?php foreach($navigation as $group) { ?>
                                    <div class="row">
                                        <div id="permission-user-<?= $group['id'] ?>" class="col-md-12">
                                            <label for="chkAll_<?= $group['id'] ?>" class="col-md-3 col-md-offset-1">
                                                <strong><?= $group['name'] ?></strong>
                                            </label>
                                            <div class="col-md-8">
                                                <label>
                                                    <input type="checkbox" id="chkAll_<?= $group['id'] ?>" name="navigation_id[]" value="<?= $group['id'] ?>" class="checkAll" <?= $group['id'] == 1 ? 'disabled' : '' ?> <?= $group['id'] == 1 ? 'checked' : (in_array($group['id'], $rolePermissions) ? 'checked' : '') ?>> 
                                                </label>
                                            </div>
        
                                            <?php if(isset($group['children']) && count($group['children']))  { ?>
                                                <?php foreach ($group['children'] as $nav) { ?>
                                                    <div class="row">
                                                        <div class="col-xs-12">
                                                            <label for="chkAll_<?= $nav['id'] ?>" class="col-md-3 col-md-offset-1 small_label"><?= $nav['name'] ?></label>
                                                            <div class="col-md-2">
                                                                <label>
                                                                    <input type="checkbox" class="checkAllSub" id="chkAll_<?= $nav['id'] ?>" name="navigation_id[]" value="<?= $nav['id'] ?>" <?= in_array($nav['id'], $rolePermissions) ? 'checked' : '' ?>> 
                                                                </label>
                                                            </div>

        
                                                            <?php if(isset($nav['children']) && count($nav['children']))  { ?>
                                                                <div id="sub-permission-user-<?= $nav['id'] ?>">
                                                                    <?php foreach ($nav['children'] as $navVal) { ?>
                                                                        <div class="row">
                                                                            <div class="col-xs-12">
                                                                                <label for="chkAll_<?= $navVal['id'] ?>" class="col-md-3 col-md-offset-1 sub_small_label"><?= $navVal['name'] ?></label>
                                                                                <div class="col-md-2">
                                                                                    <label>
                                                                                        <input type="checkbox" id="chkAll_<?= $navVal['id'] ?>" name="navigation_id[]" value="<?= $navVal['id'] ?>" <?= in_array($navVal['id'], $rolePermissions) ? 'checked' : '' ?>> 
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    <?php } ?> 
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                <?php } ?> 
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <hr>
                                <?php } ?> 
                            <?php } ?>
                        </form>
                    </div>
                    <div class="box-footer">
                        <div class="col-md-offset-1 col-md-6">
                            <button type="button" class="btn btn-success" id="createBtn"><i class="fa fa-check"></i> {{ transLang('save_permission') }}</button>
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
            $(document).on('change', '.checkAll', function(e) {
                var ContainerID = $(this).val();
                var status = this.checked ? true : false;
                $("#permission-user-"+ContainerID).find("input[type=checkbox]").each(function() {
                    this.checked = status;
                });
            });

            $(document).on('change', '.checkAllSub', function(e) {
                var ContainerID = $(this).val();
                var status = this.checked ? true : false;
                $("#sub-permission-user-"+ContainerID).find("input[type=checkbox]").each(function() {
                    this.checked = status;
                });
            });

            $(document).on('click','#createBtn',function(e) {
                e.preventDefault()
                let btn = $(this);
                let loader = $('.message_box');

                $.ajax({
                    dataType: 'json',
                    type: 'POST',
                    url: "{{ route('admin.role.permission.save', ['id' => Request::segment(4) ]) }}",
                    data: $('#update-form').serialize(),
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
                        location.replace('{{ route("admin.role.index")}}');
                    }
                })
            });
        })
    </script>
@endsection