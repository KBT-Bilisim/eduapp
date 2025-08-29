@extends('layouts.master')

@section('title', 'Todo Yönetimi - KBTS ARJ Sistem')
@push('styles')
    <link rel="stylesheet" href="/vuexy/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="/vuexy/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="/vuexy/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="/vuexy/assets/vendor/libs/select2/select2.css" />
    <link rel="stylesheet" href="/vuexy/assets/vendor/libs/@form-validation/form-validation.css" />
    <link rel="stylesheet" href="/vuexy/assets/vendor/libs/flatpickr/flatpickr.css" />
    <link rel="stylesheet" href="/vuexy/assets/vendor/libs/sweetalert2/sweetalert2.css" />
@endpush

@section('content')
<!-- Flash Messages -->
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Başarılı!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Hata!</strong> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Todo Stats Cards -->
<div class="row g-6 mb-6">
    <div class="col-lg-3 col-md-6">
        <div class="card h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div class="card-title mb-0">
                    <h5 class="mb-1 me-2" id="pendingTodos">{{ $todoStats['pending'] }}</h5>
                    <p class="text-body mb-0">Bekleyen</p>
                </div>
                <div class="card-icon">
                    <span class="badge bg-label-warning rounded-pill p-2">
                        <i class="icon-base ti tabler-clock icon-22px"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div class="card-title mb-0">
                    <h5 class="mb-1 me-2" id="inProgressTodos">{{ $todoStats['in_progress'] }}</h5>
                    <p class="text-body mb-0">Devam Ediyor</p>
                </div>
                <div class="card-icon">
                    <span class="badge bg-label-primary rounded-pill p-2">
                        <i class="icon-base ti tabler-player-play icon-22px"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div class="card-title mb-0">
                    <h5 class="mb-1 me-2" id="completedTodos">{{ $todoStats['completed'] }}</h5>
                    <p class="text-body mb-0">Tamamlanan</p>
                </div>
                <div class="card-icon">
                    <span class="badge bg-label-success rounded-pill p-2">
                        <i class="icon-base ti tabler-check icon-22px"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div class="card-title mb-0">
                    <h5 class="mb-1 me-2" id="totalTodos">{{ $todoStats['total'] }}</h5>
                    <p class="text-body mb-0">Toplam</p>
                </div>
                <div class="card-icon">
                    <span class="badge bg-label-info rounded-pill p-2">
                        <i class="icon-base ti tabler-list-check icon-22px"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Todos List Table -->
<div class="card">
    <div class="card-header border-bottom d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Todo Listesi</h5>
        <button class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddTodo">
            <i class="icon-base ti tabler-plus me-1"></i>Yeni Todo
        </button>
    </div>
    <div class="card-datatable table-responsive">
        <table class="datatables-todos table" id="todos-table">
            <thead class="table-light">
                <tr>
                    <th></th>
                    <th>Başlık</th>
                    <th>Öncelik</th>
                    <th>Durum</th>
                    <th>Atanan</th>
                    <th>Son Tarih</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Add Todo Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddTodo" aria-labelledby="offcanvasAddTodoLabel">
    <div class="offcanvas-header">
        <h5 id="offcanvasAddTodoLabel" class="offcanvas-title">Yeni Todo Ekle</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0 h-100">
        <form class="add-new-todo pt-0" id="addTodoForm">
            @csrf
            <div class="form-floating form-floating-outline mb-6">
                <input type="text" class="form-control" id="add-todo-title" placeholder="Todo başlığı" name="title" required />
                <label for="add-todo-title">Başlık</label>
            </div>
            <div class="form-floating form-floating-outline mb-6">
                <textarea class="form-control" id="add-todo-description" placeholder="Todo açıklaması" name="description" rows="3"></textarea>
                <label for="add-todo-description">Açıklama</label>
            </div>
            <div class="form-floating form-floating-outline mb-6">
                <select id="add-todo-priority" class="form-select" name="priority" required>
                    <option value="low">Düşük</option>
                    <option value="medium" selected>Orta</option>
                    <option value="high">Yüksek</option>
                </select>
                <label for="add-todo-priority">Öncelik</label>
            </div>
            <div class="form-floating form-floating-outline mb-6">
                <select id="add-todo-status" class="form-select" name="status" required>
                    <option value="pending" selected>Bekliyor</option>
                    <option value="in_progress">Devam Ediyor</option>
                    <option value="completed">Tamamlandı</option>
                </select>
                <label for="add-todo-status">Durum</label>
            </div>
            <div class="form-floating form-floating-outline mb-6">
                <input type="date" class="form-control" id="add-todo-due-date" name="due_date" />
                <label for="add-todo-due-date">Son Tarih</label>
            </div>
            <button type="submit" class="btn btn-primary me-3 data-submit">Kaydet</button>
            <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">İptal</button>
        </form>
    </div>
