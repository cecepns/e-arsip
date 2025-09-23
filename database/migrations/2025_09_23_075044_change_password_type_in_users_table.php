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
            // Change password column from VARCHAR(255) to VARCHAR(50) 
            // because password will not be hashed (plain text)
            $table->string('password', 50)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Change back to VARCHAR(255) for hashed passwords
            $table->string('password', 255)->change();
        });
    }
};
