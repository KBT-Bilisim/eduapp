<div class="d-flex align-items-center">
    <button class="btn btn-text-secondary rounded-pill btn-icon edit-todo-btn" 
            data-todo-id="{{ $todo->id }}" 
            data-bs-toggle="offcanvas" 
            data-bs-target="#offcanvasEditTodo">
        <i class="icon-base ti tabler-edit icon-22px"></i>
    </button>
    <button class="btn btn-text-secondary rounded-pill btn-icon delete-todo-btn" 
            data-todo-id="{{ $todo->id }}">
        <i class="icon-base ti tabler-trash icon-22px"></i>
    </button>
</div>
