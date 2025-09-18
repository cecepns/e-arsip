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
        Schema::create('lampiran', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('surat_id');
            $table->enum('tipe_surat', ['masuk', 'keluar']);
            $table->string('nama_file', 255);
            $table->string('path_file', 255);
            $table->enum('tipe_lampiran', ['utama', 'pendukung'])->default('utama');
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['surat_id', 'tipe_surat']);
            $table->index('tipe_lampiran');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lampiran');
    }
};
