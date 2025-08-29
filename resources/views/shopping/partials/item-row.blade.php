<tr class="{{ $item->is_purchased ? 'table-success' : '' }}">
  <td>
    <form method="POST" action="{{ route('shopping.items.toggle', $item) }}">
      @csrf @method('PATCH')
      <input type="checkbox" onchange="this.form.submit()" {{ $item->is_purchased ? 'checked' : '' }}>
    </form>
  </td>

  <td>{{ $item->name }}</td>
  <td>{{ $item->quantity }} {{ $item->unit }}</td>
  <td>
    @if($item->category)
      <span class="badge" style="background:{{ $item->category->color ?? '#6c757d' }}">
        {{ $item->category->name }}
      </span>
    @endif
  </td>
  <td>{{ $item->line_estimated_total }} ₺</td>
  <td class="text-muted">{{ $item->notes }}</td>

  <td>
    {{-- confirm() yerine js-confirm + Swal --}}
    <form method="POST"
          action="{{ route('shopping.items.destroy', $item) }}"
          class="js-confirm"
          data-confirm="Bu ürün kalıcı olarak silinecek!">
      @csrf @method('DELETE')
      <button class="btn btn-sm btn-outline-danger">Sil</button>
    </form>
  </td>
</tr>
