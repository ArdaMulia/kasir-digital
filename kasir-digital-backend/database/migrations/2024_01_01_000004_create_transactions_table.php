<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Header transaksi. cashier_id adalah user yang melakukan transaksi
     * (bisa Owner atau Sales). owner_id disimpan terdenormalisasi agar query
     * "semua transaksi milik owner X" tidak perlu join berlapis.
     *
     * Komisi HANYA dihitung jika cashier_id adalah role Sales (lihat catatan
     * aturan bisnis #5 di issue.md). Jika cashier adalah Owner, total_commission = 0.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            $table->string('transaction_code')->unique();

            $table->foreignId('cashier_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->comment('User yang melakukan transaksi: Owner atau Sales');

            $table->foreignId('owner_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->comment('Denormalisasi: owner pemilik data transaksi ini');

            $table->decimal('total_amount', 14, 2)->default(0);
            $table->decimal('total_commission', 14, 2)->default(0);

            $table->string('payment_method')->default('cash')
                ->comment('cash, midtrans, dll (opsional di Fase 6)');

            $table->string('status')->default('completed')
                ->comment('pending, completed, cancelled, refunded');

            $table->timestamp('paid_at')->nullable();

            $table->timestamps();

            $table->index(['owner_id', 'created_at']);
            $table->index(['cashier_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
