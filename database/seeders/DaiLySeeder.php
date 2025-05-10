<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DaiLySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('dai_lies')->delete();

        DB::table('dai_lies')->truncate();

        DB::table('dai_lies')->insert([
            [
                'ten_cong_ty' => 'Công ty TNHH Thương Mại Hòa Bình',
                'email' =>  'hoabinh@gmail.com',
                'password' => bcrypt('123456'),
                'dia_chi' => '123 Đường Lê Lợi, Hà Nội',
                'so_dien_thoai' => '0987654321',
                'loai_tai_khoan' => 'Đại Lý',
                'vi_do' => 21.028511,
                'kinh_do' => 105.804817,
                'dia_chi_vi' => 'TA9yodjyC7YpDJSxLZF9mntnZGMaG8Eitb'
            ],
            [
                'ten_cong_ty' => 'Công ty Cổ Phần Nam Á',
                'email' =>  'nama@gmail.com',
                'password' => bcrypt('123456'),
                'dia_chi' => '45 Đường Trần Hưng Đạo, TP.HCM',
                'so_dien_thoai' => '0978123456',
                'loai_tai_khoan' => 'Đại Lý',
                'vi_do' => 10.8911,
                'kinh_do' => 106.5903,
                'dia_chi_vi' => '',
            ],
            [
                'ten_cong_ty' => 'Công ty TNHH Minh Phát',
                'email' =>  'minhphat@gmail.com',
                'password' => bcrypt('123456'),
                'dia_chi' => '90 Đường Nguyễn Văn Linh, Đà Nẵng',
                'so_dien_thoai' => '0967234567',
                'loai_tai_khoan' => 'Đại Lý',
                'vi_do' => 16.0739,
                'kinh_do' => 108.2240,
                'dia_chi_vi' => '',
            ],
            [
                'ten_cong_ty' => 'Công ty Cổ Phần Đại Lộc',
                'email' =>  'dailoc@gmail.com',
                'password' => bcrypt('123456'),
                'dia_chi' => '50 Đường Lý Thường Kiệt, Hải Phòng',
                'so_dien_thoai' => '0956123789',
                'loai_tai_khoan' => 'Đại Lý',
                'vi_do' => 20.8600,
                'kinh_do' => 106.6822,
                'dia_chi_vi' => '',
            ],
            [
                'ten_cong_ty' => 'Công ty TNHH Quốc Tế An Bình',
                'email' =>  'anbinha@gmail.com',
                'password' => bcrypt('123456'),
                'dia_chi' => '75 Đường Nguyễn Huệ, Hà Nội',
                'so_dien_thoai' => '0945789123',
                'loai_tai_khoan' => 'Đại Lý',
                'vi_do' => 21.0381,
                'kinh_do' => 105.9118,
                'dia_chi_vi' => '',
            ],
            [
                'ten_cong_ty' => 'Công ty TNHH Thịnh Vượng',
                'email' =>  'thinhvuong@gmail.com',
                'password' => bcrypt('123456'),
                'dia_chi' => '120 Đường Cách Mạng Tháng 8, TP.HCM',
                'so_dien_thoai' => '0934678921',
                'loai_tai_khoan' => 'Đại Lý',
                'vi_do' => 10.8911,
                'kinh_do' => 106.5903,
                'dia_chi_vi' => '',
            ],
            [
                'ten_cong_ty' => 'Công ty Cổ Phần Ánh Dương',
                'email' =>  'anhduong@gmail.com',
                'password' => bcrypt('123456'),
                'dia_chi' => '88 Đường Võ Văn Kiệt, Đà Nẵng',
                'so_dien_thoai' => '0923567892',
                'loai_tai_khoan' => 'Đại Lý',
                'vi_do' => 16.0739,
                'kinh_do' => 108.2240,
                'dia_chi_vi' => '',
            ],
            [
                'ten_cong_ty' => 'Công ty TNHH Phát Đạt',
                'email' =>  'phatdat@gmail.com',
                'password' => bcrypt('123456'),
                'dia_chi' => '37 Đường Quang Trung, Hải Phòng',
                'so_dien_thoai' => '0912345678',
                'loai_tai_khoan' => 'Đại Lý',
                'vi_do' => 20.8600,
                'kinh_do' => 106.6822,
                'dia_chi_vi' => '',
            ],
            [
                'ten_cong_ty' => 'Công ty TNHH Phúc An',
                'email' =>  'phucan@gmail.com',
                'password' => bcrypt('123456'),
                'dia_chi' => '62 Đường Hoàng Diệu, Hà Nội',
                'so_dien_thoai' => '0901234567',
                'loai_tai_khoan' => 'Đại Lý',
                'vi_do' => 21.0381,
                'kinh_do' => 105.9118,
                'dia_chi_vi' => '',
            ],
            [
                'ten_cong_ty' => 'Công ty Cổ Phần Tấn Tài',
                'email' =>  'tantai@gmail.com',
                'password' => bcrypt('123456'),
                'dia_chi' => '110 Đường Nguyễn Trãi, TP.HCM',
                'so_dien_thoai' => '0988765432',
                'loai_tai_khoan' => 'Đại Lý',
                'vi_do' => 10.8911,
                'kinh_do' => 106.5903,
                'dia_chi_vi' => '',
            ],
            [
                'ten_cong_ty' => 'Công ty TNHH Việt Hưng',
                'email' =>  'viethung@gmail.com',
                'password' => bcrypt('123456'),
                'dia_chi' => '99 Đường Hùng Vương, Hà Nội',
                'so_dien_thoai' => '0977555444',
                'loai_tai_khoan' => 'Đại Lý',
                'vi_do' => 21.0381,
                'kinh_do' => 105.9118,
                'dia_chi_vi' => '',
            ],
            [
                'ten_cong_ty' => 'Công ty TNHH Lộc Phát',
                'email' =>  'locphat@gmail.com',
                'password' => bcrypt('123456'),
                'dia_chi' => '66 Đường Phạm Văn Đồng, TP.HCM',
                'so_dien_thoai' => '0967443322',
                'loai_tai_khoan' => 'Đại Lý',
                'vi_do' => 10.8911,
                'kinh_do' => 106.5903,
                'dia_chi_vi' => '',
            ],
            [
                'ten_cong_ty' => 'Công ty Cổ Phần Thành Đạt',
                'email' =>  'thanhdat@gmail.com',
                'password' => bcrypt('123456'),
                'dia_chi' => '77 Đường Điện Biên Phủ, Đà Nẵng',
                'so_dien_thoai' => '0955332211',
                'loai_tai_khoan' => 'Đại Lý',
                'vi_do' => 16.0739,
                'kinh_do' => 108.2240,
                'dia_chi_vi' => '',
            ],
            [
                'ten_cong_ty' => 'Công ty TNHH Bảo Long',
                'email' =>  'baolong@gmail.com',
                'password' => bcrypt('123456'),
                'dia_chi' => '134 Đường 3/2, Hải Phòng',
                'so_dien_thoai' => '0944221100',
                'loai_tai_khoan' => 'Đại Lý',
                'vi_do' => 20.8600,
                'kinh_do' => 106.6822,
                'dia_chi_vi' => '',
            ],
        ]);
    }
}
