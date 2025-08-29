@extends('layouts.master')

@section('title', 'Kullanıcılar - KBTS ARJ Sistem')
@push('styles')
    <link rel="stylesheet" href="/vuexy/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="/vuexy/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="/vuexy/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="/vuexy/assets/vendor/libs/select2/select2.css" />
    <link rel="stylesheet"
        href="/vuexy/assets/vendor/libs/@form-validation/form-validation.css" />
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

<!-- Users List Table -->
<div class="card">
    <div class="card-header border-bottom">
        <h5 class="card-title mb-0">Kullanıcı Yönetimi</h5>
        <div class="d-flex justify-content-between align-items-center row pt-4 gap-6 gap-md-0">
            <div class="col-md-4 user_role"></div>
            <div class="col-md-4 user_plan"></div>
            <div class="col-md-4 user_status"></div>
        </div>
    </div>
    <div class="card-datatable table-responsive">
        <table class="datatables-users table">
            <thead class="table-light">
                <tr>
                    <th></th>
                    <th></th>
                    <th>Kullanıcı</th>
                    <th>Rol</th>
                    
                    <th>Durum</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                <tr>
                    <td></td>
                    <td><input type="checkbox" class="dt-checkboxes form-check-input"></td>
                    <td>
                        <div class="d-flex justify-content-start align-items-center user-name">
                            <div class="avatar-wrapper">
                                <div class="avatar avatar-sm me-4">
                                    @php
                                        $initials = collect(explode(' ', $user->name))->map(function($name) {
                                            return strtoupper(substr($name, 0, 1));
                                        })->take(2)->implode('');
                                    @endphp
                                    <span class="avatar-initial rounded-circle bg-label-primary">{{ $initials }}</span>
                                </div>
                            </div>
                            <div class="d-flex flex-column">
                                <span class="fw-medium">{{ $user->name }}</span>
                                <small>{{ $user->email }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="text-truncate d-flex align-items-center text-heading">
                            <i class="icon-base ti tabler-device-desktop icon-md text-danger me-2"></i>
                            Admin
                        </span>
                    </td>
                    
                    <td><span class="badge bg-label-success">Aktif</span></td>
                    <td>
                        <div class="d-flex align-items-center">
                            <button class="btn btn-text-secondary rounded-pill btn-icon edit-user-btn" data-user-id="{{ $user->id }}" data-bs-toggle="modal" data-bs-target="#editUserModal">
                                <i class="icon-base ti tabler-edit icon-22px"></i>
                            </button>
                            <button class="btn btn-text-secondary rounded-pill btn-icon delete-user-btn" data-user-id="{{ $user->id }}">
                                <i class="icon-base ti tabler-trash icon-22px"></i>
                            </button>
                        </div>
                    </td>
                </tr> @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Add User Offcanvas -->
<div class="offcanvas
        offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel">
    <div class="offcanvas-header">
        <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Yeni Kullanıcı Ekle</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0 p-6 h-100">
        <form class="add-new-user pt-0" id="addNewUserForm" method="POST" action="{{ route('users.store') }}">
            @csrf
            <div class="form-control-validation mb-6">
                <label class="form-label" for="add-user-fullname">İsim Soyisim</label>
                <input type="text" class="form-control" id="add-user-fullname" placeholder="John Doe" name="name"
                    aria-label="John Doe" required />
            </div>
            <div class="form-control-validation mb-6">
                <label class="form-label" for="add-user-email">Email</label>
                <input type="email" id="add-user-email" class="form-control" placeholder="john.doe@example.com"
                    aria-label="john.doe@example.com" name="email" required />
            </div>
            <div class="form-control-validation mb-6">
                <label class="form-label" for="add-user-password">Şifre</label>
                <input type="password" id="add-user-password" class="form-control" placeholder="Şifre" aria-label="Şifre"
                    name="password" required />
            </div>
            <div class="form-control-validation mb-6">
                <label class="form-label" for="user-role">Kullanıcı Rolü</label>
                <select id="user-role" class="form-select" name="role">
                    <option value="">Rol Seçin</option>
                    <option value="Admin">Admin</option>
                    <option value="Author">Author</option>
                    <option value="Editor">Editor</option>
                    <option value="Maintainer">Maintainer</option>
                    <option value="Subscriber">Subscriber</option>
                </select>
            </div>
            <div class="form-control-validation mb-6">
                <label class="form-label" for="user-plan">Plan Seçin</label>
                <select id="user-plan" class="form-select" name="plan">
                    <option value="">Plan Seçin</option>
                    <option value="Basic">Basic</option>
                    <option value="Company">Company</option>
                    <option value="Enterprise">Enterprise</option>
                    <option value="Team">Team</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary me-3 data-submit">Kaydet</button>
            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">İptal</button>
        </form>
    </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Kullanıcı Düzenle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editUserForm" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="edit-user-id" name="user_id">

                        <div class="row">
                            <div class="col-12 col-md-6 mb-6">
                                <label class="form-label" for="edit-user-fullname">İsim Soyisim</label>
                                <input type="text" class="form-control" id="edit-user-fullname" name="name"
                                    required />
                            </div>
                            <div class="col-12 col-md-6 mb-6">
                                <label class="form-label" for="edit-user-email">Email</label>
                                <input type="email" id="edit-user-email" class="form-control" name="email"
                                    required />
                            </div>
                            <div class="col-12 col-md-6 mb-6">
                                <label class="form-label" for="edit-user-password">Yeni Şifre (Opsiyonel)</label>
                                <input type="password" id="edit-user-password" class="form-control" name="password" />
                                <div class="form-text">Şifreyi değiştirmek istemiyorsanız boş bırakın</div>
                            </div>
                            <div class="col-12 col-md-6 mb-6">
                                <label class="form-label" for="edit-user-role">Kullanıcı Rolü</label>
                                <select id="edit-user-role" class="form-select" name="role">
                                    <option value="">Rol Seçin</option>
                                    <option value="Admin">Admin</option>
                                    <option value="Author">Author</option>
                                    <option value="Editor">Editor</option>
                                    <option value="Maintainer">Maintainer</option>
                                    <option value="Subscriber">Subscriber</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" form="editUserForm" class="btn btn-primary">Güncelle</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="/vuexy/assets/vendor/libs/moment/moment.js"></script>
    <script src="/vuexy/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="/vuexy/assets/vendor/libs/select2/select2.js"></script>
    <script src="/vuexy/assets/vendor/libs/@form-validation/popular.js"></script>
    <script src="/vuexy/assets/vendor/libs/@form-validation/bootstrap5.js"></script>
    <script src="/vuexy/assets/vendor/libs/@form-validation/auto-focus.js"></script>
    <script src="/vuexy/assets/vendor/libs/cleavejs/cleave.js"></script>
    <script src="/vuexy/assets/vendor/libs/cleavejs/cleave-phone.js"></script>

    <script>
        console.log('Users index script loaded!');

        // DataTable configuration
        let dt_user_table = $('.datatables-users');

        if (dt_user_table.length) {
            var dt_user = dt_user_table.DataTable({
                order: [
                    [2, 'asc']
                ],
                language: {
                    search: '',
                    searchPlaceholder: 'Kullanıcı Ara...',
                    lengthMenu: '_MENU_ kayıt göster',
                    info: '_TOTAL_ kayıttan _START_ - _END_ arası gösteriliyor',
                    infoEmpty: 'Kayıt bulunamadı',
                    paginate: {
                        next: '<i class="icon-base ti tabler-chevron-right"></i>',
                        previous: '<i class="icon-base ti tabler-chevron-left"></i>'
                    }
                },
                columnDefs: [{
                        className: 'control',
                        searchable: false,
                        orderable: false,
                        responsivePriority: 2,
                        targets: 0
                    },
                    {
                        targets: 1,
                        orderable: false,
                        searchable: false,
                        responsivePriority: 4
                    },
                    {
                        targets: -1,
                        searchable: false,
                        orderable: false
                    }
                ],
                responsive: true,
                dom: '<"card-header d-flex flex-wrap pb-md-2"<"d-flex align-items-center me-5"f><"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-md-end gap-3 gap-sm-2 flex-wrap flex-sm-nowrap"lB>>t<"row mx-2"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                buttons: [{
                    text: '<i class="icon-base ti tabler-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Yeni Kullanıcı Ekle</span>',
                    className: 'add-new btn btn-primary waves-effect waves-light',
                    action: function(e, dt, node, config) {
                        // Open the offcanvas
                        const offcanvas = new bootstrap.Offcanvas(document.getElementById(
                            'offcanvasAddUser'));
                        offcanvas.show();
                    }
                }]
            });
        }

        // Users data for editing
        const usersData = {!! json_encode($users->toArray()) !!};

        // Edit user function
        function editUser(id) {
            console.log('Edit user clicked:', id);
            const user = usersData.find(u => u.id == id);
            if (user) {
                console.log('User found:', user);
                document.getElementById('edit-user-id').value = user.id;
                document.getElementById('edit-user-fullname').value = user.name;
                document.getElementById('edit-user-email').value = user.email;
                document.getElementById('edit-user-password').value = '';
                document.getElementById('editUserForm').action = '{{ url('/users') }}/' + user.id;
                console.log('Form action set to:', document.getElementById('editUserForm').action);
            } else {
                console.log('User not found!');
            }
        }

        // Delete user function  
        function deleteUser(id) {
            console.log('Delete user clicked:', id);
            if (confirm('Bu kullanıcıyı silmek istediğinizden emin misiniz?')) {
                console.log('Delete confirmed for user:', id);
                // Create form and submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ url('/users') }}/' + id;

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';

                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';

                form.appendChild(csrfToken);
                form.appendChild(methodInput);
                document.body.appendChild(form);
                console.log('Submitting delete form...');
                form.submit();
            }
        }

        // Form validation
        document.addEventListener('DOMContentLoaded', function(e) {
            const addNewUserForm = document.getElementById('addNewUserForm');
            const editUserForm = document.getElementById('editUserForm');

            // Event delegation for edit and delete buttons
            document.addEventListener('click', function(e) {
                if (e.target.closest('.edit-user-btn')) {
                    const btn = e.target.closest('.edit-user-btn');
                    const userId = btn.getAttribute('data-user-id');
                    console.log('Edit button clicked for user:', userId);
                    editUser(userId);
                }

                if (e.target.closest('.delete-user-btn')) {
                    const btn = e.target.closest('.delete-user-btn');
                    const userId = btn.getAttribute('data-user-id');
                    console.log('Delete button clicked for user:', userId);
                    deleteUser(userId);
                }
            });

            // Simple form submission without FormValidation for now
            if (addNewUserForm) {
                addNewUserForm.addEventListener('submit', function(e) {
                    console.log('Add form submitting...', this.action);
                    // Let the form submit normally
                });
            }

            if (editUserForm) {
                editUserForm.addEventListener('submit', function(e) {
                    console.log('Edit form submitting...', this.action);
                    // Let the form submit normally
                });
            }
        });
    </script>
@endpush
