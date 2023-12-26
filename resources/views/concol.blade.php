@extends('layouts.app') <!-- Assuming 'app' is the name of your main layout -->
@section('content')

<style>
    .box {
        border: 1px solid #ccc;
        padding: 10px;
        margin-bottom: 20px;
        max-height: 300px;
        overflow: auto;
        white-space: pre-wrap;
    }

    .highlight {
        color: #0366d6;
    }
</style>
<div class="row justify-content-center mt-10">
    <div class="col-md-6 ">
        <form id="geoJsonForm" enctype="multipart/form-data">
            @csrf
            <input type="file" name="geoJsonFile" id="geoJsonFile" accept=".json">
            <button type="button" id="uploadBtn">Convert</button>
        </form>
    </div>
</div>

<div class="row mt-4">

    <div class="box" id="uploadContent">
        <p>Uploaded JSON:</p>
    </div>


    <div class="box" id="formattedContent">
        <p>Formatted JSON Preview:</p>
    </div>

</div>

<div class="row mt-4">

    <div class="box" id="finalContent">
        <p>Final JSON Preview:</p>
    </div>

</div>


<script>
    document.getElementById('geoJsonFile').addEventListener('change', function() {
        const file = this.files[0];
        const reader = new FileReader();

        reader.onload = function(e) {
            const content = e.target.result;

            try {
                const jsonContent = JSON.parse(content);

                const uploadDiv = document.getElementById('uploadContent');
                uploadDiv.innerHTML = `<pre>${syntaxHighlight(content)}</pre>`;

                const formattedJson = convertJson(jsonContent);
                const formattedDiv = document.getElementById('formattedContent');
                formattedDiv.innerHTML = `<pre>${syntaxHighlight(JSON.stringify(formattedJson, null, 2))}</pre>`;

                const finalFormattedJson = finaljson(formattedJson);
                const finalDiv = document.getElementById('finalContent');
                finalDiv.innerHTML = `<pre>${syntaxHighlight(JSON.stringify(finalFormattedJson, null, 2))}</pre>`;

            } catch (error) {
                console.error("Invalid JSON file:", error);

                const uploadDiv = document.getElementById('uploadContent');
                uploadDiv.textContent = "Invalid JSON file!";
            }
        };

        reader.readAsText(file);
    });

    // Function to convert JSON (Implement your conversion logic here)
    function convertJson(jsonContent) {
        const features = jsonContent.features.map(feature => {
            const coordinates = feature.properties ? [
                [parseFloat(feature.properties.utm_x.replace(',', '.')), parseFloat(feature.properties.utm_y.replace(',', '.'))]
            ] : [];

            return {
                type: "Feature",
                properties: {
                    name: feature.properties ? feature.properties.block : null,
                },
                geometry: {
                    type: "Polygon",
                    coordinates: coordinates,
                },
            };
        });

        return {
            type: "FeatureCollection",
            features: features.filter(feature => feature.properties.name !== null),
        };
    }

    function finaljson(convertJson) {
        const groupedFeatures = {};

        convertJson.features.forEach(feature => {
            const name = feature.properties.name;

            if (!groupedFeatures[name]) {
                groupedFeatures[name] = {
                    type: "Feature",
                    properties: {
                        name: name
                    },
                    geometry: {
                        type: "Polygon",
                        coordinates: []
                    }
                };
            }

            groupedFeatures[name].geometry.coordinates.push(...feature.geometry.coordinates);
        });

        const mergedFeatures = Object.values(groupedFeatures).map(feature => {
            return {
                type: "Feature",
                properties: {
                    name: feature.properties.name
                },
                geometry: {
                    type: "Polygon",
                    coordinates: [
                        feature.geometry.coordinates
                    ]
                }
            };
        });

        const mergedFeatureCollection = {
            type: "FeatureCollection",
            features: mergedFeatures
        };

        return mergedFeatureCollection;
    }


    // Function to syntax-highlight JSON
    function syntaxHighlight(json) {
        // Add your syntax highlighting logic here
        return json;
    }

    document.getElementById('uploadBtn').addEventListener('click', function() {
        // Get the final formatted JSON content
        const finalDivContent = document.getElementById('finalContent').textContent;
        const finalFormattedJson = JSON.parse(finalDivContent);

        // Convert the JSON object to a string
        const finalJsonString = JSON.stringify(finalFormattedJson, null, 2);

        // Create a Blob object with the JSON data
        const blob = new Blob([finalJsonString], {
            type: 'application/json'
        });

        // Create a link element to trigger the download
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);

        // Set the file name for the downloaded JSON file
        link.download = 'final_formatted_json.json';

        // Trigger the download
        link.click();
    });
</script>

@endsection