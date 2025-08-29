<select class="form-select form-select-sm status-select" data-todo-id="{{ $todo->id }}" data-old-status="{{ $todo->status }}" style="width: auto;">
    <option value="pending" {{ $todo->status === 'pending' ? 'selected' : '' }}>Bekliyor</option>
    <option value="in_progress" {{ $todo->status === 'in_progress' ? 'selected' : '' }}>Devam Ediyor</option>
    <option value="completed" {{ $todo->status === 'completed' ? 'selected' : '' }}>Tamamlandı</option>
</select>
