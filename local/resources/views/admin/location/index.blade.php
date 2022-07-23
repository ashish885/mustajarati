@extends('admin.layouts.master')

@section('title') {{ transLang('all_countries') }} @endsection

@section('content')
<section class="content-header">
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i> {{ transLang('dashboard') }}</a></li>
        <li class="active">{{ transLang('all_countries') }}</li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="container mt-5">
                    <h2>Implement Google Autocomplete Address in Laravel 8</h2>
                    <div class="form-group">
                        <label>Location/City/Address</label>
                        <input type="text" name="autocomplete" id="autocomplete" class="form-control" placeholder="Choose Location">
                    </div>
                    <div class="form-group" id="latitudeArea">
                        <label>Latitude</label>
                        <input type="text" id="latitude" name="latitude" class="form-control">
                    </div>
                    <div class="form-group" id="longtitudeArea">
                        <label>Longitude</label>
                        <input type="text" name="longitude" id="longitude" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>

            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script type="text/javascript" src="https://maps.google.com/maps/api/js?key=config('cms.google_api_key')=places&callback=initAutocomplete"></script>
<script type="text/javascript">
    $(function() {
        $("#latitudeArea").addClass("d-none");
        $("#longtitudeArea").addClass("d-none");
        google.maps.event.addDomListener(window, 'load', initialize);

        function initialize() {
            var input = document.getElementById('autocomplete');
            var autocomplete = new google.maps.places.Autocomplete(input);
            autocomplete.addListener('place_changed', function() {
                var place = autocomplete.getPlace();
                $('#latitude').val(place.geometry['location'].lat());
                $('#longitude').val(place.geometry['location'].lng());
                $("#latitudeArea").removeClass("d-none");
                $("#longtitudeArea").removeClass("d-none");
            });
        }

    });
</script>
@endsection