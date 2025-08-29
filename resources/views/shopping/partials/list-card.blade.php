<div class="col-sm-6 col-lg-4">
  <div class="card h-100 shadow-sm">
    <div class="card-body">
      <div class="d-flex justify-content-between">
        <h5 class="card-title mb-1">{{ $list->name }}</h5>
        @if($list->is_completed)
          <span class="badge bg-success">Tamamlandı</span>
        @endif
      </div>
      <p class="text-muted small mb-2">{{ $list->description }}</p>

      @php
        $percent = $list->completion_percentage ?? 0;
        $purchased = $list->purchased_items_count ?? $list->items()->where('is_purchased',true)->count();
        $total = $list->items_count ?? $list->items()->count();
      @endphp

      <div class="progress mb-2" style="height:8px">
        <div class="progress-bar" role="progressbar" style="width: {{ $percent }}%"></div>
      </div>
      <div class="d-flex justify-content-between text-muted small">
        <span>{{ $purchased }}/{{ $total }} ürün</span>
        <span> {{ $list->total_estimated_price }} ₺</span>
      </div>

      <a href="{{ route('shopping.show', $list) }}" class="btn btn-sm btn-primary mt-2 w-100">
        Detaya Git
      </a>
    </div>
  </div>
</div>
