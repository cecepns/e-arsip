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
        Schema::table('disposisi', function (Blueprint $table) {
            $table->unsignedBigInteger('surat_keluar_id')->nullable()->after('surat_masuk_id');
            $table->foreign('surat_keluar_id')->references('id')->on('surat_keluar')->onDelete('cascade');
        });

        DB::statement('ALTER TABLE disposisi MODIFY surat_masuk_id BIGINT UNSIGNED NULL');

        DB::statement("
            ALTER TABLE disposisi
            ADD CONSTRAINT disposisi_surat_check
            CHECK (
                (surat_masuk_id IS NOT NULL AND surat_keluar_id IS NULL)
                OR
                (surat_masuk_id IS NULL AND surat_keluar_id IS NOT NULL)
            )
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE disposisi DROP CHECK disposisi_surat_check');

        Schema::table('disposisi', function (Blueprint $table) {
            $table->dropForeign(['surat_keluar_id']);
            $table->dropColumn('surat_keluar_id');
        });

        DB::statement('ALTER TABLE disposisi MODIFY surat_masuk_id BIGINT UNSIGNED NOT NULL');
    }
};


