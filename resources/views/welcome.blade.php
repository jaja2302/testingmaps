<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="{{asset('Leatlef/leaflet.css')}}" />
    <!-- <link rel="shortcut icon" href="{{asset('img/CBI-logo.png')}}" type="image/x-icon"> -->
    <script src="{{asset('jquery-3.7.1.min.js')}}"></script>

</head>

<body class="antialiased">

    <div class="container">
        <div class="row m-5">
            <div class="col-xl-5">
                <p>Pilih est</p>
            </div>

            <div>
                <select name="estate" id="estate">
                    @foreach ($option as $item)
                    <option value="{{$item['id']}}">{{$item['est']}}</option>
                    @endforeach
                </select>

                <button class="button button-blue" id="button">Show</button>
            </div>


        </div>

        <div class="row">
            <div id="map" style="height: 700px;width:1800px">
            </div>
        </div>
    </div>

    <script src="{{asset('Leatlef/leaflet.js')}}"></script>

    <script>
        var group = L.layerGroup();

        // Initialize the map and set its view
        var map = L.map('map', {
            editable: true // Enable editing
        }).setView([-2.2745234, 111.61404248], 11);

        // Define the "Google Satellite" tile layer
        var googleSatellite = L.tileLayer('http://{s}.google.com/vt?lyrs=s&x={x}&y={y}&z={z}', {
            maxZoom: 22, // Increase the maxZoom value to 22 or any desired value
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
        });

        // Add "Google Satellite" as the only base map
        googleSatellite.addTo(map);

        var markersLayer = L.layerGroup().addTo(map); // Initialize markersLayer as a layer group and add it to the map

        $('#button').click(function() {
            // Your code to execute when the button is clicked goes here
            var estate = $('#estate').val();

            console.log(estate);

            $.ajax({
                url: "{{ route('drawmaps') }}",
                method: 'get', // Specify the HTTP method (POST, GET, etc.)
                data: {
                    estate: estate
                }, // Data to be sent to the server
                success: function(result) {
                    var plot = JSON.parse(result);

                    const plotResult = Object.entries(plot['plot']);
                    drawArrow(plotResult)
                },
                error: function(xhr, status, error) {
                    // Handle errors in the AJAX request
                    console.error('AJAX request failed');
                    console.error(status + ': ' + error);
                }
            });
        });

        function drawArrow(plotResult) {
            console.log(plotResult);
            markersLayer.clearLayers(); // Clear the layers within markersLayer

            let bounds = []; // Initialize an empty LatLngBounds object

            const latLngArray = plotResult.map((item) => {
                const latLngString = item[1].latln;
                const coordinates = latLngString.match(/\[(.*?)\]/g);
                if (coordinates) {
                    return coordinates.map((coord) => {
                        const [longitude, latitude] = coord
                            .replace('[', '')
                            .replace(']', '')
                            .split(',')
                            .map(parseFloat);
                        return [latitude, longitude]; // Reversed to follow [lat, lon] format
                    });
                }
                return [];
            });

            latLngArray.forEach((coordinates, index) => {
                const polygonCoords = coordinates.map(([lat, lon]) => [lat, lon]);
                const name = plotResult[index][0]; // Get the name from plotResult at the corresponding index

                if (polygonCoords.length > 2) { // Only draw polygons with at least 3 points
                    const polygon = L.polygon(polygonCoords, {
                        color: 'red',
                        weight: 2
                    }).addTo(markersLayer).bindPopup(name); // Bind popup with the name

                    // Extend the bounds with the polygon's coordinates
                    bounds.push(...polygon.getLatLngs());
                }
            });

            // Create a LatLngBounds object and fit the map to its bounds
            if (bounds.length > 0) {
                map.fitBounds(L.latLngBounds(bounds));
            }
        }
    </script>

</body>

</html>