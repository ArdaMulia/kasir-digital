<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Daftar permission dasar sesuai pembagian akses di issue.md (bagian 5 & 6).
     * Bisa ditambah/disesuaikan saat controller per modul dibuat di Fase 2+.
     */
    private array $ownerPermissions = [
        'manage-products',
        'manage-sales-accounts',
        'manage-commission-base',
        'view-all-transactions',
        'view-reports',
        'create-transaction',
    ];

    private array $salesPermissions = [
        'manage-own-selling-price',
        'create-transaction',
        'view-own-transactions',
    ];

    public function run(): void
    {
        // Reset cache permission Spatie
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (array_unique([...$this->ownerPermissions, ...$this->salesPermissions]) as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $ownerRole = Role::firstOrCreate(['name' => User::ROLE_OWNER, 'guard_name' => 'web']);
        $ownerRole->syncPermissions($this->ownerPermissions);

        $salesRole = Role::firstOrCreate(['name' => User::ROLE_SALES, 'guard_name' => 'web']);
        $salesRole->syncPermissions($this->salesPermissions);
    }
}
