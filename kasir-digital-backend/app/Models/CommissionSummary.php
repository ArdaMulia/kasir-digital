<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommissionSummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_id',
        'owner_id',
        'period_start',
        'period_end',
        'total_commission',
        'total_transactions',
        'total_items_sold',
    ];

    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end' => 'date',
            'total_commission' => 'decimal:2',
            'total_transactions' => 'integer',
            'total_items_sold' => 'integer',
        ];
    }

    public function sales(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sales_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
