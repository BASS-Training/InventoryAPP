<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema};

return new class extends Migration {
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            return;
        }
        Schema::table('item_requests', function (Blueprint $table) {
            $table->enum('status', ['Diajukan', 'Disetujui', 'Ditolak', 'Diproses', 'Dibatalkan', 'Dikembalikan'])->default('Diajukan')->change();
        });
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->enum('tipe_pergerakan', ['masuk', 'keluar', 'koreksi-tambah', 'koreksi-kurang', 'pengembalian'])->change();
        });
    }

    public function down(): void
    {
        // Preserve valid return records when rolling back application features.
    }
};
