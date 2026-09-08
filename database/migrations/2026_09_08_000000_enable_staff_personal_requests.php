<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration {
    public function up(): void
    {
        $role = Role::findOrCreate('StafGudang', 'web');
        foreach (['pengajuan-barang-create', 'pengajuan-barang-list-own', 'pengajuan-barang-cancel-own'] as $permission) {
            $role->givePermissionTo(Permission::findOrCreate($permission, 'web'));
        }
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        // Preserve existing grants; they may predate this migration.
    }
};
