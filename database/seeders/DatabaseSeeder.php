<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Bagian;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            BagianSeeder::class,
            UserSeeder::class,
            SuratKeluarSeeder::class,
        ]);
    }
}
