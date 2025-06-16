@php
    $label = request()->get('note_type') == 'day_shift' ? 'Supervisor Day Shift Notes' : 'Supervisor Night Shift Notes';
@endphp
@extends('layouts.app')
@section('title', $label)
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">
                    {{ $label }}
                </h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{route('admin.dashboard')}}">
                                Dashboard
                            </a>
                        </li>
                        <li class="breadcrumb-item active">
                            {{ $label }}
                        </li>
                    </ol>
                </div>

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-2">Create {{ $label }}</h4>
                </div>
                <hr class="m-0" style="color: #d1d9f3">
                <div class="card-body">
                    <form action="{{route('supervisor-notes.store')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <!-- Shift Log -->
                            <div class="col-md-6 mb-3">
                                <label for="shift_log_id" class="form-label">Shift Log</label>
                                <select name="shift_log_id" id="shift_log_id"
                                        class="form-control {{$errors->has('shift_log_id') ? 'is-invalid' : ''}}">
                                    <option value="">Select Shift Log</option>
                                    @foreach($shiftLogs as $shiftLog)
                                        <option {{old('shift_log_id') == $shiftLog->id ? 'selected' : ''}} value="{{ $shiftLog->id }}">{{ $shiftLog->wo_number }}
                                            - {{ $shiftLog->asset_no }} - {{ $shiftLog->duration }} hours
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">
                                    @error('shift_log_id')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Note Type -->
                            <div class="col-md-6 mb-3">
                                <label for="note_type" class="form-label">Note Type</label>
                                <input type="hidden" name="note_type" id="note_type" readonly class="form-control"
                                       value="{{ request()->get('note_type') }}">
                                <input type="text" readonly class="form-control"
                                       value="{{ request()->get('note_type') == 'day_shift' ? 'Day Shift' : 'Night Shift' }}">
                            </div>

                            <!-- Note -->
                            <div class="col-md-12 mb-3">
                                <label for="note" class="form-label">Note</label>
                                <textarea name="note" id="note" cols="30" rows="10"
                                          class="form-control {{$errors->has('note') ? 'is-invalid' : ''}}">{{old('note')}}</textarea>
                                <div class="invalid-feedback">
                                    @error('note')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Image Upload -->
                            <div class="col-md-12 mb-3">
                                <label for="images" class="form-label">Upload Images</label>
                                <input type="file" name="images[]" id="images"
                                       class="form-control {{$errors->has('images') ? 'is-invalid' : ''}}" multiple
                                       accept="image/*">
                                <div class="invalid-feedback">
                                    @error('images')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="row mt-2" id="image-preview"></div>
                            </div>

                            <!-- Submit Button -->
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-secondary">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('page-script')
    <script src="{{asset('assets/libs/select2/select2.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            $('#shift_log_id').select2();

            let selectedFiles = [];

            $('#images').on('change', function () {
                selectedFiles = Array.from(this.files); // clone file list

                renderPreview();
            });

            function renderPreview() {
                const preview = $('#image-preview');
                preview.empty();

                selectedFiles.forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const previewItem = $(`
                    <div class="col-md-2 position-relative mb-2" data-index="${index}">
                        <img src="${e.target.result}" class="img-fluid rounded border" style="height: 100px; object-fit: cover;">
                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 remove-image" style="border-radius: 50%; padding: 0 6px;">×</button>
                    </div>
                `);
                        preview.append(previewItem);
                    };
                    reader.readAsDataURL(file);
                });
            }

            // Remove preview and update file list
            $('#image-preview').on('click', '.remove-image', function () {
                const index = $(this).closest('[data-index]').data('index');

                selectedFiles.splice(index, 1); // remove from array
                renderPreview(); // re-render preview with updated list

                // Clear and repopulate input field (if using form submit)
                const dataTransfer = new DataTransfer();
                selectedFiles.forEach(file => dataTransfer.items.add(file));
                $('#images')[0].files = dataTransfer.files;
            });
        });
    </script>
@endsection
@section('page-style')
    <link rel="stylesheet" href="{{asset('assets/libs/select2/select2.min.css')}}">
    <style>
        span.select2-selection.select2-selection--single {
            height: 40px;
            padding-top: 5px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            top: 6px !important;
        }
    </style>
@endsection