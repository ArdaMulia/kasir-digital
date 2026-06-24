<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    public const ROLE_OWNER = 'owner';
    public const ROLE_SALES = 'sales';

    protected $fillable = [
        'owner_id',
        'name',
        'email',
        'phone',
        'password',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi: jika user ini Sales, owner() mengarah ke Owner pemilik.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Relasi: jika user ini Owner, sales() berisi semua Sales di bawahnya.
     */
    public function sales(): HasMany
    {
        return $this->hasMany(User::class, 'owner_id');
    }

    /**
     * Produk yang dimiliki user ini (hanya relevan jika user adalah Owner).
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'owner_id');
    }

    /**
     * Harga jual yang ditetapkan user ini (hanya relevan jika user adalah Sales).
     */
    public function salesPrices(): HasMany
    {
        return $this->hasMany(SalesPrice::class, 'sales_id');
    }

    /**
     * Transaksi yang dilakukan user ini sebagai kasir.
     */
    public function transactionsAsCashier(): HasMany
    {
        return $this->hasMany(Transaction::class, 'cashier_id');
    }

    /**
     * Ringkasan komisi (hanya relevan jika user adalah Sales).
     */
    public function commissionSummaries(): HasMany
    {
        return $this->hasMany(CommissionSummary::class, 'sales_id');
    }

    public function isOwner(): bool
    {
        return $this->hasRole(self::ROLE_OWNER);
    }

    public function isSales(): bool
    {
        return $this->hasRole(self::ROLE_SALES);
    }
}
