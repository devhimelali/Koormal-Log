@extends('layouts.app')
@section('title', 'Supervisor Shift Log')
@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary-subtle pb-2 text-white">
            <h5 class="mb-0">Job Details – More Information</h5>
        </div>
        <div class="card-body">
            <form>
                <!-- Row 1 -->
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">WO Number</label>
                        <input type="text" class="form-control" value="{{ $log->wo_number }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Asset Number</label>
                        <input type="text" class="form-control" value="{{ $log->asset_no }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Priority</label>
                        <input type="text" class="form-control" value="{{ $log->priority }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Department</label>
                        <input type="text" class="form-control" value="{{ $log->department }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Duration (hrs)</label>
                        <input type="text" class="form-control" value="{{ $log->duration }}">
                    </div>
                </div>

                <!-- Row 2 -->
                <div class="row g-3 mt-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Workorder Description</label>
                        <textarea class="form-control" rows="3">{{ $log->work_description }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Asset Description</label>
                        <textarea class="form-control" rows="3">{{ $log->asset_description }}</textarea>
                    </div>
                </div>

                <!-- Notes -->
                <div class="mt-4">
                    <label class="form-label fw-semibold">Supervisor Notes</label>
                    <textarea class="form-control" rows="4"></textarea>
                </div>

                <div class="mt-4">
                    <label class="form-label fw-semibold">Attach Images / Documents</label>
                    <div id="customDropzone" class="border border-2 border-dashed rounded text-center p-4 bg-light"
                        style="cursor: pointer;">
                        <div id="dropzonePlaceholder">
                            <i class="bi bi-upload fs-1 text-muted"></i>
                            <p class="text-muted mb-0">Click or drag files here to upload</p>
                        </div>
                        <input type="file" id="fileInput" name="attachments[]" multiple hidden>
                        <div id="filePreview" class="d-flex flex-wrap gap-3 mt-3 justify-content-start"></div>
                    </div>
                </div>


                <!-- Buttons -->
                <div class="mt-4 d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-success">
                        <i class="bi bi-check-circle me-1"></i> Job Completed
                    </button>
                    {{-- <button type="button" class="btn btn-outline-secondary">
                        <i class="bi bi-paperclip me-1"></i> Attach Image / Document
                    </button> --}}
                    <button type="submit" class="btn btn-primary ms-auto">
                        <i class="bi bi-save me-1"></i> Save and Return
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('page-script')
    <script>
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
