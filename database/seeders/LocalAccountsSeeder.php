<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class LocalAccountsSeeder extends Seeder
{
    public function run(): void
    {
        $adminPassword = env('LOCAL_ADMIN_PASSWORD');
        $viewerPassword = env('LOCAL_VIEWER_PASSWORD');

        if (!$adminPassword || !$viewerPassword) {
            throw new \RuntimeException('Set LOCAL_ADMIN_PASSWORD and LOCAL_VIEWER_PASSWORD before running this local-only seeder.');
        }

        $permissions = [
            'barang-list', 'barang-create', 'barang-edit', 'barang-delete', 'barang-show',
            'kategori-list', 'kategori-create', 'kategori-edit', 'kategori-delete',
            'unit-list', 'unit-create', 'unit-edit', 'unit-delete',
            'lokasi-list', 'lokasi-create', 'lokasi-edit', 'lokasi-delete',
            'stok-pergerakan-list', 'stok-masuk-create', 'stok-keluar-create', 'stok-koreksi',
            'user-list', 'user-create', 'user-edit', 'user-delete',
            'role-permission-manage', 'view-audit-trail', 'maintenance-manage',
            'pengajuan-barang-list-own', 'pengajuan-barang-create', 'pengajuan-barang-cancel-own',
            'pengajuan-barang-list-all', 'pengajuan-barang-approve', 'pengajuan-barang-process',
            'pengajuan-barang-return', 'view-dashboard', 'view-laporan-stok',
            'view-laporan-barang-masuk', 'view-laporan-barang-keluar', 'view-laporan-maintenance',
        ];

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $adminRole = Role::findOrCreate('Admin', 'web');
        $adminRole->syncPermissions(Permission::all());

        $staffRole = Role::findOrCreate('StafGudang', 'web');
        $staffRole->syncPermissions([
            'barang-list', 'barang-create', 'barang-edit', 'barang-show',
            'kategori-list', 'kategori-create', 'unit-list', 'unit-create',
            'lokasi-list', 'lokasi-create', 'stok-pergerakan-list',
            'stok-masuk-create', 'stok-keluar-create', 'stok-koreksi',
            'pengajuan-barang-list-own', 'pengajuan-barang-create',
            'pengajuan-barang-cancel-own', 'pengajuan-barang-list-all',
            'pengajuan-barang-approve', 'pengajuan-barang-process',
            'pengajuan-barang-return', 'view-dashboard', 'view-laporan-stok',
            'view-laporan-barang-masuk', 'view-laporan-barang-keluar',
            'view-laporan-maintenance', 'maintenance-manage',
        ]);

        $viewerRole = Role::findOrCreate('Viewer', 'web');
        $viewerRole->syncPermissions([
            'barang-list', 'barang-show', 'kategori-list', 'unit-list', 'lokasi-list',
            'stok-pergerakan-list', 'pengajuan-barang-list-own', 'pengajuan-barang-create',
            'pengajuan-barang-cancel-own', 'view-dashboard',
        ]);

        $admin = User::updateOrCreate(
            ['email' => 'local-admin@inventory.test'],
            ['name' => 'Local Admin', 'password' => Hash::make($adminPassword), 'email_verified_at' => now()]
        );
        $admin->syncRoles([$adminRole]);

        $viewer = User::updateOrCreate(
            ['email' => 'local-viewer@inventory.test'],
            ['name' => 'Local Viewer', 'password' => Hash::make($viewerPassword), 'email_verified_at' => now()]
        );
        $viewer->syncRoles([$viewerRole]);
    }
}
