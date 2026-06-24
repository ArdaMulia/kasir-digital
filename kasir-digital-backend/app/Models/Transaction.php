<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_REFUNDED = 'refunded';

    protected $fillable = [
        'transaction_code',
        'cashier_id',
        'owner_id',
        'total_amount',
        'total_commission',
        'payment_method',
        'status',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'total_commission' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(TransactionItem::class, 'transaction_id');
    }

    /**
     * Apakah transaksi ini menghasilkan komisi (hanya jika kasir adalah Sales).
     */
    public function isCommissionable(): bool
    {
        return $this->cashier?->isSales() ?? false;
    }
}
