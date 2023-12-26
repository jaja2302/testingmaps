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

    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand" href="#">Your Logo</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="/">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('uploadjson') }}">Update maps dari Edit</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('convert') }}">Convert geomaps Json</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#contact">Update maps dari geomaps</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

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