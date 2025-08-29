@extends('layouts.master')

@section('title', 'Todo Yönetimi (Simple) - KBTS ARJ Sistem')
@push('styles')
    <link rel="stylesheet" href="/vuexy/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="/vuexy/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="/vuexy/assets/vendor/libs/flatpickr/flatpickr.css" />
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
                    <h5 class="mb-1 me-2">{{ $todos->where('status', 'pending')->count() }}</h5>
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
                    <h5 class="mb-1 me-2">{{ $todos->where('status', 'in_progress')->count() }}</h5>
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
                    <h5 class="mb-1 me-2">{{ $todos->where('status', 'completed')->count() }}</h5>
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
                    <h5 class="mb-1 me-2">{{ $todos->count() }}</h5>
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

<!-- Add Todo Form Card -->
<div class="card mb-6">
    <div class="card-header border-bottom d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Todo Listesi</h5>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTodoModal">
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
            <tbody>
                @forelse ($todos as $todo)
                <tr>
                    <td></td>
                    <td>
                        <div class="d-flex flex-column">
                            <span class="fw-medium text-heading">{{ $todo->title }}</span>
                            @if($todo->description)
                                <small class="text-muted">{{ Str::limit($todo->description, 50) }}</small>
                            @endif
                        </div>
                    </td>
                    <td>
                        <span class="badge {{ $todo->priority_badge }}">{{ $todo->priority_text }}</span>
                    </td>
                    <td>
                        <!-- Status Update Form -->
                        <form action="{{ route('todos.update-status', $todo->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <select name="status" class="form-select form-select-sm" 
                                    style="width: auto;" onchange="this.form.submit()">
                                <option value="pending" {{ $todo->status === 'pending' ? 'selected' : '' }}>Bekliyor</option>
                                <option value="in_progress" {{ $todo->status === 'in_progress' ? 'selected' : '' }}>Devam Ediyor</option>
                                <option value="completed" {{ $todo->status === 'completed' ? 'selected' : '' }}>Tamamlandı</option>
                            </select>
                        </form>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-3">
                                @php
                                    $initials = collect(explode(' ', $todo->user->name))
                                        ->map(fn($name) => strtoupper(substr($name, 0, 1)))
                                        ->take(2)
                                        ->implode('');
                                @endphp
                                <span class="avatar-initial rounded-circle bg-label-primary">{{ $initials }}</span>
                            </div>
                            <div class="d-flex flex-column">
                                <span class="fw-medium">{{ $todo->user->name }}</span>
                                <small class="text-muted">{{ $todo->user->email }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($todo->due_date)
                            <span class="text-{{ $todo->due_date->isPast() ? 'danger' : 'body' }}">
                                {{ $todo->due_date->format('d.m.Y') }}
                            </span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <!-- Edit Button -->
                            <button class="btn btn-text-secondary rounded-pill btn-icon me-1" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editModal{{ $todo->id }}">
                                <i class="icon-base ti tabler-edit icon-22px"></i>
                            </button>
                            
                            <!-- Delete Form -->
                            <form action="{{ route('todos.destroy', $todo->id) }}" method="POST" 
                                  class="d-inline" onsubmit="return confirm('Bu todo kalıcı olarak silinecek! Emin misiniz?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-text-secondary rounded-pill btn-icon">
                                    <i class="icon-base ti tabler-trash icon-22px"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>

                <!-- Edit Modal for each todo -->
                <div class="modal fade" id="editModal{{ $todo->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Todo Düzenle</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="{{ route('todos.update', $todo->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-body">
                                    <div class="form-floating form-floating-outline mb-4">
                                        <input type="text" class="form-control" name="title" 
                                               value="{{ $todo->title }}" required>
                                        <label>Başlık</label>
                                    </div>
                                    <div class="form-floating form-floating-outline mb-4">
                                        <textarea class="form-control" name="description" rows="3">{{ $todo->description }}</textarea>
                                        <label>Açıklama</label>
                                    </div>
                                    <div class="form-floating form-floating-outline mb-4">
                                        <select class="form-select" name="priority" required>
                                            <option value="low" {{ $todo->priority === 'low' ? 'selected' : '' }}>Düşük</option>
                                            <option value="medium" {{ $todo->priority === 'medium' ? 'selected' : '' }}>Orta</option>
                                            <option value="high" {{ $todo->priority === 'high' ? 'selected' : '' }}>Yüksek</option>
                                        </select>
                                        <label>Öncelik</label>
                                    </div>
                                    <div class="form-floating form-floating-outline mb-4">
                                        <select class="form-select" name="status" required>
                                            <option value="pending" {{ $todo->status === 'pending' ? 'selected' : '' }}>Bekliyor</option>
                                            <option value="in_progress" {{ $todo->status === 'in_progress' ? 'selected' : '' }}>Devam Ediyor</option>
                                            <option value="completed" {{ $todo->status === 'completed' ? 'selected' : '' }}>Tamamlandı</option>
                                        </select>
                                        <label>Durum</label>
                                    </div>
                                    <div class="form-floating form-floating-outline mb-4">
                                        <input type="date" class="form-control" name="due_date" 
                                               value="{{ $todo->due_date ? $todo->due_date->format('Y-m-d') : '' }}">
                                        <label>Son Tarih</label>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">İptal</button>
                                    <button type="submit" class="btn btn-primary">Güncelle</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4">
                        <div class="d-flex flex-column align-items-center">
                            <i class="icon-base ti tabler-inbox icon-48px text-muted mb-2"></i>
                            <span class="text-muted">Henüz hiç todo bulunmuyor.</span>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Add Todo Modal -->
<div class="modal fade" id="addTodoModal" tabindex="-1" aria-labelledby="addTodoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTodoModalLabel">Yeni Todo Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('todos.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-floating form-floating-outline mb-4">
                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                               id="title" name="title" placeholder="Todo başlığı" 
                               value="{{ old('title') }}" required>
                        <label for="title">Başlık</label>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-floating form-floating-outline mb-4">
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" placeholder="Todo açıklaması" 
                                  rows="3">{{ old('description') }}</textarea>
                        <label for="description">Açıklama</label>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-floating form-floating-outline mb-4">
                        <select class="form-select @error('priority') is-invalid @enderror" 
                                id="priority" name="priority" required>
                            <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Düşük</option>
                            <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Orta</option>
                            <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>Yüksek</option>
                        </select>
                        <label for="priority">Öncelik</label>
                        @error('priority')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-floating form-floating-outline mb-4">
                        <select class="form-select @error('status') is-invalid @enderror" 
                                id="status" name="status" required>
                            <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>Bekliyor</option>
                            <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>Devam Ediyor</option>
                            <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Tamamlandı</option>
                        </select>
                        <label for="status">Durum</label>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-floating form-floating-outline mb-4">
                        <input type="date" class="form-control @error('due_date') is-invalid @enderror" 
                               id="due_date" name="due_date" value="{{ old('due_date') }}">
                        <label for="due_date">Son Tarih</label>
                        @error('due_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="/vuexy/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
<script>
$(document).ready(function() {
    // Simple DataTable initialization - no server-side processing
    $('#todos-table').DataTable({
        responsive: true,
        columnDefs: [
            {
                targets: 0,
                orderable: false,
                searchable: false,
                responsivePriority: 3,
                render: function (data, type, full, meta) {
                    return '';
                }
            },
            {
                targets: -1, // last column (actions)
                orderable: false,
                searchable: false,
                responsivePriority: 1
            }
        ],
        order: [[1, 'desc']], // Order by title descending
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        language: {
            search: 'Ara:',
            lengthMenu: '_MENU_ kayıt göster',
            info: '_TOTAL_ kayıttan _START_ - _END_ arası gösteriliyor',
            infoEmpty: 'Kayıt bulunamadı',
            infoFiltered: '(_MAX_ kayıt içinden filtrelendi)',
            zeroRecords: 'Eşleşen kayıt bulunamadı',
            paginate: {
                first: 'İlk',
                last: 'Son',
                next: 'Sonraki',
                previous: 'Önceki'
            }
        }
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);

    // Validation hataları varsa add modal'ı aç
    @if($errors->any())
        $('#addTodoModal').modal('show');
    @endif
});
</script>
@endpush
