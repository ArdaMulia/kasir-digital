<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Produk dimiliki oleh Owner. Owner menentukan harga dasar (base_price)
     * dan komisi dasar (base_commission) yang berlaku untuk semua sales di bawahnya.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->foreignId('owner_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('name');
            $table->string('sku')->nullable()->unique();
            $table->string('category')->nullable();

            $table->decimal('base_price', 14, 2)
                ->comment('Harga dasar / modal, ditentukan Owner');

            $table->decimal('base_commission', 14, 2)
                ->default(0)
                ->comment('Komisi dasar per unit terjual, ditentukan Owner');

            $table->integer('stock')->default(0);

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['owner_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