</div>

<!-- Edit Todo Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEditTodo" aria-labelledby="offcanvasEditTodoLabel">
    <div class="offcanvas-header">
        <h5 id="offcanvasEditTodoLabel" class="offcanvas-title">Todo Düzenle</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0 h-100">
        <form class="edit-todo pt-0" id="editTodoForm">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit-todo-id" name="todo_id">
            <div class="form-floating form-floating-outline mb-6">
                <input type="text" class="form-control" id="edit-todo-title" placeholder="Todo başlığı" name="title" required />
                <label for="edit-todo-title">Başlık</label>
            </div>
            <div class="form-floating form-floating-outline mb-6">
                <textarea class="form-control" id="edit-todo-description" placeholder="Todo açıklaması" name="description" rows="3"></textarea>
                <label for="edit-todo-description">Açıklama</label>
            </div>
            <div class="form-floating form-floating-outline mb-6">
                <select id="edit-todo-priority" class="form-select" name="priority" required>
                    <option value="low">Düşük</option>
                    <option value="medium">Orta</option>
                    <option value="high">Yüksek</option>
                </select>
                <label for="edit-todo-priority">Öncelik</label>
            </div>
            <div class="form-floating form-floating-outline mb-6">
                <select id="edit-todo-status" class="form-select" name="status" required>
                    <option value="pending">Bekliyor</option>
                    <option value="in_progress">Devam Ediyor</option>
                    <option value="completed">Tamamlandı</option>
                </select>
                <label for="edit-todo-status">Durum</label>
            </div>
            <div class="form-floating form-floating-outline mb-6">
                <input type="date" class="form-control" id="edit-todo-due-date" name="due_date" />
                <label for="edit-todo-due-date">Son Tarih</label>
            </div>
            <button type="submit" class="btn btn-primary me-3 data-submit">Güncelle</button>
            <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">İptal</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="/vuexy/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
