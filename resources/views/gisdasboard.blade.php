@extends('layouts.app') <!-- Assuming 'app' is the name of your main layout -->
@section('content')

<div class="row m-5">
    <div class="col-xl-5">
        <p>Pilih Estate</p>
    </div>

    <div>
        <select name="estate" id="estate">
            <option value="">Pilih Estate</option>
            @foreach ($list_options as $estate)
            <option value="{{$estate}}">{{$estate}}</option>
            @endforeach
        </select>

        <button class="btn btn-primary" id="button">Show</button>
        <button class="btn btn-primary" id="saveButton">Save Draw</button>
    </div>
</div>

<div class="row">
    <div id="map" style="height: 700px;width:1800px">
    </div>
</div>

<script>
    var group = L.layerGroup();
    var map = L.map('map', {
        editable: true
    }).setView([-2.2745234, 111.61404248], 11);

    var googleSatellite = L.tileLayer('http://{s}.google.com/vt?lyrs=s&x={x}&y={y}&z={z}', {
        maxZoom: 22,
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
    });

    var drawnItems = new L.FeatureGroup();
    map.addLayer(drawnItems);

    var drawControl = new L.Control.Draw({
        edit: {
            featureGroup: drawnItems,
            poly: {
                allowIntersection: false
            }
        },
        draw: {
            polygon: {
                allowIntersection: false,
                showArea: true
            }
        }
    });

    map.addControl(drawControl);
    var currentPolygon = null;

    googleSatellite.addTo(map);
    var markersLayer = L.layerGroup().addTo(map);

    $('#button').click(function() {
        var estate = $('#estate').val();
        if (!estate) {
            alert('Please select an estate first');
            return;
        }

        $.ajax({
            url: "{{ route('gis.getPlots') }}",
            method: 'get',
            data: {
                estate: estate
            },
            success: function(result) {
                drawPlots(result.plots);
            },
            error: function(xhr, status, error) {
                console.error('AJAX request failed');
                console.error(status + ': ' + error);
            }
        });
    });

    function drawPlots(plots) {
        markersLayer.clearLayers();
        drawnItems.clearLayers();
        let bounds = [];

        plots.forEach(function(plot) {
            let coordinates = plot.coordinates[0];

            if (coordinates && coordinates.length > 0) {
                currentPolygon = L.polygon(coordinates, {
                    color: 'blue',
                    fillOpacity: 0.5,
                    weight: 2,
                    smoothFactor: 1
                });

                currentPolygon.addTo(drawnItems);
                currentPolygon.enableEdit();

                coordinates.forEach(coord => {
                    bounds.push(coord);
                });

                coordinates.forEach(coord => {
                    L.circleMarker(coord, {
                        radius: 3,
                        color: 'white',
                        fillColor: 'blue',
                        fillOpacity: 1,
                        weight: 1
                    }).addTo(markersLayer);
                });
            }
        });

        if (bounds.length > 0) {
            map.fitBounds(bounds);
        }
    }

    $('#saveButton').click(function() {
        let est = $('#estate').val();
        if (!est) {
            alert('Please select an estate first');
            return;
        }

        if (!currentPolygon) {
            alert('No polygon to save');
            return;
        }

        // Get coordinates from the polygon
        let coordinates = currentPolygon.getLatLngs()[0].map(function(latLng) {
            return {
                "lat": parseFloat(latLng.lat),
                "lon": parseFloat(latLng.lng)
            };
        });

        // Validate coordinates
        if (!coordinates || coordinates.length < 3) {
            alert('Invalid polygon: need at least 3 points');
            return;
        }

        // Ensure polygon is closed
        if (coordinates.length > 0 &&
            (coordinates[0].lat !== coordinates[coordinates.length - 1].lat ||
                coordinates[0].lon !== coordinates[coordinates.length - 1].lon)) {
            coordinates.push(coordinates[0]);
        }

        // Show loading indicator
        $('#saveButton').prop('disabled', true).text('Saving...');

        $.ajax({
            url: "{{ route('gis.savePlots') }}",
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                est: est,
                coordinates: coordinates
            },
            success: function(response) {
                alert('Coordinates saved successfully');
                // Refresh the display
                $('#button').click();
            },
            error: function(xhr, status, error) {
                console.error('Save failed:', xhr.responseJSON);
                let errorMessage = 'Failed to save coordinates';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage += ': ' + xhr.responseJSON.error;
                }
                alert(errorMessage);
            },
            complete: function() {
                // Re-enable the save button
                $('#saveButton').prop('disabled', false).text('Save Draw');
            }
        });
    });

    // Handle newly drawn items
    map.on('draw:created', function(e) {
        drawnItems.clearLayers();
        currentPolygon = e.layer;
        drawnItems.addLayer(currentPolygon);
    });

    // Handle edited items
    map.on('draw:edited', function(e) {
        var layers = e.layers;
        layers.eachLayer(function(layer) {
            currentPolygon = layer;
        });
    });
</script>

@endsection