<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DonViVanChuyenSeeding extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('don_vi_van_chuyens')->delete();

        DB::table('don_vi_van_chuyens')->truncate();

        DB::table('don_vi_van_chuyens')->insert([
            [
                'ten_cong_ty' => 'Công ty Giao Nhận Nhanh 24h',
                'email' =>  'fast24h@gmail.com',
                'password' => bcrypt('123456'),
                'so_dien_thoai' => '0912345678',
                'dia_chi' => '567 Đường Hai Bà Trưng',
                'cuoc_van_chuyen' => '40000',
                'loai_tai_khoan' => 'Đơn vị vận chuyển'
            ],
            [
                'ten_cong_ty' => 'Công ty Giao Hàng Tiết Kiệm',
                'email' =>  'giaohangtietkiem@gmail.com',
                'password' => bcrypt('123456'),
                'so_dien_thoai' => '0922233445',
                'dia_chi' => '789 Đường Nguyễn Huệ',
                'cuoc_van_chuyen' => '25000',
                'loai_tai_khoan' => 'Đơn vị vận chuyển'
            ],
            [
                'ten_cong_ty' => 'Công ty Chuyển Phát Nhanh VN',
                'email' =>  'vnexpress@gmail.com',
                'password' => bcrypt('123456'),
                'so_dien_thoai' => '0933456789',
                'dia_chi' => '456 Đường Trần Hưng Đạo',
                'cuoc_van_chuyen' => '35000',
                'loai_tai_khoan' => 'Đơn vị vận chuyển'
            ],
            [
                'ten_cong_ty' => 'Công ty Vận Tải ABC',
                'email' =>  'abc@gmail.com',
                'password' => bcrypt('123456'),
                'so_dien_thoai' => '0901234567',
                'dia_chi' => '123 Đường Lê Lợi',
                'cuoc_van_chuyen' => '30000',
                'loai_tai_khoan' => 'Đơn vị vận chuyển'
            ],
        ]);
    }
}
