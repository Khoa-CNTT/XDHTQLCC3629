<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NhaSanXuatSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('nha_san_xuats')->delete();

        DB::table('nha_san_xuats')->truncate();

        DB::table('nha_san_xuats')->insert([
            ['ten_cong_ty' => 'Công ty TNHH ABC', 'loai_doi_tac' =>  'Nhà sản xuất', 'dia_chi' => '123 Đường Lê Lợi, TP.HCM', 'so_dien_thoai' => '0901234567', 'email' => 'abc@company.com','ngay_tao' => '2024-04-13','ngay_cap_nhat' => '2025-01-10'],
            ['ten_cong_ty' => 'Công ty CP XYZ', 'loai_doi_tac' =>  'Nhà sản xuất', 'dia_chi' => '456 Đường Nguyễn Trãi, Hà Nội', 'so_dien_thoai' => '0912345678', 'email' => 'xyz@company.com','ngay_tao' => '2024-04-14','ngay_cap_nhat' => '2025-01-11'],
            ['ten_cong_ty' => 'Tập đoàn Công nghiệp DEF', 'loai_doi_tac' =>  'Nhà sản xuất', 'dia_chi' => '789 Đường Trần Hưng Đạo, Đà Nẵng', 'so_dien_thoai' => '0923456789', 'email' => 'def@company.com','ngay_tao' => '2024-04-15','ngay_cap_nhat' => '2025-01-12'],
            ['ten_cong_ty' => 'Nhà máy GHI Việt Nam', 'loai_doi_tac' =>  'Nhà sản xuất', 'dia_chi' => '321 Đường Lý Thường Kiệt, Bình Dương', 'so_dien_thoai' => '0934567890', 'email' => 'ghi@company.com','ngay_tao' => '2024-04-16','ngay_cap_nhat' => '2025-01-13'],
            ['ten_cong_ty' => 'Công ty TNHH JKL', 'loai_doi_tac' =>  'Nhà sản xuất', 'dia_chi' => '654 Đường Phạm Văn Đồng, Hải Phòng', 'so_dien_thoai' => '0945678901', 'email' => 'jkl@company.com','ngay_tao' => '2024-04-17','ngay_cap_nhat' => '2025-01-14'],
            ['ten_cong_ty' => 'Công ty Sản Xuất MNO', 'loai_doi_tac' =>  'Nhà sản xuất', 'dia_chi' => '987 Đường Hoàng Văn Thụ, Cần Thơ', 'so_dien_thoai' => '0956789012', 'email' => 'mno@company.com','ngay_tao' => '2024-04-18','ngay_cap_nhat' => '2025-01-15'],
            ['ten_cong_ty' => 'Công ty TNHH PQR', 'loai_doi_tac' =>  'Nhà sản xuất', 'dia_chi' => '147 Đường Nguyễn Văn Cừ, Đà Lạt', 'so_dien_thoai' => '0967890123', 'email' => 'pqr@company.com','ngay_tao' => '2024-04-19','ngay_cap_nhat' => '2025-01-16'],
            ['ten_cong_ty' => 'Nhà sản xuất STU', 'loai_doi_tac' =>  'Nhà sản xuất', 'dia_chi' => '258 Đường Võ Thị Sáu, Nha Trang', 'so_dien_thoai' => '0978901234', 'email' => 'stu@company.com','ngay_tao' => '2024-04-20','ngay_cap_nhat' => '2025-01-17'],
            ['ten_cong_ty' => 'Công ty CP VWX', 'loai_doi_tac' =>  'Nhà sản xuất', 'dia_chi' => '369 Đường Quang Trung, Vũng Tàu', 'so_dien_thoai' => '0989012345', 'email' => 'vwx@company.com','ngay_tao' => '2024-04-21','ngay_cap_nhat' => '2025-01-18'],
            ['ten_cong_ty' => 'Tập đoàn YZ Việt Nam', 'loai_doi_tac' =>  'Nhà sản xuất', 'dia_chi' => '741 Đường Điện Biên Phủ, Huế', 'so_dien_thoai' => '0990123456', 'email' => 'yz@company.com','ngay_tao' => '2024-04-22','ngay_cap_nhat' => '2025-01-19'],
        ]);
    }
}
