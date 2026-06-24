# Kasir Digital — Backend (Fase 1: Fondasi)

Paket ini berisi **overlay** (file tambahan/pengganti) untuk Fase 1 dari `issue.md` / Issue #1:
struktur project, migration, dan model untuk backend Laravel 12.

> Catatan: paket ini **bukan** instalasi Laravel yang lengkap (vendor/composer dependencies tidak
> disertakan). Ikuti langkah di bawah untuk membuat project Laravel baru lalu menimpa/menambah
> file dengan yang ada di paket ini.

---

## 1. Buat Project Laravel 12 Baru

```bash
composer create-project laravel/laravel:^12.0 kasir-digital-backend
cd kasir-digital-backend
```

## 2. Install Dependency Sesuai issue.md

```bash
composer require laravel/sanctum
composer require laravel/reverb
composer require laravel/horizon
composer require spatie/laravel-permission
composer require predis/predis
```

## 3. Salin File Overlay

Salin seluruh isi folder `app/`, `database/`, dan `routes/` dari paket ini ke dalam project
Laravel yang baru dibuat (timpa file yang sudah ada bila ada konflik, seperti
`database/migrations/0001_01_01_000000_create_users_table.php` yang akan kita modifikasi).

```bash
cp -r app/Models/*       kasir-digital-backend/app/Models/
cp -r app/Enums/*        kasir-digital-backend/app/Enums/
cp -r database/migrations/* kasir-digital-backend/database/migrations/
cp -r database/seeders/* kasir-digital-backend/database/seeders/
cp routes/api.php        kasir-digital-backend/routes/api.php
```

> Karena bawaan Laravel 12 sudah punya migration `..._create_users_table.php`,
> **hapus dulu migration users bawaan**, lalu pakai migration users versi kami
> (`..._create_users_table.php` di paket ini) yang sudah ditambah kolom `owner_id` dan `is_active`.

> **Catatan migration Spatie Permission:** paket ini menyertakan
> `2024_01_01_000001_create_permission_tables.php` (migration standar Spatie) agar urutan
> migration tetap benar (setelah users, sebelum products). Jika Anda sudah menjalankan
> `vendor:publish` untuk Spatie dan filename-nya berbeda/duplikat, hapus salah satu supaya
> tidak terjadi "table already exists".

## 4. Publish Config Package

```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan vendor:publish --provider="Laravel\Horizon\HorizonServiceProvider"
php artisan reverb:install
```

## 5. Konfigurasi `.env`

Salin pengaturan dari `.env.example` pada paket ini ke `.env` project Anda. Poin penting:

- `DB_CONNECTION=mysql` (MySQL — webserver tidak mendukung PostgreSQL, lihat catatan perubahan setup database)
- `QUEUE_CONNECTION=redis`
- `CACHE_STORE=redis`
- `SESSION_DRIVER=redis` (opsional, atau `database`)
- Variabel `REVERB_*` (host, port, app key/secret) — diisi sesuai `reverb:install`

## 6. Jalankan Migration & Seeder

```bash
php artisan migrate
php artisan db:seed --class=RolePermissionSeeder
```

## 7. Struktur yang Disertakan di Fase 1 Ini

### Migration (`database/migrations/`)
| File | Keterangan |
|---|---|
| `..._create_users_table.php` | Tabel user, ditambah `owner_id` (self-reference) & `is_active` |
| `..._create_permission_tables.php` | Tabel role & permission (Spatie, standar) |
| `..._create_products_table.php` | Produk milik Owner: `base_price`, `base_commission`, `stock` |
| `..._create_sales_prices_table.php` | Harga jual per Sales per Produk |
| `..._create_transactions_table.php` | Header transaksi |
| `..._create_transaction_items_table.php` | Detail transaksi (snapshot harga & komisi) |
| `..._create_commission_summaries_table.php` | Tabel agregasi komisi (opsional, untuk laporan) |

### Model (`app/Models/`)
- `User.php` — relasi owner ⇄ sales, role helper (`isOwner()`, `isSales()`)
- `Product.php`
- `SalesPrice.php`
- `Transaction.php`
- `TransactionItem.php` — termasuk method bantu `calculateCommission()` (placeholder logika komisi, akan dipakai penuh oleh Service Layer di Fase 2)
- `CommissionSummary.php`

### Seeder (`database/seeders/`)
- `RolePermissionSeeder.php` — membuat role `owner` & `sales` beserta permission dasar.

### Routes (`routes/api.php`)
- Skeleton route group `auth`, `owner`, `sales` (masih kosong/comment, diisi di Fase 2 saat controller dibuat).

---

## 8. Yang BELUM Dikerjakan (Fase Selanjutnya)
- Controller, Request Validation, Resource/Transformer.
- Service layer kalkulasi komisi (logika penuh).
- Job & Queue (Horizon) untuk proses komisi async.
- Event & Broadcasting (Reverb) untuk notifikasi realtime.
- Endpoint Auth (Sanctum) dan middleware permission per route.

Lanjutkan ke **Fase 2: Transaksi & Komisi** setelah struktur ini di-review dan migration berhasil dijalankan.
