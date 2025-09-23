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
            // Drop unused columns that are not in the project requirement
            $table->dropColumn([
                'nama_lengkap',
                'nomor_telepon', 
                'foto',
                'email_verified_at',
                'remember_token'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add back the columns if rollback is needed
            $table->string('nama_lengkap')->after('id');
            $table->string('nomor_telepon', 20)->nullable()->after('email');
            $table->string('foto', 255)->nullable()->after('nomor_telepon');
            $table->timestamp('email_verified_at')->nullable()->after('email');
            $table->rememberToken()->after('updated_at');
        });
    }
};
