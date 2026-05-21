<div class="card mb-3">
    <div class="card-body">
        <strong>Submission Code:</strong>
        {{ $visit->submission->procurement_submission_code }} <br>

        <strong>Status:</strong>
        <span class="badge bg-secondary">{{ ucfirst($visit->status) }}</span>
    </div>
</div>
