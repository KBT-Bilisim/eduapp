{{-- Ürün ekleme modalı --}}
<div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('shopping.items.store', $list->id) }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="addItemModalLabel">Ürün Ekle</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="name" class="form-label">Ürün Adı</label>
            <input type="text" class="form-control" name="name" required>
          </div>
          <div class="mb-3">
            <label for="quantity" class="form-label">Miktar</label>
            <input type="number" class="form-control" name="quantity" min="1" required>
          </div>
          <div class="mb-3">
            <label for="unit" class="form-label">Birim</label>
            <input type="text" class="form-control" name="unit">
          </div>
          <div class="mb-3">
            <label for="estimated_price" class="form-label">Tahmini Fiyat</label>
            <input type="number" class="form-control" name="estimated_price" step="0.01">
          </div>
          <div class="mb-3">
            <label for="category_id" class="form-label">Kategori</label>
            <select name="category_id" class="form-select" required>
              @foreach(App\Models\Category::all() as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label for="notes" class="form-label">Notlar</label>
            <textarea class="form-control" name="notes"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
          <button type="submit" class="btn btn-primary">Ekle</button>
        </div>
      </form>
    </div>
  </div>
</div>
