<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShoppingItem extends Model
{
    protected $fillable = [
        'name','quantity','unit','estimated_price','is_purchased','notes',
        'shopping_list_id','category_id'
    ];

    protected $casts = [
        'quantity'        => 'integer',
        'is_purchased'    => 'boolean',
        'estimated_price' => 'decimal:2',
    ];

    public function list(): BelongsTo
    {
        return $this->belongsTo(ShoppingList::class, 'shopping_list_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getLineEstimatedTotalAttribute(): string
    {
        $total = ((float)($this->estimated_price ?? 0)) * ((int)($this->quantity ?? 1));
        return number_format($total, 2, '.', '');
    }
}
