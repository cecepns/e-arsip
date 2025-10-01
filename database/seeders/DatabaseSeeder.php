<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Bagian;
use App\Models\Pengaturan;
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
            PengaturanSeeder::class,
            BagianSeeder::class,
            UserSeeder::class,
            SuratKeluarSeeder::class,
            SuratMasukSeeder::class,
            DisposisiSeeder::class,
        ]);
    }
}
