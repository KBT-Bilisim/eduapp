<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Todo extends Model
{
    protected $fillable = [
        'title',
        'description',
        'priority',
        'status',
        'due_date',
        'user_id'
    ];

    protected $casts = [
        'due_date' => 'date:Y-m-d',
    ];

    protected $appends = [
        'priority_badge',
        'status_badge', 
        'priority_text',
        'status_text'
    ];

    /**
     * Get the user that owns the todo.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get priority badge class
     */
    public function getPriorityBadgeAttribute(): string
    {
        return match($this->priority) {
            'low' => 'bg-label-success',
            'medium' => 'bg-label-warning',
            'high' => 'bg-label-danger',
            default => 'bg-label-secondary'
        };
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending' => 'bg-label-secondary',
            'in_progress' => 'bg-label-primary',
            'completed' => 'bg-label-success',
            default => 'bg-label-secondary'
        };
    }

    /**
     * Get priority text
     */
    public function getPriorityTextAttribute(): string
    {
        return match($this->priority) {
            'low' => 'Düşük',
            'medium' => 'Orta',
            'high' => 'Yüksek',
            default => 'Orta'
        };
    }

    /**
     * Get status text
     */
    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Bekliyor',
            'in_progress' => 'Devam Ediyor',
            'completed' => 'Tamamlandı',
            default => 'Bekliyor'
        };
    }
}
