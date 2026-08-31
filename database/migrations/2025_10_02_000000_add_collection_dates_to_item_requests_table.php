<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('item_requests', function (Blueprint $table) {
            $table->date('tanggal_pengambilan')->nullable()->after('tanggal_dibutuhkan');
            $table->date('jatuh_tempo_pengembalian')->nullable()->after('tanggal_pengambilan');
        });
    }

    public function down(): void
    {
        Schema::table('item_requests', function (Blueprint $table) {
            $table->dropColumn(['tanggal_pengambilan', 'jatuh_tempo_pengembalian']);
        });
    }
};
