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
            // Add new foreign key column for kepala bagian
            $table->unsignedBigInteger('kepala_bagian_user_id')->nullable()->after('nama_bagian');
            $table->foreign('kepala_bagian_user_id')->references('id')->on('users')->onDelete('set null');
        });

        // Migrate existing data: sync kepala_bagian string with users.is_kepala_bagian
        $this->migrateExistingData();

        // Remove old columns after data migration
        Schema::table('bagian', function (Blueprint $table) {
            $table->dropColumn('kepala_bagian');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_kepala_bagian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore old columns
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_kepala_bagian')->default(false)->after('role');
        });

        Schema::table('bagian', function (Blueprint $table) {
            $table->string('kepala_bagian', 100)->nullable()->after('nama_bagian');
        });

        // Restore data from new relation to old columns
        $this->restoreOldData();

        // Remove new foreign key column
        Schema::table('bagian', function (Blueprint $table) {
            $table->dropForeign(['kepala_bagian_user_id']);
            $table->dropColumn('kepala_bagian_user_id');
        });
    }

    /**
     * Migrate existing data from old structure to new structure
     */
    private function migrateExistingData(): void
    {
        // Get all bagian with kepala_bagian
        $bagianList = \DB::table('bagian')->whereNotNull('kepala_bagian')->get();

        foreach ($bagianList as $bagian) {
            // Find user with matching username in the same bagian
            $user = \DB::table('users')
                ->where('bagian_id', $bagian->id)
                ->where('username', 'like', "%{$bagian->kepala_bagian}%")
                ->first();

            if ($user) {
                // Update bagian with user ID
                \DB::table('bagian')
                    ->where('id', $bagian->id)
                    ->update(['kepala_bagian_user_id' => $user->id]);
            }
        }

        // Also handle users with is_kepala_bagian = true
        $kepalaBagianUsers = \DB::table('users')
            ->where('is_kepala_bagian', true)
            ->whereNotNull('bagian_id')
            ->get();

        foreach ($kepalaBagianUsers as $user) {
            // Update bagian with this user as kepala bagian
            \DB::table('bagian')
                ->where('id', $user->bagian_id)
                ->update(['kepala_bagian_user_id' => $user->id]);
        }
    }

    /**
     * Restore old data structure (for rollback)
     */
    private function restoreOldData(): void
    {
        // Get all bagian with kepala_bagian_user_id
        $bagianList = \DB::table('bagian')
            ->join('users', 'bagian.kepala_bagian_user_id', '=', 'users.id')
            ->select('bagian.id', 'users.username')
            ->get();

        foreach ($bagianList as $bagian) {
            // Update bagian with username
            \DB::table('bagian')
                ->where('id', $bagian->id)
                ->update(['kepala_bagian' => $bagian->username]);

            // Update user with is_kepala_bagian = true
            \DB::table('users')
                ->where('id', $bagian->id)
                ->update(['is_kepala_bagian' => true]);
        }
    }
};
