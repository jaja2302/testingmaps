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

    <style>
        /* Custom styling for the text label */
        .text-label {
            color: white;
            /* Set text color to white */
            font-size: 10px;
            /* Set font size to 45px */
            text-align: center;
            /* Center the text */
            /* You can add more styles as needed */
        }
    </style>
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

                <button class="btn btn-primary" id="button">Show</button>
                <a href="{{ route('uploadjson') }}" class="btn btn-outline-success">Update</a>
                <a href="{{ route('convert') }}" class="btn btn-outline-success">Convert json</a>
            </div>


        </div>

        <div class="row">
            <div id="map" style="height: 700px;width:1800px">
            </div>
        </div>
    </div>



    <script src="{{asset('Leatlef/leaflet.js')}}"></script>
    <script src="{{asset('Leatlef/Leaflet.Editable.js')}}"></script>

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
        var markersLayer2 = L.layerGroup().addTo(map); // Initialize markersLayer as a layer group and add it to the map

        $('#button').click(function() {
            // Your code to execute when the button is clicked goes here
            var estate = $('#estate').val();

            // console.log(estate);
            var _token = $('input[name="_token"]').val();

            $.ajax({
                url: "{{ route('drawmaps') }}",
                method: 'get', // Specify the HTTP method (POST, GET, etc.)
                data: {
                    estate: estate,
                    _token: _token
                }, // Data to be sent to the server
                success: function(result) {
                    var plot = JSON.parse(result);

                    const plotResult = Object.entries(plot['plot']);
                    const plotest = Object.entries(plot['plot_estate']);
                    drawBlok(plotResult)
                    drawPlot(plotest)

                },
                error: function(xhr, status, error) {
                    // Handle errors in the AJAX request
                    console.error('AJAX request failed');
                    console.error(status + ': ' + error);
                }
            });
        });


        function drawPlot(plotest) {
            // console.log(plotest);
            markersLayer2.clearLayers(); // Assuming markersLayer2 is the layer to contain the polygons

            let bounds = []; // Initialize an empty LatLngBounds object

            // Iterate through the plotest array using a for loop
            for (let i = 0; i < plotest.length; i++) {
                let polygonName = plotest[i][0]; // Get the name of the polygon (e.g., "R010")
                let coordinates = []; // Array to store polygon coordinates

                let polygonCoords = plotest[i][1]; // Array containing objects with lat lon info

                // Iterate through the polygonCoords array using a for loop
                for (let j = 0; j < polygonCoords.length; j++) {
                    let lat = polygonCoords[j]['lat'];
                    let lon = polygonCoords[j]['lon'];
                    let est = polygonCoords[j]['est'];

                    if (typeof lat !== 'undefined' && typeof lon !== 'undefined') {
                        coordinates.push([lat, lon]); // Push coordinates as an array
                        bounds.push([lat, lon]); // Extend bounds with each coordinate
                    }
                }


                // Create a Leaflet polygon if coordinates exist and add it to the markersLayer2
                if (coordinates.length > 0) {

                    // let polygon = L.polygon(coordinates, {
                    //     color: 'blue'
                    // }).addTo(markersLayer2).bindPopup(polygonName);

                    let polygon = L.polygon(coordinates, {
                        color: 'black'
                    }).addTo(markersLayer2)


                    // Add event listener for vertex drag end
                    polygon.on('editable:vertex:dragend', function(e) {
                        console.log('Edited polygon:', e.target.getLatLngs());
                        // e.target.getLatLngs() will give you the updated coordinates after drag
                        // You can further process these coordinates as needed
                    });

                    polygon.enableEdit();
                }
            }

            // Create a LatLngBounds object and fit the map to its bounds
            if (bounds.length > 0) {
                map.fitBounds(bounds);
            }

            let saveButton = document.createElement('button');
            saveButton.textContent = 'Save Changes';
            saveButton.style.margin = '10px';
            document.body.appendChild(saveButton);

            saveButton.addEventListener('click', function() {
                let updatedCoordinates = [];

                // Iterate through polygons to retrieve updated coordinates
                markersLayer2.eachLayer(function(layer) {
                    let coords = layer.getLatLngs()[0]; // Get updated coordinates of the polygon
                    let polygonData = [];

                    coords.forEach(coord => {
                        let {
                            lat,
                            lng: lon
                        } = coord; // Destructure 'lng' as 'lon'
                        let est = 'SJE'; // Hardcoded value for 'est'

                        polygonData.push({
                            lat,
                            lon,
                            est
                        });
                    });

                    updatedCoordinates.push(polygonData);
                });

                // Convert to JSON
                let jsonData = JSON.stringify(updatedCoordinates, null, 2);

                // Create a Blob and download as a text file
                let blob = new Blob([jsonData], {
                    type: 'application/json'
                });
                let link = document.createElement('a');
                link.download = 'edited_coordinates.json';
                link.href = URL.createObjectURL(blob);
                link.click();
            });



        }


        function drawBlok(plotResult) {
            // console.log(plotResult);
            markersLayer.clearLayers(); // Assuming markersLayer is the layer to contain the polygons

            let bounds = []; // Initialize an empty LatLngBounds object
            let editedPolygons = new Set(); // Declare editedPolygons before the loop
            let afdelingMap = {};

            // Iterate through the plotResult array using a for loop
            for (let i = 0; i < plotResult.length; i++) {
                let polygonName = plotResult[i][0]; // Get the name of the polygon (e.g., "R010")
                let coordinates = []; // Array to store polygon coordinates

                // console.log(polygonName);
                let polygonCoords = plotResult[i][1]; // Array containing objects with lat lon info

                // Iterate through the polygonCoords array using a for loop
                for (let j = 0; j < polygonCoords.length; j++) {
                    let lat = polygonCoords[j]['lat'];
                    let lon = polygonCoords[j]['lon'];
                    let afdeling = polygonCoords[j]['afdeling'];

                    // console.log(afdeling);

                    if (typeof lat !== 'undefined' && typeof lon !== 'undefined') {
                        coordinates.push([lat, lon]); // Push coordinates as an array
                        bounds.push([lat, lon]); // Extend bounds with each coordinate
                        afdelingMap[polygonName] = afdeling;
                    }
                }

                let randomColor = '#' + Math.floor(Math.random() * 16777215).toString(16); // Generate a random color

                // Create a Leaflet polygon if coordinates exist and add it to the markersLayer
                if (coordinates.length > 0) {
                    let polygon = L.polygon(coordinates, {
                        color: randomColor,
                        fillOpacity: 1.0 // Set fill opacity to 100% (1.0)
                    }).addTo(markersLayer).bindPopup(polygonName);
                    let polygonCenter = L.latLng(L.polygon(coordinates).getBounds().getCenter());


                    let customIcon = L.divIcon({
                        className: 'text-label', // Custom CSS class for the label
                        html: `<div>${polygonName}</div>`, // Text content of the label
                        iconSize: [0, 0] // Set iconSize to zero to prevent the default icon from appearing
                    });
                    let labelMarker = L.marker(polygonCenter, {
                        icon: customIcon,
                        zIndexOffset: 1000 // Set a higher zIndex to ensure the label appears above the polygon
                    }).addTo(markersLayer);

                    polygon.on('editable:vertex:dragend', function(e) {
                        let editedCoords = e.target.getLatLngs()[0]; // Get updated coordinates
                        editedPolygons.add(polygonName); // Add the edited polygon's name to the set

                        let afdeling = afdelingMap[polygonName];


                        // Log polygon information along with coordinates and afdeling in JSON-like format
                        let polygonData = {
                            name: polygonName,
                            afdeling: afdeling,
                            coordinates: []
                        };

                        editedCoords.forEach(coord => {
                            polygonData.coordinates.push({
                                lat: coord.lat,
                                lon: coord.lng
                            });
                        });

                        console.log(JSON.stringify(polygonData, null, 2));
                    });



                    // polygon.enableEdit();
                }
            }

            // let saveButton = document.createElement('button');
            // saveButton.textContent = 'Save Changes';
            // saveButton.style.margin = '10px';
            // document.body.appendChild(saveButton);

            // saveButton.addEventListener('click', function() {
            //     let updatedCoordinates = [];

            //     markersLayer.eachLayer(function(layer) {
            //         // Check if the layer has a popup
            //         if (layer.getPopup()) {
            //             let polygonName = layer.getPopup().getContent();

            //             if (editedPolygons.has(polygonName)) {
            //                 let coords = layer.getLatLngs()[0];
            //                 let afdeling = afdelingMap[polygonName]; // Retrieve afdeling from the map

            //                 coords.forEach(coord => {
            //                     let {
            //                         lat,
            //                         lng: lon
            //                     } = coord;

            //                     let coordinateData = {
            //                         name: polygonName,
            //                         afdeling: afdeling,
            //                         lat,
            //                         lon
            //                     };

            //                     updatedCoordinates.push(coordinateData);
            //                 });
            //             }
            //         }
            //     });

            //     let jsonData = JSON.stringify(updatedCoordinates, null, 2);

            //     let blob = new Blob([jsonData], {
            //         type: 'application/json'
            //     });
            //     let link = document.createElement('a');
            //     link.download = 'edited_coordinates.json';
            //     link.href = URL.createObjectURL(blob);
            //     link.click();
            // });

            // Create a LatLngBounds object and fit the map to its bounds
            if (bounds.length > 0) {
                map.fitBounds(bounds);
            }
        }
    </script>

</body>

</html>