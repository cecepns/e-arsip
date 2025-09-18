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
        Schema::create('disposisi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('surat_masuk_id');
            $table->unsignedBigInteger('tujuan_bagian_id');
            $table->text('isi_instruksi');
            $table->string('sifat', 50)->default('Biasa');
            $table->text('catatan')->nullable();
            $table->enum('status', ['Menunggu', 'Dikerjakan', 'Selesai'])->default('Menunggu');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('surat_masuk_id')->references('id')->on('surat_masuk')->onDelete('cascade');
            $table->foreign('tujuan_bagian_id')->references('id')->on('bagian')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Indexes for better performance
            $table->index('surat_masuk_id');
            $table->index('tujuan_bagian_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disposisi');
    }
};
