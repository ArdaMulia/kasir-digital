<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'product_id',
        'product_name_snapshot',
        'quantity',
        'base_price_snapshot',
        'selling_price_snapshot',
        'base_commission_snapshot',
        'subtotal',
        'commission_amount',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'base_price_snapshot' => 'decimal:2',
            'selling_price_snapshot' => 'decimal:2',
            'base_commission_snapshot' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'commission_amount' => 'decimal:2',
        ];
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Rumus komisi sesuai issue.md #2.4:
     * Komisi = (Harga Jual Sales - Harga Dasar + Komisi Dasar Produk) * Qty
     *
     * NOTE: ini hanya helper kalkulasi dasar untuk Fase 1.
     * Logika lengkap (termasuk validasi & pengecekan role cashier) akan
     * dipindahkan ke Service Layer khusus di Fase 2, agar dipakai konsisten
     * oleh API transaksi maupun Job kalkulasi async (Horizon).
     */
    public static function calculateCommission(
        float $basePrice,
        float $sellingPrice,
        float $baseCommission,
        int $quantity,
        bool $isCommissionable
    ): float {
        if (! $isCommissionable) {
            return 0;
        }

        $commissionPerUnit = ($sellingPrice - $basePrice) + $baseCommission;

        return max($commissionPerUnit, 0) * $quantity;
    }
}
