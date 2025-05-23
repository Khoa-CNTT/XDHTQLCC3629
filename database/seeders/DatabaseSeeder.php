<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call([
            NhaSanXuatSeeder::class,
            DanhMucSanPhamSeeder::class,
            SanPhamSeeder::class,
            ChiTietSanPhamSeeder::class,
            SanPhamNSXSeeder::class,
            NguyenLieuSeeder::class,
            NguyenLieuSanPhamSeeder::class,
            DaiLySeeder::class,
            PhuongTienSeeder::class,
            NhanVienSeeder::class,
            DonViVanChuyenSeeding::class,
            KhoTrungChuyenSeeder::class,
            TinhThanhSeeder::class,
            QuanHuyenSeeder::class,
        ]);
    }
}
