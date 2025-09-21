<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bagian', function (Blueprint $table) {
            $table->string('kepala_bagian', 100)->nullable()->after('nama_bagian');
            $table->enum('status', ['Aktif', 'Nonaktif'])->default('Aktif')->after('keterangan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bagian', function (Blueprint $table) {
            $table->dropColumn(['kepala_bagian', 'status']);
        });
    }
};
