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
        Schema::table('users', function (Blueprint $table) {
            // Rename name to nama_lengkap
            $table->renameColumn('name', 'nama_lengkap');
            
            // Add new columns
            $table->string('username', 50)->unique()->after('nama_lengkap');
            $table->string('nomor_telepon', 20)->nullable()->after('email');
            $table->string('foto', 255)->nullable()->after('nomor_telepon');
            $table->enum('role', ['Admin', 'Staf'])->default('Staf')->after('foto');
            $table->unsignedBigInteger('bagian_id')->nullable()->after('role');
            
            // Add foreign key constraint (will be added after creating bagian table)
            // $table->foreign('bagian_id')->references('id')->on('bagian')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign key first if exists
            $table->dropForeign(['bagian_id']);
            
            // Drop columns
            $table->dropColumn(['bagian_id', 'role', 'foto', 'nomor_telepon', 'username']);
            
            // Rename back to name
            $table->renameColumn('nama_lengkap', 'name');
        });
    }
};
