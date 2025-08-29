<div class="d-flex flex-column">
    <span class="fw-medium text-heading">{{ $todo->title }}</span>
    @if($todo->description)
        <small class="text-muted">{{ Str::limit($todo->description, 50) }}</small>
    @endif
</div>
