<?php

namespace App\Http\Controllers;

use App\Models\ShoppingItem;
use App\Models\ShoppingList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShoppingItemController extends Controller
{
    public function store(Request $request, ShoppingList $shopping)
    {
        abort_unless($shopping->user_id === Auth::id(), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'unit' => ['nullable', 'string', 'max:32'],
            'estimated_price' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'category_id' => ['nullable', 'exists:categories,id'],
        ]);

        $data['quantity'] = $data['quantity'] ?? 1;
        $data['shopping_list_id'] = $shopping->id;

        $item = ShoppingItem::create($data);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Ürün eklendi.', 'item' => $item]);
        }

        return back()->with('success', 'Ürün eklendi.');
    }

    public function togglePurchased(Request $request, ShoppingItem $item)
    {
        abort_unless($item->list->user_id === Auth::id(), 403);

        $item->is_purchased = !$item->is_purchased;
        $item->save();

        $list = $item->list;
        $total = $list->items()->count();
        $purchased = $list->items()->where('is_purchased', true)->count();
        $list->is_completed = ($total > 0 && $total === $purchased);
        $list->save();

        if ($request->expectsJson()) {
            $percent = $total ? intval(round($purchased / $total * 100)) : 0;
            return response()->json([
                'success' => true,
                'message' => 'Durum güncellendi.',
                'is_purchased' => $item->is_purchased,
                'is_completed' => $list->is_completed,
                'percent' => $percent
            ]);
        }

        return back()->with('success', 'Durum güncellendi.');
    }

    public function destroy(Request $request, ShoppingItem $item)
    {
        abort_unless($item->list->user_id === Auth::id(), 403);

        $item->delete();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Ürün silindi.']);
        }

        return back()->with('success', 'Ürün silindi.');
    }
}
