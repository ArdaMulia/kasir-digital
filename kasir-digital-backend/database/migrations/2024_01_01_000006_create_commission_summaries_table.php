<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel agregasi komisi per sales per periode. Bersifat OPSIONAL —
     * digunakan untuk mempercepat dashboard/laporan (diisi oleh Job di Horizon,
     * lihat Modul H di issue.md), bukan sumber kebenaran utama. Sumber kebenaran
     * tetap transaction_items.commission_amount.
     */
    public function up(): void
    {
        Schema::create('commission_summaries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sales_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('owner_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->date('period_start');
            $table->date('period_end');

            $table->decimal('total_commission', 14, 2)->default(0);
            $table->integer('total_transactions')->default(0);
            $table->integer('total_items_sold')->default(0);

            $table->timestamps();

            $table->unique(['sales_id', 'period_start', 'period_end']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commission_summaries');
    }
};
