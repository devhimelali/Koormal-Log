<div class="modal fade" id="addOpportuneJobModal" tabindex="-1" aria-labelledby="addOpportuneJobModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary-subtle pb-3">
                <h1 class="modal-title fs-5" id="addOpportuneJobModalLabel">Add a job - Opportune work</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('store-shift-log-from-opportune-jobs') }}" method="POST"
                  id="addOpportuneJobForm">
                <div class="modal-body p-3 mb-3">
                    @csrf
                    <div class="mb-2">
                        <label for="shift_name" class="form-label">Shift <span class="text-danger">*</span></label>
                        <select class="form-select" name="shift_name" id="shift_name">
                            <option value="">Select a Shift</option>
                            <option value="day">Day shift</option>
                            <option value="night">Night Shift</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label for="job_id" class="form-label">Select a Job</label>
                        <select class="form-select" name="job_id" id="job_id">
                            <option value="">Select a Job</option>
                            @foreach ($opportuneJobs as $job)
                                <option value="{{ $job->id }}" data-tooltip="{{ $job->work_description }}">
                                    {{ $job->wo_number }} - {{ $job->asset_no }}
                                </option>
                            @endforeach
                        </select>
                        <div id="jobDetails" class="mt-2 text-muted"></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-subtle-danger" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-secondary" id="addOpportuneJobSubmitBtn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>