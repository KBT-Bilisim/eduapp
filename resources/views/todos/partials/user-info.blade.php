<div class="d-flex align-items-center">
    <div class="avatar avatar-sm me-3">
        <span class="avatar-initial rounded-circle bg-label-primary">{{ $userInitials }}</span>
    </div>
    <div class="d-flex flex-column">
        <span class="fw-medium">{{ $todo->user->name }}</span>
        <small class="text-muted">{{ $todo->user->email }}</small>
    </div>
</div>
