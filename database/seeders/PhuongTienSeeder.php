<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PhuongTienSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('phuong_tiens')->delete();

        DB::table('phuong_tiens')->truncate();

        DB::table('phuong_tiens')->insert([
            ['ma_phuong_tien' => 'PT001', 'ten_phuong_tien' =>  'Xe tải 1.5 tấn'],
            ['ma_phuong_tien' => 'PT002', 'ten_phuong_tien' =>  'Xe tải 3.5 tấn'],
            ['ma_phuong_tien' => 'PT003', 'ten_phuong_tien' =>  'Xe container 20 feet'],
            ['ma_phuong_tien' => 'PT004', 'ten_phuong_tien' =>  'Xe container 40 feet'],
            ['ma_phuong_tien' => 'PT005', 'ten_phuong_tien' =>  'Xe đông lạnh 5 tấn'],
            ['ma_phuong_tien' => 'PT006', 'ten_phuong_tien' =>  'Xe đông lạnh 10 tấn'],
            ['ma_phuong_tien' => 'PT007', 'ten_phuong_tien' =>  'Xe bán tải'],
            ['ma_phuong_tien' => 'PT008', 'ten_phuong_tien' =>  'Xe máy giao hàng'],
            ['ma_phuong_tien' => 'PT009', 'ten_phuong_tien' =>  'Tàu chở hàng'],
            ['ma_phuong_tien' => 'PT010', 'ten_phuong_tien' =>  'Máy bay vận tải'],
        ]);
    }
}
