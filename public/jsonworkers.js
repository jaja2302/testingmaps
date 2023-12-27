let syntaxHighlight;

// Listen for messages from the main thread
self.addEventListener('message', function (e) {
    const { action, data } = e.data;

    if (action === 'init') {
        // Initialize the function passed from the main thread
        eval('syntaxHighlight = ' + data.syntaxHighlight);
    } else if (action === 'process') {
        const jsonContent = data.jsonContent;

        // Perform the heavy JSON processing here
        const finalFormattedJson = processJson(jsonContent);

        // Send the processed data back to the main thread
        self.postMessage({
            finalFormattedJson: finalFormattedJson
        });
    }
});

function processJson(jsonContent) {
    const features = jsonContent.features.map(feature => {
        const X = parseFloat(feature.properties.X.replace(',', '.'));
        const Y = parseFloat(feature.properties.Y.replace(',', '.'));

        const coordinates = feature.properties ? [
            [X, Y]
        ] : [];

        return {
            type: "Feature",
            properties: {
                blok: feature.properties ? feature.properties.block : null,
                afdeling: feature.properties ? feature.properties.afdeling : null,
                estate: feature.properties ? feature.properties.estate : null,
            },
            geometry: {
                type: "Polygon",
                coordinates: coordinates,
            },
        };
    });

    const convertedJson = {
        type: "FeatureCollection",
        features: features.filter(feature => feature.properties.block !== null),
    };

    const groupedFeatures = {};

    convertedJson.features.forEach(feature => {
        const block = feature.properties.blok;
        const estate = feature.properties.estate; // Define estate property
        const afdeling = feature.properties.afdeling; // Define afdeling property

        if (!groupedFeatures[block]) {
            groupedFeatures[block] = {
                type: "Feature",
                properties: {
                    block: block,
                    estate: estate, // Assign estate property
                    afdeling: afdeling, // Assign afdeling property
                },
                geometry: {
                    type: "Polygon",
                    coordinates: []
                }
            };
        }

        groupedFeatures[block].geometry.coordinates.push(...feature.geometry.coordinates);
    });

    const mergedFeatures = Object.values(groupedFeatures).map(feature => {
        return {
            type: "Feature",
            properties: {
                block: feature.properties.block, // Adjusted property name
                estate: feature.properties.estate, // Adjusted property name
                afdeling: feature.properties.afdeling, // Adjusted property name
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
