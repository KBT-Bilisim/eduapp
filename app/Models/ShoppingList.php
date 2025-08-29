<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShoppingList extends Model
{
    protected $fillable = ['name', 'description', 'is_completed', 'user_id'];

    protected $casts = [
        'is_completed' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ShoppingItem::class);
    }

    // Tamamlanma yüzdesi
    public function getCompletionPercentageAttribute(): int
    {
        $total = $this->items()->count();
        if ($total === 0) return 0;
        $purchased = $this->items()->where('is_purchased', true)->count();
        return (int) round(($purchased / $total) * 100);
    }

    // Toplam tahmini tutar (quantity * estimated_price)
    public function getTotalEstimatedPriceAttribute(): string
    {
        $sum = $this->items()
            ->selectRaw('COALESCE(SUM(quantity * COALESCE(estimated_price,0)),0) as total')
            ->value('total');
        return number_format((float)$sum, 2, '.', '');
    }
}
