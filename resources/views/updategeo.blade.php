@extends('layouts.app')

@section('content')
<div class="row justify-content-center pt-4">
    <div class="col-md-6">
        <div class="text-center">
            <form id="geoJsonForm" enctype="multipart/form-data">
                @csrf
                <input type="file" name="geoJsonFile" id="geoJsonFile" accept=".json" class="form-control mb-3">
                <button type="button" id="uploadBtn" class="btn btn-primary">Upload</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script>
    $(document).ready(function() {
        $('#uploadBtn').on('click', function() {
            var formData = new FormData();
            var fileInput = $('#geoJsonFile')[0].files[0];
            formData.append('geoJsonFile', fileInput);

            var csrfToken = $('meta[name="csrf-token"]').attr('content');
            formData.append('_token', csrfToken);

            // Show loading indicator
            Swal.fire({
                title: 'Uploading...',
                allowOutsideClick: false,
                onBeforeOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: "{{ route('geoupdate') }}",
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    console.log('File uploaded successfully:', response);
                    Swal.fire({
                        icon: 'success',
                        title: 'Upload Successful!',
                        text: 'Data inserted successfully',
                    });
                },
                error: function(error) {
                    console.error('Error uploading file:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Upload Failed',
                        text: 'An error occurred while uploading the file',
                    });
                }
            });
        });
    });
</script>
@endsection