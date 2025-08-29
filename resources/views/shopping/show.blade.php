@extends('layouts.master')

@section('title', 'Alışveriş: ' . $list->name . ' - KBTS ARJ Sistem')

@push('styles')
    <link rel="stylesheet" href="/vuexy/assets/vendor/libs/sweetalert2/sweetalert2.css" />
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

@php
    $total = $list->items->count();
    $purchased = $list->items->where('is_purchased', true)->count();
    $percent = $list->completion_percentage ?? ($total ? intval(round($purchased / $total * 100)) : 0);
  @endphp

<div class="row g-6 mb-6">
    <div class="col-lg-4 col-md-6">
        <div class="card h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div class="card-title mb-0">
                    <h5 class="mb-1 me-2">{{ $percent }}%</h5>
                    <p class="text-body mb-0">Tamamlanma</p>
                </div>
                <div class="card-icon">
                    <span class="badge bg-label-success rounded-pill p-2">
                        <i class="icon-base ti tabler-circle-check icon-22px"></i>
                    </span>
                </div>
            </div>
            <div class="px-4 pb-4">
                <div class="progress" style="height:8px">
                    <div class="progress-bar" style="width: {{ $percent }}%"></div>
                </div>
                <div class="d-flex justify-content-between text-muted small mt-2">
                    <span>{{ $purchased }}/{{ $total }} ürün</span>
                    <span> {{ $list->total_estimated_price }} ₺</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6">
        <div class="card h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div class="card-title mb-0">
                    <h5 class="mb-1 me-2">{{ $total }}</h5>
                    <p class="text-body mb-0">Toplam Kalem</p>
                </div>
                <div class="card-icon">
                    <span class="badge bg-label-info rounded-pill p-2">
                        <i class="icon-base ti tabler-package icon-22px"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-12">
        <div class="card h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div class="card-title mb-0">
                    <h5 class="mb-1 me-2">{{ $list->is_completed ? 'Evet' : 'Hayır' }}</h5>
                    <p class="text-body mb-0">Liste Tamamlandı mı?</p>
                </div>
                <div class="card-icon">
                    <span class="badge bg-label-primary rounded-pill p-2">
                        <i class="icon-base ti tabler-shopping-cart-check icon-22px"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Ana Kart: Liste Detayı (başlık/açıklama metin, modal ile düzenleme) --}}
<div class="card">
    <div class="card-header border-bottom d-flex flex-wrap gap-2 justify-content-between align-items-center">
        {{-- Solda ad & açıklama göster --}}
        <div class="flex-grow-1 me-3">
            <div class="d-flex align-items-center gap-2">
                <h5 class="mb-0">{{ $list->name }}</h5>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                    data-bs-target="#modalEditList" title="Listeyi Düzenle">
                    <i class="icon-base ti tabler-edit me-1"></i>Düzenle
                </button>
            </div>
            @if($list->description)
                <p class="text-muted mb-0 mt-1">{{ $list->description }}</p>
            @else
                <p class="text-muted mb-0 mt-1"><em>Açıklama yok</em></p>
            @endif
        </div>

        <div class="d-flex gap-2">
            <button class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddItem">
                <i class="icon-base ti tabler-plus me-1"></i>Yeni Ürün
            </button>

            {{-- confirm() yerine Swal kullanan js-confirm --}}
            <form method="POST" action="{{ route('shopping.destroy', $list) }}" class="js-confirm"
                data-confirm="Bu liste kalıcı olarak silinecek!">
                @csrf @method('DELETE')
                <button class="btn btn-outline-danger">Listeyi Sil</button>
            </form>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width:48px;">Alındı mı?</th>
                        <th>Ürün</th>
                        <th>Miktar</th>
                        <th>Kategori</th>
                        <th>Tahmini</th>
                        <th>Not</th>
                        <th style="width:80px;">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($list->items as $item)
                        {{-- item-row içinde silme formu class="js-confirm" olarak ayarlı olmalı --}}
                        @include('shopping.partials.item-row', ['item' => $item])
                    @empty
                        <tr>
                            <td colspan="7" class="text-muted">Henüz ürün yok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal: Liste Düzenle (form burada) --}}
<div class="modal fade" id="modalEditList" tabindex="-1" aria-labelledby="modalEditListLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="modalEditListLabel" class="modal-title">Listeyi Düzenle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            <form method="POST" action="{{ route('shopping.update', $list) }}">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-floating form-floating-outline mb-4">
                        <input class="form-control @error('name') is-invalid @enderror" name="name" id="edit-list-name"
                            value="{{ old('name', $list->name) }}" required>
                        <label for="edit-list-name">Liste Adı</label>
                        @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-floating form-floating-outline mb-2">
                        <input class="form-control @error('description') is-invalid @enderror" name="description"
                            id="edit-list-description" value="{{ old('description', $list->description) }}"
                            placeholder="Açıklama">
                        <label for="edit-list-description">Açıklama</label>
                        @error('description')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-secondary">Güncelle</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Offcanvas: Yeni Ürün Ekle --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddItem" aria-labelledby="offcanvasAddItemLabel">
    <div class="offcanvas-header">
        <h5 id="offcanvasAddItemLabel" class="offcanvas-title">Yeni Ürün Ekle</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0 h-100">
        <form class="add-new-item pt-0" method="POST" action="{{ route('shopping.items.store', $list) }}">
            @csrf

            <div class="form-floating form-floating-outline mb-6">
                <input name="name" id="add-item-name" class="form-control" placeholder="Ürün adı" required>
                <label for="add-item-name">Ürün Adı</label>
                @error('name')<small class="text-danger">{{ $message }}</small>@enderror
            </div>

            <div class="row g-2">
                <div class="col-6">
                    <div class="form-floating form-floating-outline mb-6">
                        <input name="quantity" id="add-item-quantity" type="number" min="1" value="1"
                            class="form-control" placeholder="Miktar">
                        <label for="add-item-quantity">Miktar</label>
                    </div>
                </div>

                <div class="col-6">
                    <div class="form-floating form-floating-outline mb-6">
                        @php($units = ['adet', 'koli', 'kilo', 'gram', 'litre', 'mililitre', 'paket', 'kutu', 'metre', 'santimetre', 'çift', 'takım'])
                        <select name="unit" id="add-item-unit" class="form-select">
                            <option value="">— Birim Seç —</option>
                            @foreach($units as $u)
                                <option value="{{ $u }}" {{ old('unit') === $u ? 'selected' : '' }}>
                                    {{ $u }}
                                </option>
                            @endforeach
                        </select>
                        <label for="add-item-unit">Birim</label>
                    </div>
                </div>
            </div>

            <div class="form-floating form-floating-outline mb-6">
                <input name="estimated_price" id="add-item-price" type="number" step="0.01" class="form-control"
                    placeholder="0.00">
                <label for="add-item-price">Tahmini Fiyat</label>
            </div>

            <div class="mb-6">
                <label class="form-label">Kategori</label>
                <select name="category_id" class="form-select">
                    <option value="">— Seçiniz —</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-floating form-floating-outline mb-6">
                <textarea name="notes" id="add-item-notes" class="form-control" rows="2"
                    placeholder="Notlar"></textarea>
                <label for="add-item-notes">Not</label>
            </div>

            <button class="btn btn-primary me-3 data-submit">Ekle</button>
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

    {{-- Doğrulama hatası olursa modal otomatik açılsın --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if ($errors->has('name') || $errors->has('description'))
                var editModal = new bootstrap.Modal(document.getElementById('modalEditList'));
                editModal.show();
            @endif
          });
    </script>
@endpush
