<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'name',
        'sku',
        'category',
        'base_price',
        'base_commission',
        'stock',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'base_commission' => 'decimal:2',
            'stock' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Semua harga jual yang ditetapkan masing-masing sales untuk produk ini.
     */
    public function salesPrices(): HasMany
    {
        return $this->hasMany(SalesPrice::class, 'product_id');
    }

    public function transactionItems(): HasMany
    {
        return $this->hasMany(TransactionItem::class, 'product_id');
    }

    /**
     * Ambil harga jual milik sales tertentu untuk produk ini.
     * Mengembalikan null jika sales belum menetapkan harga (artinya pakai base_price).
     */
    public function sellingPriceFor(User $sales): ?float
    {
        return $this->salesPrices()
            ->where('sales_id', $sales->id)
            ->value('selling_price');
    }
}
