<?php

namespace App\Http\Controllers;

use App\Models\ShoppingList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShoppingListController extends Controller
{
    public function index()
    {
        $userId = \Auth::id();

        // Liste koleksiyonu (kartlarda kullanacağız)
        $lists = \App\Models\ShoppingList::withCount([
            'items',
            'items as purchased_items_count' => fn($q) => $q->where('is_purchased', true)
        ])
            ->where('user_id', $userId)
            ->latest()
            ->paginate(12);

        // Üstte Todo istatistik kartlarına benzer istatistikler
        $totalLists = \App\Models\ShoppingList::where('user_id', $userId)->count();
        $completedLists = \App\Models\ShoppingList::where('user_id', $userId)->where('is_completed', true)->count();
        $activeLists = $totalLists - $completedLists;

        $totalItems = \App\Models\ShoppingItem::whereHas('list', fn($q) => $q->where('user_id', $userId))->count();
        $purchasedItems = \App\Models\ShoppingItem::whereHas('list', fn($q) => $q->where('user_id', $userId))
            ->where('is_purchased', true)->count();

        $shoppingStats = [
            'active' => $activeLists,
            'completed' => $completedLists,
            'total_lists' => $totalLists,
            'total_items' => $totalItems,
            'purchased_items' => $purchasedItems,
        ];

        return view('shopping.index', compact('lists', 'shoppingStats'));
    }


    public function show(ShoppingList $shopping)
    {
        abort_unless($shopping->user_id === Auth::id(), 403);

        $shopping->load(['items.category']);
        $categories = \App\Models\Category::orderBy('name')->get();

        return view('shopping.show', [
            'list' => $shopping,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $list = ShoppingList::create($data + ['user_id' => Auth::id()]);

        return redirect()->route('shopping.show', $list)
            ->with('success', 'Liste oluşturuldu.');
    }

    public function update(Request $request, ShoppingList $shopping)
    {
        abort_unless($shopping->user_id === Auth::id(), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $shopping->update($data);

        // Durumu otomatik set et
        $total = $shopping->items()->count();
        $purchased = $shopping->items()->where('is_purchased', true)->count();
        $shopping->is_completed = ($total > 0 && $total === $purchased);
        $shopping->save();

        return back()->with('success', 'Liste güncellendi.');
    }

    public function destroy(ShoppingList $shopping)
    {
        abort_unless($shopping->user_id === Auth::id(), 403);
        $shopping->delete();

        return redirect()->route('shopping.index')->with('success', 'Liste silindi.');
    }
}
