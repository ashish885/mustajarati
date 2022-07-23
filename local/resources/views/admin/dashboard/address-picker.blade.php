<div id="address-picker">
    <div class="modal-header">
        <button data-dismiss="modal" class="close" type="button">&#10005;</button>
        <h4 class="smaller lighter blue no-margin">{{ transLang('address_picker') }}</h4>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-xs-12">
                <input type="text" id="pac-input" class="map-input-box" placeholder="{{ transLang('address') }}">
                <div id="locationMapData" class="height-350"></div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button data-dismiss="modal" class="btn btn-sm btn-danger hide_model_box"><i class="ace-icon fa fa-times"></i> {{ transLang('close') }}</button>
    </div>
</div>

<script type="text/javascript">
    @if ($latitude && $longitude)
        initAutocomplete('locationMapData', 'pac-input', 'address', 'latitude', 'longitude', '{{ $latitude }}', '{{ $longitude }}');
    @else
        initAutocomplete('locationMapData', 'pac-input', 'address', 'latitude', 'longitude');
    @endif
</script>