@php
    $label = request()->get('note_type') === 'day_shift' ? 'Supervisor Day Shift Notes' : 'Supervisor Night Shift Notes';
    $logDate = request()->get('log_date');
@endphp

@extends('layouts.app')
@section('title', $label)

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">{{ $label }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">{{ $label }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="py-3 text-center">
                        <img src="{{ asset('assets/logos/koormal-logo.png') }}" style="width: 180px;"
                             alt="Koormal Logo">
                    </div>
                    <div>
                        <h3 class="text-center">{{ $label }} {{ $logDate }}</h3>
                    </div>
                    <div class="py-3 text-center">
                        <img src="{{ asset('assets/logos/4emus-logo.png') }}" style="width: 180px;" alt="4EMUS Logo">
                    </div>
                </div>

                <hr class="m-0" style="color: #d1d9f3">

                <div class="card-body">
                    <form action="{{ route('supervisor-notes.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="note_type" value="{{ request()->get('note_type') }}">
                        <input type="hidden" name="log_date" value="{{ $logDate }}">

                        <div class="row">
                            <!-- Note -->
                            <div class="col-md-12 mb-3">
                                <label for="note" class="form-label">Note</label>
                                <textarea name="note" id="note" cols="30" rows="10"
                                          class="form-control @error('note') is-invalid @enderror">{{ old('note', $supervisor_notes?->note) }}</textarea>
                                @error('note')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>



                            <!-- Upload New Images -->
                            <div class="col-md-12 mb-3">
                                <label for="images" class="form-label">Upload Images</label>
                                <input type="file" name="images[]" id="images"
                                       class="form-control @error('images') is-invalid @enderror" multiple
                                       accept="image/*">
                                @error('images')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="row mt-2" id="image-preview"></div>
                            </div>

                            <!-- Existing Images -->
                            @if (!empty($supervisor_notes?->media) && count($supervisor_notes->media))
                                <div class="col-md-12 mb-3" id="existing-images-wrapper">
                                    <label class="form-label">Existing Images</label>
                                    <div class="row" id="existing-images-preview">
                                        @foreach ($supervisor_notes->media as $img)
                                            <div class="col-md-1 mb-2 position-relative image-wrapper" data-id="{{ $img->id }}">
                                                <img src="{{ asset($img->url) }}"
                                                     class="img-fluid rounded border"
                                                     style="height: 100px; object-fit: cover;">
                                                <button type="button"
                                                        class="btn btn-sm btn-danger position-absolute top-0 end-0 remove-existing-image"
                                                        data-id="{{ $img->id }}"
                                                        style="border-radius: 50%; padding: 0 6px;">×
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Buttons -->
                            <div class="col-md-12 mt-4 d-flex justify-content-between">
                                <button type="submit" class="btn btn-secondary">Save</button>
                                <a href="{{ route('supervisors-shift-log.index', ['date' => $logDate]) }}" class="btn btn-subtle-danger">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        $(document).ready(function () {
            let selectedFiles = [];

            $('#images').on('change', function () {
                selectedFiles = Array.from(this.files);
                renderPreview();
            });

            function renderPreview() {
                const preview = $('#image-preview');
                preview.empty();

                selectedFiles.forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const previewItem = $(`
                            <div class="col-md-1 position-relative mb-2" data-index="${index}">
                                <img src="${e.target.result}" class="img-fluid rounded border" style="height: 100px; object-fit: cover;">
                                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 remove-image" style="border-radius: 50%; padding: 0 6px;">×</button>
                            </div>
                        `);
                        preview.append(previewItem);
                    };
                    reader.readAsDataURL(file);
                });
            }

            $('#image-preview').on('click', '.remove-image', function () {
                const index = $(this).closest('[data-index]').data('index');
                selectedFiles.splice(index, 1);
                renderPreview();

                const dataTransfer = new DataTransfer();
                selectedFiles.forEach(file => dataTransfer.items.add(file));
                $('#images')[0].files = dataTransfer.files;
            });

            // Remove existing image via SweetAlert2
            $('#existing-images-preview').on('click', '.remove-existing-image', function () {
                const $button = $(this);
                const imageId = $button.data('id');

                Swal.fire({
                    title: 'Delete Image?',
                    text: 'This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('supervisor-notes.delete-image') }}",
                            method: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}",
                                image_id: imageId
                            },
                            success: function (response) {
                                if (response.status === 'success') {
                                    $(`.image-wrapper[data-id="${imageId}"]`).fadeOut(300, function () {
                                        $(this).remove();

                                        if ($('#existing-images-preview .image-wrapper').length === 0) {
                                            $('#existing-images-wrapper').slideUp(300, function () {
                                                $(this).remove();
                                            });
                                        }
                                    });
                                    Swal.fire('Deleted!', 'Image has been removed.', 'success');
                                } else {
                                    Swal.fire('Error!', 'Could not delete image.', 'error');
                                }
                            },
                            error: function () {
                                Swal.fire('Error!', 'Server error occurred.', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
