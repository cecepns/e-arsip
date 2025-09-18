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
        Schema::create('surat_masuk', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat', 100);
            $table->date('tanggal_surat');
            $table->date('tanggal_terima');
            $table->string('perihal', 255);
            $table->text('ringkasan_isi')->nullable();
            $table->string('pengirim', 150);
            $table->string('sifat_surat', 50)->default('Biasa');
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('tujuan_bagian_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('tujuan_bagian_id')->references('id')->on('bagian')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Indexes for better performance
            $table->index('nomor_surat');
            $table->index('tanggal_surat');
            $table->index('tanggal_terima');
            $table->index('pengirim');
            $table->index('tujuan_bagian_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_masuk');
    }
};
