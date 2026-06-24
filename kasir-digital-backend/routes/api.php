<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Kasir Digital
|--------------------------------------------------------------------------
|
| Skeleton untuk Fase 1. Endpoint nyata (controller, request validation,
| resource) ditambahkan di Fase 2 (Transaksi & Komisi), Fase 3 (Mobile/Sales),
| dan Fase 4 (Admin Panel/Owner) sesuai roadmap di issue.md.
|
*/

// ---------------------------------------------------------------------
// Auth (Sanctum) — login/logout, dipakai Mobile App & Admin Panel
// ---------------------------------------------------------------------
Route::prefix('auth')->group(function () {
    // Route::post('/login', [AuthController::class, 'login']);
    // Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    // Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');
});

// ---------------------------------------------------------------------
// Owner — manajemen produk, sales, komisi dasar, laporan (Admin Panel)
// ---------------------------------------------------------------------
Route::middleware(['auth:sanctum', 'role:owner'])->prefix('owner')->group(function () {
    // Route::apiResource('products', ProductController::class);
    // Route::apiResource('sales', SalesAccountController::class);
    // Route::get('/reports/commission', [CommissionReportController::class, 'index']);
    // Route::get('/dashboard', [DashboardController::class, 'index']);
});

// ---------------------------------------------------------------------
// Sales — atur harga jual sendiri, transaksi, riwayat & estimasi komisi
// ---------------------------------------------------------------------
Route::middleware(['auth:sanctum', 'role:sales'])->prefix('sales')->group(function () {
    // Route::apiResource('selling-prices', SalesPriceController::class);
    // Route::post('/transactions', [TransactionController::class, 'store']);
    // Route::get('/transactions', [TransactionController::class, 'index']);
    // Route::get('/commissions', [SalesCommissionController::class, 'index']);
});

// ---------------------------------------------------------------------
// Bersama (Owner & Sales) — transaksi bisa dilakukan keduanya (lihat aturan #5)
// ---------------------------------------------------------------------
Route::middleware(['auth:sanctum'])->group(function () {
    // Route::apiResource('transactions', TransactionController::class)->only(['index', 'show', 'store']);
});
