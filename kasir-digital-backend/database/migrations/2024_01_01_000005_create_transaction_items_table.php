<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Detail item transaksi. SEMUA kolom harga & komisi di sini adalah SNAPSHOT
     * pada saat transaksi terjadi — bukan referensi live ke products/sales_prices.
     * Ini supaya histori transaksi tidak berubah jika harga dasar/komisi/harga
     * jual diubah owner/sales di kemudian hari (lihat aturan bisnis #6 di issue.md).
     *
     * commission_amount = (selling_price_snapshot - base_price_snapshot + base_commission_snapshot) * quantity
     * Bernilai 0 jika transaksi dilakukan oleh Owner (bukan Sales).
     */
    public function up(): void
    {
        Schema::create('transaction_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('transaction_id')
                ->constrained('transactions')
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            $table->string('product_name_snapshot');
            $table->integer('quantity');

            $table->decimal('base_price_snapshot', 14, 2);
            $table->decimal('selling_price_snapshot', 14, 2);
            $table->decimal('base_commission_snapshot', 14, 2)->default(0);

            $table->decimal('subtotal', 14, 2)
                ->comment('selling_price_snapshot * quantity');

            $table->decimal('commission_amount', 14, 2)->default(0)
                ->comment('Komisi yang dihasilkan item ini, 0 jika cashier adalah Owner');

            $table->timestamps();

            $table->index('transaction_id');
            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_items');
    }
};
