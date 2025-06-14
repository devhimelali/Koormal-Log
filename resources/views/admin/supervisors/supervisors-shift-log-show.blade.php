@extends('layouts.app')
@section('title', 'Supervisor Shift Log')
@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary-subtle pb-2 text-white">
            <h5 class="mb-0">Job Details – More Information</h5>
        </div>
        <div class="card-body">
            <form id="jobUpdateForm" enctype="multipart/form-data">
                <!-- Row 1 -->
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">WO Number</label>
                        <input type="text" name="wo_number" class="form-control" value="{{ $log->wo_number }}"
                                {{ $log->mark_as_complete ? 'disabled' : '' }}
                                {{ $log->is_excel_upload == 1 ? 'readonly' : '' }}>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Asset Number</label>
                        <input type="text" name="asset_no" class="form-control" value="{{ $log->asset_no }}"
                                {{ $log->is_excel_upload == 1 ? 'readonly' : '' }}>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Priority</label>
                        <input type="text" name="priority" class="form-control" value="{{ $log->priority }}"
                                {{ $log->is_excel_upload == 1 ? 'readonly' : '' }}>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Department</label>
                        <input type="text" name="department" class="form-control" value="{{ $log->department }}"
                                {{ $log->is_excel_upload == 1 ? 'readonly' : '' }}>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Duration (hrs)</label>
                        <input type="text" name="duration" class="form-control" value="{{ $log->duration }}"
                                {{ $log->is_excel_upload == 1 ? 'readonly' : '' }}>
                    </div>
                </div>

                <!-- Row 2 -->
                <div class="row g-3 mt-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Workorder Description</label>
                        <textarea class="form-control" name="work_description" rows="3"
                            {{ $log->is_excel_upload == 1 ? 'readonly' : '' }}>{{ $log->work_description }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Asset Description</label>
                        <textarea class="form-control" name="asset_description" rows="3"
                            {{ $log->is_excel_upload == 1 ? 'readonly' : '' }}>{{ $log->asset_description }}</textarea>
                    </div>
                </div>

                <!-- Notes -->
                <div class="mt-4">
                    <label class="form-label fw-semibold">Supervisor Notes</label>
                    <textarea class="form-control" name="supervisor_notes"
                              rows="4">{{ $log->supervisor_notes }}</textarea>
                </div>

                <div class="row d-flex align-items-stretch">
                    <!-- Upload Dropzone & Live Preview -->
                    <div class="mt-4 col-md-6">
                        <div class="h-100 d-flex flex-column">
                            <label class="form-label fw-semibold">Attach Images / Documents</label>
                            <div id="customDropzone"
                                 class="border border-2 border-dashed rounded text-center p-4 bg-light flex-grow-1"
                                 style="cursor: pointer;">
                                <div id="dropzonePlaceholder">
                                    <i class="bi bi-upload fs-1 text-muted"></i>
                                    <p class="text-muted mb-0">Click or drag files here to upload</p>
                                </div>
                                <input type="file" id="fileInput" name="attachments[]" multiple hidden>
                                <div id="filePreview" class="d-flex flex-wrap gap-3 mt-3 justify-content-start"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Existing Attachments -->
                    <div id="existingAttachments" class="mt-4 pt-2 col-md-6">
                        <div class="h-100 d-flex flex-column">
                            <label class="form-label fw-semibold">Existing Attachments</label>
                            <div class="d-flex flex-wrap gap-3 mt-2 justify-content-start flex-grow-1">
                                @foreach ($log->media as $media)
                                    <div class="attachment-item border rounded p-2 text-center position-relative"
                                         style="width: 100px; height: 120px;" data-id="{{ $media->id }}">

                                        <!-- Remove Button -->
                                        <span
                                                class="removeAttachment d-flex align-items-center justify-content-center
                                                position-absolute top-0 end-0 bg-white text-danger border rounded-circle shadow-sm"
                                                style="width: 24px; height: 24px; cursor: pointer; transform: translate(25%, -25%);"
                                                title="Remove">
                                            <i class='bx bx-trash fs-5 fw-bold'></i>
                                        </span>

                                        @php
                                            $url = asset($media->url);
                                            $ext = strtolower(pathinfo($media->url, PATHINFO_EXTENSION));
                                            $imageTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                                            $videoTypes = ['mp4', 'webm', 'ogg'];
                                        @endphp

                                        @if (in_array($ext, $imageTypes))
                                            {{-- Image with Lightbox and Download --}}
                                            <a href="{{ $url }}"
                                               class="glightbox"
                                               data-gallery="media-gallery"
                                               data-title='<a href="{{ $url }}" download class="btn btn-sm btn-light mt-2">⬇ Download Image</a>'>
                                                <img src="{{ $url }}" class="img-thumbnail" style="height: 100px;"
                                                     loading="lazy">
                                            </a>
                                        @elseif (in_array($ext, $videoTypes))
                                            {{-- Video with Lightbox and Download --}}
                                            <a href="{{ $url }}"
                                               class="glightbox"
                                               data-gallery="media-gallery"
                                               data-type="video"
                                               data-title='<a href="{{ $url }}" download class="btn btn-sm btn-light mt-2">⬇ Download Video</a>'>
                                                <video class="w-100 h-100 object-fit-cover" muted playsinline>
                                                    <source src="{{ $url }}" type="video/{{ $ext }}">
                                                    Your browser does not support the video tag.
                                                </video>
                                            </a>
                                        @else
                                            {{-- Non-media file (Download Only) --}}
                                            <a href="{{ $url }}" download target="_blank">
                                                <i class="bi bi-file-earmark-text fs-1 text-secondary"></i>
                                                <div class="small text-truncate">{{ basename($media->url) }}</div>
                                            </a>
                                        @endif

                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Buttons -->
                <div class="mt-4 d-flex flex-wrap gap-2">
                    @if ($log->mark_as_complete == 0)
                        {{-- <button type="button" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i> Mark As Completed
                        </button> --}}
                        <a href="{{ route('shift-logs.markComplete', $log->id) }}" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i> Mark As Completed
                        </a>
                    @else
                        {{-- <button type="button" class="btn btn-success">
                            <i class="bi bi-check-circle me-1"></i> Job Completed
                        </button> --}}
                        <a href="{{ route('shift-logs.markComplete', $log->id) }}" class="btn btn-success">
                            <i class="bi bi-check-circle me-1"></i> Job Completed
                        </a>
                    @endif
                    <button type="submit" class="btn btn-secondary ms-auto">
                        <i class="bi bi-save me-1"></i> Save and Return
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('page-script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
    <script>
        const lightbox = GLightbox({
            selector: '.glightbox'
        });
        implementAutoAjaxLoading();
        $(document).ready(function () {
            $('#jobUpdateForm').on('submit', function (e) {
                e.preventDefault();

                let form = $(this)[0];
                let formData = new FormData(form);

                $.ajax({
                    url: '{{ route('shift-logs.update-details', $log->id) }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-HTTP-Method-Override': 'PUT'
                    },
                    success: function (response) {
                        notify('success', response.message);
                        setTimeout(() => {
                            window.location.href =
                                `{{ route('supervisors-shift-log.index') }}?date=${response.date}`;
                        }, 1000);
                    },

                    error: function (xhr) {
                        notify('error', 'Failed to update job');
                    }
                });
            });
        });

        $(document).on('click', '.removeAttachment', function () {
            const wrapper = $(this).closest('.attachment-item');
            const mediaId = wrapper.data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: "This file will be permanently deleted.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ route('media.destroy', '__ID__') }}`.replace('__ID__', mediaId),
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE'
                        },
                        success: function () {
                            wrapper.remove();
                            Swal.fire(
                                'Deleted!',
                                'The attachment has been removed.',
                                'success'
                            );
                        },
                        error: function () {
                            Swal.fire(
                                'Error!',
                                'Failed to delete the attachment.',
                                'error'
                            );
                        }
                    });
                }
            });
        });


        const dropzone = document.getElementById('customDropzone');
        const fileInput = document.getElementById('fileInput');
        const previewContainer = document.getElementById('filePreview');
        const placeholder = document.getElementById('dropzonePlaceholder');

        let selectedFiles = [];

        dropzone.addEventListener('click', () => fileInput.click());

        dropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropzone.classList.add('bg-white', 'border-primary');
        });

        dropzone.addEventListener('dragleave', () => {
            dropzone.classList.remove('bg-white', 'border-primary');
        });

        dropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropzone.classList.remove('bg-white', 'border-primary');
            handleFiles(e.dataTransfer.files);
        });

        fileInput.addEventListener('change', () => {
            handleFiles(fileInput.files);
        });

        function handleFiles(files) {
            placeholder.style.display = 'none';

            Array.from(files).forEach(file => {
                // Store in memory
                selectedFiles.push(file);

                const index = selectedFiles.length - 1;
                const reader = new FileReader();
                const fileType = file.type;

                const wrapper = document.createElement('div');
                wrapper.classList.add('border', 'rounded', 'p-2', 'text-center', 'position-relative');
                wrapper.style.width = '100px';

                // Remove button
                const removeBtn = document.createElement('span');
                removeBtn.innerHTML = '&times;';
                removeBtn.classList.add('position-absolute', 'top-0', 'end-0', 'px-1', 'text-danger', 'fw-bold');
                removeBtn.style.cursor = 'pointer';
                removeBtn.title = 'Remove';
                removeBtn.onclick = () => {
                    selectedFiles.splice(index, 1); // Remove from memory
                    wrapper.remove(); // Remove from UI
                    if (selectedFiles.length === 0) placeholder.style.display = 'block';

                    // Reset fileInput so same file can be selected again
                    fileInput.value = '';
                };
                wrapper.appendChild(removeBtn);

                // Image preview
                if (fileType.startsWith('image/')) {
                    reader.onload = (e) => {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.classList.add('img-thumbnail');
                        img.style.height = '70px';
                        wrapper.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                } else {
                    const icon = document.createElement('i');
                    icon.className = 'bi bi-file-earmark-text fs-1 text-secondary';
                    wrapper.appendChild(icon);

                    const label = document.createElement('div');
                    label.innerText = file.name;
                    label.classList.add('small', 'text-truncate');
                    wrapper.appendChild(label);
                }

                previewContainer.appendChild(wrapper);
            });
        }
    </script>
@endsection
@section('page-style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">
    <style>
        .glightbox-clean .gslide-description
        {
            background: transparent !important;
        }
        .glightbox-clean .gdesc-inner {
             padding: 0 !important;
        }
    </style>
@endsection