<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            // MySQL needs a raw statement to alter an enum. SQLite stores this
            // column as text, so there is no equivalent schema change to run.
            DB::statement("ALTER TABLE stock_movements MODIFY COLUMN tipe_pergerakan ENUM('masuk', 'keluar', 'koreksi-tambah', 'koreksi-kurang') NOT NULL COMMENT 'Jenis pergerakan stok'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE stock_movements MODIFY COLUMN tipe_pergerakan ENUM('masuk', 'keluar') NOT NULL COMMENT 'Jenis pergerakan stok'");
        }
    }
};
