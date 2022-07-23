// @ts-nocheck

function initAutocomplete(mapBoxID, searchBoxID, addrField, latField, lngField, defLat = 23.885942, defLng = 45.079162) {
    var defLatLng = new google.maps.LatLng(defLat, defLng);
    geocoder = new google.maps.Geocoder();
    var map = new google.maps.Map(document.getElementById(mapBoxID), {
        center: defLatLng,
        zoom: 8.8,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        draggable: true,
    });

    // Create the search box and link it to the UI element.
    var input = document.getElementById(searchBoxID);
    var searchBox = new google.maps.places.SearchBox(input);
    map.controls[google.maps.ControlPosition.TOP_RIGHT].push(input);


    // Bias the SearchBox results towards current map's viewport.
    map.addListener('bounds_changed', function () {
        searchBox.setBounds(map.getBounds());
    });
    var marker = new google.maps.Marker({
        map: map,
        position: defLatLng,
        draggable: true,
    });
    google.maps.event.addListener(marker, 'drag', function () {
        updateMarkerPosition(marker.getPosition(), latField, lngField);
    });
    google.maps.event.addListener(marker, 'dragend', function () {
        geocodePosition(marker.getPosition(), addrField);
        map.panTo(marker.getPosition());
    });



    // Add circle overlay and bind to marker
    var circle = new google.maps.Circle({
        map: map,
        radius: 50 * 1000,    // In metres
        fillColor: '#ffc4c4'
    });
    circle.bindTo('center', marker, 'position');
    marker.circle = circle;


    // To add the marker to the map, call setMap();
    marker.setMap(map);
    // updateMarkerPosition(marker.getPosition(), defLat, defLng);
    var markers = [];
    // Listen for the event fired when the user selects a prediction and retrieve
    // more details for that place.
    searchBox.addListener('places_changed', function () {
        var places = searchBox.getPlaces();
        marker.setVisible(false);

        if (places.length == 0) {
            return;
        }


        marker.setMap(null);
        marker.setMap(map);
        if (marker.circle !== undefined) {
            marker.circle.setMap(null);
        }

        // Clear out the old markers.
        markers.forEach(function (marker) {
            marker.setMap(null);
        });
        markers = [];

        // For each place, get the icon, name and location.
        var bounds = new google.maps.LatLngBounds();
        places.forEach(function (place, index) {
            if (index > 0) return false;

            if (!place.geometry) {
                console.log("Returned place contains no geometry");
                return;
            }
            marker = new google.maps.Marker({
                map: map,
                title: place.name,
                position: place.geometry.location,
                draggable: true,
            });

            marker.setMap(null);
            marker.setMap(map);
            if (marker.circle !== undefined) {
                marker.circle.setMap(null);
            }

            // Add circle overlay and bind to marker
            var circle = new google.maps.Circle({
                map: map,
                radius: 50 * 1000,    // In metres
                fillColor: '#ffc4c4'
            });
            circle.bindTo('center', marker, 'position');
            marker.circle = circle;

            // Create a marker for each place.
            markers.push(marker);
            updateMarkerPosition(marker.getPosition(), latField, lngField);
            geocodePosition(marker.getPosition(), addrField);

            if (place.geometry.viewport) {
                // Only geocodes have viewport.
                bounds.union(place.geometry.viewport);
            } else {
                bounds.extend(place.geometry.location);
            }

            google.maps.event.addListener(marker, 'drag', function () {
                updateMarkerPosition(marker.getPosition(), latField, lngField);
            });

            google.maps.event.addListener(marker, 'dragend', function () {
                geocodePosition(marker.getPosition(), addrField);
                map.panTo(marker.getPosition());
            });

            google.maps.event.addListener(map, 'click', function (e) {
                updateMarkerPosition(e.latLng, latField, lngField);
                geocodePosition(marker.getPosition(), addrField);
                marker.setPosition(e.latLng);
                map.panTo(marker.getPosition());
            });
        });
        map.fitBounds(bounds);


    });
}

function geocodePosition(pos, addrField) {
    geocoder.geocode({
        latLng: pos
    }, function (responses) {
        if (responses && responses.length > 0) {
            updateMarkerAddress(responses[0].formatted_address, addrField);
        } else {
            updateMarkerAddress('', addrField);
        }
    });
}

function updateMarkerPosition(latLng, latField, lngField, searchBoxID) {
    $(`[name=${latField}]`).val(latLng.lat());
    $(`[name=${lngField}]`).val(latLng.lng());
}

function updateMarkerAddress(str, addrField) {
    $(`[name=${addrField}]`).val(str);
}