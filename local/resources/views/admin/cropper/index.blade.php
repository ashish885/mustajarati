<div id="image-cropper-wrapper">
    <div class="modal-header">
        <div class="pull-right">
            <button id="crop-btn" class="btn btn-sm btn-success">
                <i class="fa fa-check"></i> {{ transLang('crop') }}
            </button>
            <button data-dismiss="modal" class="btn btn-sm btn-danger hide_model_box">
                <i class="fa fa-times"></i> {{ transLang('close') }}
            </button>
        </div>
        <h4 class="smaller lighter blue no-margin">{{ transLang('crop_image') }}</h4>
    </div>
    <div class="modal-body">
        <img id="img-cropper" style="display:none;">
    </div>
    <div class="modal-footer">
        <button id="crop-btn" class="btn btn-sm btn-success">
            <i class="fa fa-check"></i> {{ transLang('crop') }}
        </button>
        <button data-dismiss="modal" class="btn btn-sm btn-danger hide_model_box">
            <i class="fa fa-times"></i> {{ transLang('close') }}
        </button>
    </div>
</div>
<script type="text/javascript">
    let $cropper_img  = $('#cropper_model').find('.modal-content img');
    function initCropper() {
        let $target = $('#cropper_model').find('.modal-content');
        let { crop_img } = $target.data();
 
        $cropper_img.attr('src', crop_img).show();

        $cropper_img.cropper({
            @if ($enable_ratio)
                cropBoxResizable: true,
                aspectRatio: parseFloat('{{ $width }}') / parseFloat('{{ $height }}'),
            @else
                cropBoxResizable: false,
            @endif
            zoomOnWheel: true,
            dragMode: 'none',
            data: {
                width: parseFloat('{{ $width }}'),
                height: parseFloat('{{ $height }}'),
            }
        });
    }
    setTimeout(() => initCropper(), 400);

    $('#image-cropper-wrapper').on('click', '#crop-btn', function (e) {
        e.preventDefault();
        $(this).prop('disabled', true);
        
        try {
            let mimeType = dataURLtoMimeType($('#image-cropper-wrapper #img-cropper').attr('src'));
            console.log(mimeType);
            alert($name);
            let imageData = $cropper_img.cropper('getCroppedCanvas').toDataURL(mimeType, 0.9);
            $('#{{ $name }}-cropper-preview').attr('src', imageData).show();
            $('[name="{{ $name }}"]').val(imageData);
        } catch(e) {
            console.log(e);
        }
        $('#image-cropper-wrapper .hide_model_box').click();
    });
</script>