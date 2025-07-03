<div class="modal-header bg-primary-subtle py-3">
    <h1 class="modal-title fs-5" id="handoverCompletionModalLabel">Handover Completion Details</h1>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body p-3 mb-3">
    <div class="row mb-2">
        <div class="col-md-6">
            <p class="mb-0"><strong>Date:</strong> {{ $handoverCompletion->log_date }}</p>
            <p class="mb-0"><strong>Shift:</strong> {{ ucfirst($handoverCompletion->shift) }}</p>
        </div>
        <div class="col-md-6">
            <strong>Supervisor:</strong>
            {{ \App\Models\Supervisor::where('date', $handoverCompletion->log_date)->where('shift', $handoverCompletion->shift)->value('name') ?? 'N/A' }}
        </div>
    </div>
    <hr class="my-0">

    <h5 class="my-2">Answers</h5>
    <ul class="list-group">
        @foreach ($handoverCompletion->answers ?? [] as $question => $answer)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                {{ $question }}
                <span class="badge bg-{{ $answer === 'Yes' ? 'success' : 'danger' }}">
                    {{ $answer }}
                </span>
            </li>
        @endforeach
    </ul>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-subtle-danger" data-bs-dismiss="modal">Close</button>
</div>
