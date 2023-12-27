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



<script>
    $('#uploadBtn').click(function() {
        const file = $('#geoJsonFile')[0].files[0]; // Get the file from the input element
        const formData = new FormData();
        formData.append('file', file);
        formData.append('_token', $('input[name="_token"]').val());

        Swal.fire({
            title: 'Processing Data',
            text: 'Please wait...',
            allowOutsideClick: false,
            onBeforeOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: "{{ route('formatjson') }}",
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                // Handle the response from the server
                Swal.close(); // Close the loading animation

                // Trigger the download of the JSON file
                const data = JSON.stringify(response);

                const blob = new Blob([data], {
                    type: 'application/json'
                });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'formatted_data.json';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            },

            error: function(xhr, status, error) {
                // Handle errors in the AJAX request
                console.error('AJAX request failed');
                console.error(status + ': ' + error);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!'
                });
            }
        });
    });
</script>

@endsection