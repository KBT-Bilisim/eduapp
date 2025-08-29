@extends('layouts.master')

@section('title', 'Alışveriş Listeleri - KBTS ARJ Sistem')

@push('styles')
    <link rel="stylesheet" href="/vuexy/assets/vendor/libs/sweetalert2/sweetalert2.css" />
    {{-- İstersen diğer vendor css’leri burada tutabilirsin --}}
@endpush

@section('content')
    {{-- Bootstrap fallback (JS kapalıysa) --}}
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

    {{-- Üst İstatistik Kartları --}}
    <div class="row g-6 mb-6">
        <div class="col-lg-3 col-md-6">
            <div class="card h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div class="card-title mb-0">
                        <h5 class="mb-1 me-2">{{ $shoppingStats['active'] ?? 0 }}</h5>
                        <p class="text-body mb-0">Aktif Listeler</p>
                    </div>
                    <div class="card-icon">
                        <span class="badge bg-label-primary rounded-pill p-2">
                            <i class="icon-base ti tabler-shopping-cart icon-22px"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div class="card-title mb-0">
                        <h5 class="mb-1 me-2">{{ $shoppingStats['completed'] ?? 0 }}</h5>
                        <p class="text-body mb-0">Tamamlanan Listeler</p>
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
                        <h5 class="mb-1 me-2">{{ $shoppingStats['total_items'] ?? 0 }}</h5>
                        <p class="text-body mb-0">Toplam Ürün</p>
                    </div>
                    <div class="card-icon">
                        <span class="badge bg-label-info rounded-pill p-2">
                            <i class="icon-base ti tabler-package icon-22px"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div class="card-title mb-0">
                        <h5 class="mb-1 me-2">{{ $shoppingStats['purchased_items'] ?? 0 }}</h5>
                        <p class="text-body mb-0">Alınan Ürün</p>
                    </div>
                    <div class="card-icon">
                        <span class="badge bg-label-warning rounded-pill p-2">
                            <i class="icon-base ti tabler-circle-check icon-22px"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Listeler Kartı --}}
    <div class="card">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Alışveriş Listeleri</h5>
            <button class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddList">
                <i class="icon-base ti tabler-plus me-1"></i>Yeni Liste
            </button>
        </div>

        <div class="card-body">
            <div class="row g-3">
                @forelse($lists as $list)
                    {{-- list-card içinde silme formun varsa class="js-confirm" ekle --}}
                    @include('shopping.partials.list-card', ['list' => $list])
                @empty
                    <div class="col-12">
                        <div class="alert alert-info mb-0">Henüz liste yok. Sağ üstten “Yeni Liste” oluştur.</div>
                    </div>
                @endforelse
            </div>

            <div class="mt-3">
                {{ $lists->links() }}
            </div>
        </div>
    </div>

    {{-- Offcanvas: Yeni Liste --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddList" aria-labelledby="offcanvasAddListLabel">
        <div class="offcanvas-header">
            <h5 id="offcanvasAddListLabel" class="offcanvas-title">Yeni Alışveriş Listesi</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body mx-0 flex-grow-0 h-100">
            <form class="add-new-list pt-0" id="addListForm" method="POST" action="{{ route('shopping.store') }}">
                @csrf
                <div class="form-floating form-floating-outline mb-6">
                    <input type="text" class="form-control" id="add-list-name" placeholder="Liste adı" name="name"
                        required />
                    <label for="add-list-name">Liste Adı</label>
                    @error('name')<small class="text-danger">{{ $message }}</small>@enderror
                </div>
                <div class="form-floating form-floating-outline mb-6">
                    <textarea class="form-control" id="add-list-description" placeholder="Açıklama" name="description"
                        rows="3"></textarea>
                    <label for="add-list-description">Açıklama</label>
                    @error('description')<small class="text-danger">{{ $message }}</small>@enderror
                </div>
                <button type="submit" class="btn btn-primary me-3 data-submit">Kaydet</button>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">İptal</button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="/vuexy/assets/vendor/libs/sweetalert2/sweetalert2.js"></script>
    <script>
        // Sayfaya özel Swal helper'ları
        function showSuccess(msg, cfg = {}) {
            Swal.fire(Object.assign({ icon: 'success', title: 'Başarılı', text: msg, confirmButtonText: 'Tamam' }, cfg));
        }
        function showError(msg, cfg = {}) {
            Swal.fire(Object.assign({ icon: 'error', title: 'Hata', text: msg, confirmButtonText: 'Tamam' }, cfg));
        }
        function showConfirm(text, cfg = {}) {
            return Swal.fire(Object.assign({
                icon: 'warning',
                title: 'Emin misiniz?',
                text,
                showCancelButton: true,
                confirmButtonText: cfg.confirmButtonText || 'Evet',
                cancelButtonText: cfg.cancelButtonText || 'İptal'
            }, cfg));
        }

        // Flash'ları Swal olarak göster
        document.addEventListener('DOMContentLoaded', function () {
            const successMsg = @json(session('success'));
            const errorMsg = @json(session('error'));
            if (successMsg) showSuccess(successMsg);
            if (errorMsg) showError(errorMsg);
        });

        // .js-confirm formlarında Swal onayı
        document.addEventListener('submit', function (e) {
            const form = e.target.closest('form.js-confirm');
            if (!form) return;

            if (form.dataset.swaled === '1') return; // ikinci kez tetiklemeyi önle
            e.preventDefault();

            const text = form.dataset.confirm || 'Bu işlem geri alınamaz.';
            showConfirm(text, { confirmButtonText: 'Evet, sil!', cancelButtonText: 'İptal' })
                .then(res => {
                    if (res.isConfirmed) {
                        form.dataset.swaled = '1';
                        form.submit();
                    }
                });
        });
    </script>
@endpush