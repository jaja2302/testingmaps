<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman update nich</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="{{asset('jquery-3.7.1.min.js')}}"></script>
</head>

<body>

    <style>
        /* Add this CSS to center the container */
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            /* Adjust this to fit your layout */
        }
    </style>
    <div class="container">
        <form id="geoJsonForm" enctype="multipart/form-data">
            @csrf <!-- This will generate the CSRF token field -->
            <input type="file" name="geoJsonFile" id="geoJsonFile" accept=".json">
            <button type="button" id="uploadBtn">Upload GeoJSON</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#uploadBtn').on('click', function() {
                let formData = new FormData();
                let fileInput = $('#geoJsonFile')[0].files[0];
                formData.append('geoJsonFile', fileInput); // Corrected the file name

                // Retrieve CSRF token from the meta tag
                var _token = $('meta[name="csrf-token"]').attr('content');
                formData.append('_token', _token);

                $.ajax({
                    url: "{{ route('uploaddata') }}",
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        // Handle success response
                        console.log('File uploaded successfully:', response);
                    },
                    error: function(xhr, status, error) {
                        // Handle error
                        console.error('Error uploading file:', error);
                    }
                });
            });
        });
    </script>

    </script>

</body>

</html>