<script src="/vuexy/assets/vendor/libs/flatpickr/flatpickr.js"></script>
<script>
$(document).ready(function() {
    
    // =====================
    // DATATABLE INITIALIZATION
    // =====================
    
    const dt_todos = $('#todos-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: '{{ route("todos.datatable") }}',
            type: 'GET'
        },
        columns: [
            { data: 'empty', orderable: false, searchable: false, responsivePriority: 3 },
            { data: 'title_description', name: 'title' },
            { data: 'priority', name: 'priority' },
            { data: 'status', name: 'status', orderable: false },
            { data: 'user', name: 'user.name' },
            { data: 'due_date', name: 'due_date' },
            { data: 'actions', orderable: false, searchable: false, responsivePriority: 1 }
        ],
        order: [[1, 'desc']],
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        language: {
            search: 'Ara:',
            lengthMenu: '_MENU_ kayıt göster',
            info: '_TOTAL_ kayıttan _START_ - _END_ arası gösteriliyor',
            infoEmpty: 'Kayıt bulunamadı',
            infoFiltered: '(_MAX_ kayıt içinden filtrelendi)',
            processing: 'Yükleniyor...',
            zeroRecords: 'Eşleşen kayıt bulunamadı',
            paginate: {
                first: 'İlk',
                last: 'Son',
                next: 'Sonraki',
                previous: 'Önceki'
            }
        },
        drawCallback: function() {
            // Her tablo yenilendikten sonra status select'lere event listener ekle
            initStatusSelects();
        }
    });

    // =====================
    // UTILITY FUNCTIONS
    // =====================
    
    function updateStatistics() {
        console.log('İstatistikler güncelleniyor...');
        
        $.ajax({
            url: '{{ route("todos.statistics") }}',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                console.log('İstatistik verileri alındı:', data);
                
                $('#totalTodos').text(data.total || 0);
                $('#pendingTodos').text(data.pending || 0);
                $('#inProgressTodos').text(data.in_progress || 0);
                $('#completedTodos').text(data.completed || 0);
                
                console.log('İstatistikler başarıyla güncellendi');
            },
            error: function(xhr, status, error) {
                console.error('İstatistik güncelleme hatası:', error);
            }
        });
    }

    function handleFormError(xhr) {
        if (xhr.status === 422) {
            const errors = xhr.responseJSON.errors;
            let errorMessage = 'Lütfen formu doğru şekilde doldurun:\n';
            $.each(errors, function(key, value) {
                errorMessage += '- ' + value[0] + '\n';
            });
            showError(errorMessage);
        } else {
            showError('Bir hata oluştu. Lütfen tekrar deneyin.');
        }
    }

    function initStatusSelects() {
        $('.status-select').each(function() {
            $(this).data('old-status', $(this).val());
        });
    }

    // =====================
    // FORM EVENT HANDLERS
    // =====================
    
    $('#addTodoForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize();
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.text();
        
        submitBtn.prop('disabled', true).text('Kaydediliyor...');
        
        $.ajax({
            url: '{{ route("todos.store") }}',
            method: 'POST',
            data: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(response) {
                showSuccess(response.message);
                $('#addTodoForm')[0].reset();
                $('#offcanvasAddTodo').offcanvas('hide');
                
                // Tabloyu yenile ve istatistikleri güncelle
                dt_todos.ajax.reload();
                updateStatistics();
            },
            error: handleFormError,
            complete: function() {
                submitBtn.prop('disabled', false).text(originalText);
            }
        });
    });

    $(document).on('click', '.edit-todo-btn', function() {
        const todoId = $(this).data('todo-id');
        
        $.ajax({
            url: '/todos/' + todoId,
            method: 'GET',
            success: function(todo) {
                $('#edit-todo-id').val(todo.id);
                $('#edit-todo-title').val(todo.title);
                $('#edit-todo-description').val(todo.description);
                $('#edit-todo-priority').val(todo.priority);
                $('#edit-todo-status').val(todo.status);
                $('#edit-todo-due-date').val(todo.due_date);
            },
            error: function() {
                showError('Todo bilgileri yüklenemedi.');
            }
        });
    });

    $('#editTodoForm').on('submit', function(e) {
        e.preventDefault();
        
        const todoId = $('#edit-todo-id').val();
        const formData = $(this).serialize();
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.text();
        
        submitBtn.prop('disabled', true).text('Güncelleniyor...');
        
        $.ajax({
            url: '/todos/' + todoId,
            method: 'PUT',
            data: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(response) {
                showSuccess(response.message);
                $('#offcanvasEditTodo').offcanvas('hide');
                
                // Tabloyu yenile ve istatistikleri güncelle
                dt_todos.ajax.reload();
                updateStatistics();
            },
            error: handleFormError,
            complete: function() {
                submitBtn.prop('disabled', false).text(originalText);
            }
        });
    });

    // =====================
    // STATUS & DELETE HANDLERS  
    // =====================
    
    $(document).on('change', '.status-select', function() {
        const $this = $(this);
        const todoId = $this.data('todo-id');
        const newStatus = $this.val();
        const oldStatus = $this.data('old-status');
        
        $this.data('old-status', newStatus);
        
        $.ajax({
            url: '{{ route("todos.update-status", ":id") }}'.replace(':id', todoId),
            method: 'PATCH',
            data: {
                status: newStatus,
                _token: '{{ csrf_token() }}'
            },
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(response) {
                if (response.success) {
                    showSuccess('Durum başarıyla güncellendi.');
                    updateStatistics();
                } else {
                    showError('Durum güncellenemedi.');
                    $this.val(oldStatus);
                }
            },
            error: function() {
                showError('Durum güncellenirken hata oluştu.');
                $this.val(oldStatus);
            }
        });
    });

    $(document).on('click', '.delete-todo-btn', function() {
        const todoId = $(this).data('todo-id');
        
        showConfirm('Bu todo kalıcı olarak silinecek!', {
            confirmButtonText: 'Evet, sil!',
            cancelButtonText: 'İptal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/todos/' + todoId,
                    method: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        if (response.success) {
                            showSuccess(response.success);
                            dt_todos.ajax.reload();
                            updateStatistics();
                        } else {
                            showError('Todo silinemedi.');
                        }
                    },
                    error: function() {
                        showError('Todo silinirken hata oluştu.');
                    }
                });
            }
        });
    });

    // =====================
    // INITIALIZATION
    // =====================
    
    // İlk yüklemede istatistikleri güncelle
    updateStatistics();
});
</script>
@endpush
