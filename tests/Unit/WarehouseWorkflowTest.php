<?php

use App\Models\{Barang, ItemRequest, User};
use Illuminate\Support\Facades\{Artisan, Notification};
use Spatie\Permission\Models\{Permission, Role};

uses(Tests\TestCase::class);

beforeEach(function () {
    $this->withoutMiddleware(Illuminate\Routing\Middleware\ThrottleRequests::class);
    config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
    app('db')->purge('sqlite');
    // Legacy data-adjustment migration requires permission tables first.
    Artisan::call('migrate', ['--path' => 'database/migrations/2025_06_02_014421_create_permission_tables.php', '--force' => true]);
    Artisan::call('migrate', ['--force' => true]);
    foreach (['view-dashboard', 'pengajuan-barang-create', 'pengajuan-barang-list-own',
        'pengajuan-barang-cancel-own', 'pengajuan-barang-list-all', 'pengajuan-barang-approve',
        'pengajuan-barang-process', 'pengajuan-barang-return', 'barang-show'] as $name) {
        Permission::findOrCreate($name, 'web');
    }
    Role::findOrCreate('StafGudang', 'web')->syncPermissions(Permission::all());
    Role::findOrCreate('Admin', 'web')->syncPermissions(Permission::all());
    app(Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    Notification::fake();
    $this->a = User::factory()->create()->assignRole('StafGudang');
    $this->b = User::factory()->create()->assignRole('StafGudang');
    $this->item = Barang::create(['nama_barang' => 'Test laptop', 'stok' => 10, 'status' => 'aktif', 'tipe_item' => 'aset']);
    $this->submission = ItemRequest::create([
        'user_id' => $this->a->id, 'barang_id' => $this->item->id,
        'tipe_pengajuan' => 'peminjaman', 'kuantitas_diminta' => 2,
        'keperluan' => 'Testing', 'status' => 'Diajukan',
        'tanggal_pengambilan' => today(), 'jatuh_tempo_pengembalian' => today()->addDays(7),
    ]);
});

test('officer cannot handle own request even through direct endpoints', function () {
    foreach (['StafGudang', 'Admin'] as $role) {
        $this->a->syncRoles([$role]);
        $this->actingAs($this->a);
        foreach (['approve', 'reject', 'process'] as $action) {
            $this->post(route('admin.pengajuan.barang.' . $action, $this->submission), [])->assertForbidden();
        }
        $this->put(route('admin.pengajuan.barang.return', $this->submission), [])->assertForbidden();
    }
    expect($this->submission->fresh()->status)->toBe('Diajukan');
    expect((int) $this->item->fresh()->stok)->toBe(10);
});

test('another officer can approve hand over and accept a return', function () {
    $this->actingAs($this->b);
    $this->post(route('admin.pengajuan.barang.approve', $this->submission), ['kuantitas_disetujui' => 2, 'catatan_approval' => null])->assertSessionHasNoErrors();
    expect($this->submission->fresh()->status)->toBe('Disetujui');
    $this->post(route('admin.pengajuan.barang.process', $this->submission), ['catatan_pemroses' => null])->assertSessionHasNoErrors();
    expect($this->submission->fresh()->status)->toBe('Diproses');
    expect((int) $this->item->fresh()->stok)->toBe(8);
    $this->put(route('admin.pengajuan.barang.return', $this->submission), ['catatan_pengembalian' => null])->assertSessionHasNoErrors();
    expect($this->submission->fresh()->status)->toBe('Dikembalikan');
    expect((int) $this->item->fresh()->stok)->toBe(10);
});

test('staff can submit both request types and see operational dashboard', function () {
    $this->actingAs($this->a);
    foreach (['permintaan' => 'habis_pakai', 'peminjaman' => 'aset'] as $type => $itemType) {
        $this->item->update(['tipe_item' => $itemType]);
        $this->get(route('pengajuan.barang.create', $type))->assertOk();
        $this->post(route('pengajuan.barang.store'), [
            'barang_id' => $this->item->id, 'tipe_pengajuan' => $type,
            'kuantitas_diminta' => 1, 'keperluan' => 'Test staff request',
            'tanggal_pengambilan' => today()->toDateString(),
            'jatuh_tempo_pengembalian' => today()->addDays(7)->toDateString(),
        ])->assertRedirect(route('pengajuan.barang.index'))->assertSessionHasNoErrors();
    }
    $response = $this->get(route('dashboard'))->assertOk()->assertViewIs('dashboard-operations');
    expect($response->viewData('dashboardData')['queue'])->toHaveCount(0);
    expect($response->viewData('dashboardData')['months'])->toHaveCount(6);
    $this->actingAs($this->b)->get(route('dashboard'))->assertOk();
    $this->b->syncRoles(['Admin']);
    $this->get(route('dashboard'))->assertOk()->assertViewIs('dashboard-operations');
});
