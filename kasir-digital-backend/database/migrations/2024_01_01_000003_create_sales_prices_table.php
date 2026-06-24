<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Harga jual yang ditetapkan masing-masing Sales untuk sebuah Produk.
     * Satu kombinasi (product_id, sales_id) hanya boleh punya satu harga jual aktif.
     * Validasi selling_price >= products.base_price dilakukan di level aplikasi (Fase 2).
     */
    public function up(): void
    {
        Schema::create('sales_prices', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            $table->foreignId('sales_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->decimal('selling_price', 14, 2)
                ->comment('Harga jual yang ditetapkan sales, harus >= base_price produk');

            $table->timestamps();

            $table->unique(['product_id', 'sales_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_prices');
    }
};
