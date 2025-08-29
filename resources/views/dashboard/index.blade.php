@extends('layouts.master')

@section('title', 'Dashboard - KBTS ARJ Sistem')

@section('content')
<!-- Flash Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Başarılı!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Hata!</strong> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Welcome card -->
<div class="row g-6 mb-6">
    <div class="col-12">
        <div class="card bg-primary">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h4 class="text-white mb-2">Hoşgeldiniz {{ Auth::user()->name ?? Auth::user()->email }}! 🎉</h4>
                        <p class="text-white mb-4">KBTS ARJ Sistem admin paneline başarıyla giriş yaptınız. Sol menüden istediğiniz modüle geçebilirsiniz.</p>
                        <a href="{{ route('users.index') }}" class="btn btn-label-light">
                            <i class="icon-base ti tabler-users me-2"></i>
                            Kullanıcıları Görüntüle
                        </a>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="avatar avatar-xl mt-3">
                            <span class="avatar-initial rounded bg-label-light">
                                <i class="icon-base ti tabler-crown text-primary" style="font-size: 3rem;"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Row -->
<div class="row g-6">
    <!-- Left column -->
    <div class="col-xl-8 col-lg-7">
        <!-- System Info Card -->
        <div class="card mb-6">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title m-0">Sistem Bilgileri</h5>
                <div class="dropdown">
                    <button class="btn btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                        <i class="icon-base ti tabler-dots-vertical icon-20px"></i>
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="javascript:void(0);">Yenile</a>
                        <a class="dropdown-item" href="javascript:void(0);">Raporla</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <td><strong>Laravel:</strong></td>
                                <td>{{ app()->version() }}</td>
                            </tr>
                            <tr>
                                <td><strong>PHP:</strong></td>
                                <td>{{ phpversion() }}</td>
                            </tr>
                            <tr>
                                <td><strong>Database:</strong></td>
                                <td>{{ config('database.default') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Timezone:</strong></td>
                                <td>{{ config('app.timezone') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Kullanıcı:</strong></td>
                                <td>{{ Auth::user()->email }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Activity Timeline -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title m-0">Son Aktiviteler</h5>
            </div>
            <div class="card-body">
                <ul class="timeline mb-0">
                    <li class="timeline-item timeline-item-transparent">
                        <span class="timeline-point timeline-point-primary"></span>
                        <div class="timeline-event">
                            <div class="timeline-header mb-3">
                                <h6 class="mb-0">Sistem Girişi</h6>
                                <small class="text-muted">{{ date('d M Y H:i') }}</small>
                            </div>
                            <p class="mb-0">{{ Auth::user()->name ?? Auth::user()->email }} sisteme giriş yaptı.</p>
                        </div>
                    </li>
                    <li class="timeline-item timeline-item-transparent">
                        <span class="timeline-point timeline-point-success"></span>
                        <div class="timeline-event">
                            <div class="timeline-header mb-3">
                                <h6 class="mb-0">Admin Panel</h6>
                                <small class="text-muted">{{ date('d M Y H:i') }}</small>
                            </div>
                            <p class="mb-0">Dashboard sayfası yüklendi.</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Right column -->
    <div class="col-xl-4 col-lg-5">
        <!-- Quick Actions -->
        <div class="card mb-6">
            <div class="card-header">
                <h5 class="card-title m-0">Hızlı İşlemler</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-3">
                    <button class="btn btn-outline-primary" onclick="location.reload()">
                        <i class="icon-base ti tabler-refresh me-2"></i>
                        Sayfayı Yenile
                    </button>
                    <a href="{{ route('users.index') }}" class="btn btn-outline-success">
                        <i class="icon-base ti tabler-users me-2"></i>
                        Kullanıcı Yönetimi
                    </a>
                    <button class="btn btn-outline-warning" onclick="document.documentElement.setAttribute('data-bs-theme', document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark')">
                        <i class="icon-base ti tabler-palette me-2"></i>
                        Tema Değiştir
                    </button>
                </div>
            </div>
        </div>

        <!-- Calendar Widget -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title m-0">Takvim</h5>
            </div>
            <div class="card-body text-center">
                <div class="display-3 text-primary">{{ date('d') }}</div>
                <div class="h4 text-muted">{{ \Carbon\Carbon::now()->locale('tr')->monthName }}</div>
                <div class="text-muted">{{ date('Y') }}</div>
                <hr>
                <div class="badge bg-label-primary">{{ \Carbon\Carbon::now()->locale('tr')->dayName }}</div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        console.log('Dashboard loaded with Vuexy theme!');
    });
</script>
@endpush
