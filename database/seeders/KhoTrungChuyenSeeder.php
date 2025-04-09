<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KhoTrungChuyenSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('kho_trung_chuyens')->delete();

        DB::table('kho_trung_chuyens')->truncate();

        DB::table('kho_trung_chuyens')->insert([
            [
                'ten_kho' => 'Kho Hà Nội',
                'tinh_thanh' => 'Hà Nội',
                'dia_chi' => 'KCN Sài Đồng, Phường Phúc Đồng, Quận Long Biên',
                'vi_do' => 21.0381,
                'kinh_do' => 105.9118,
            ],
            [
                'ten_kho' => 'Kho Hải Phòng',
                'tinh_thanh' => 'Hải Phòng',
                'dia_chi' => 'Đường Bạch Đằng, Phường Hạ Lý, Quận Hồng Bàng',
                'vi_do' => 20.8600,
                'kinh_do' => 106.6822,
            ],
            [
                'ten_kho' => 'Kho Quảng Ninh',
                'tinh_thanh' => 'Quảng Ninh',
                'dia_chi' => 'P. Cao Xanh, TP. Hạ Long',
                'vi_do' => 21.0064,
                'kinh_do' => 107.2925,
            ],
            [
                'ten_kho' => 'Kho Bắc Ninh',
                'tinh_thanh' => 'Bắc Ninh',
                'dia_chi' => 'KCN Yên Phong, Huyện Yên Phong',
                'vi_do' => 21.1860,
                'kinh_do' => 106.0764,
            ],
            [
                'ten_kho' => 'Kho Thanh Hóa',
                'tinh_thanh' => 'Thanh Hóa',
                'dia_chi' => 'Phường Đông Hương, TP. Thanh Hóa',
                'vi_do' => 19.8079,
                'kinh_do' => 105.7768,
            ],
            [
                'ten_kho' => 'Kho Nghệ An',
                'tinh_thanh' => 'Nghệ An',
                'dia_chi' => '75 Trường Chinh, Phường Lê Lợi, TP. Vinh',
                'vi_do' => 18.6734,
                'kinh_do' => 105.6929,
            ],
            [
                'ten_kho' => 'Kho Hà Tĩnh',
                'tinh_thanh' => 'Hà Tĩnh',
                'dia_chi' => 'Phường Thạch Linh, TP. Hà Tĩnh',
                'vi_do' => 18.335,
                'kinh_do' => 105.906,
            ],
            [
                'ten_kho' => 'Kho Quảng Bình',
                'tinh_thanh' => 'Quảng Bình',
                'dia_chi' => 'Đường Trần Quang Khải, TP. Đồng Hới',
                'vi_do' => 17.4689,
                'kinh_do' => 106.6223,
            ],
            [
                'ten_kho' => 'Kho Huế',
                'tinh_thanh' => 'Thừa Thiên Huế',
                'dia_chi' => 'Đường Nguyễn Tất Thành, Hương Thủy',
                'vi_do' => 16.4637,
                'kinh_do' => 107.5909,
            ],
            [
                'ten_kho' => 'Kho Đà Nẵng',
                'tinh_thanh' => 'Đà Nẵng',
                'dia_chi' => 'Lô 39, KCN Đà Nẵng, Phường An Hải Bắc, Quận Sơn Trà',
                'vi_do' => 16.0739,
                'kinh_do' => 108.2240,
            ],
            [
                'ten_kho' => 'Kho Quảng Ngãi',
                'tinh_thanh' => 'Quảng Ngãi',
                'dia_chi' => 'TP. Quảng Ngãi',
                'vi_do' => 15.1201,
                'kinh_do' => 108.7923,
            ],
            [
                'ten_kho' => 'Kho Bình Định',
                'tinh_thanh' => 'Bình Định',
                'dia_chi' => 'Xã Hoài Tân, Thị xã Hoài Nhơn',
                'vi_do' => 14.4200,
                'kinh_do' => 109.0000,
            ],
            [
                'ten_kho' => 'Kho Phú Yên',
                'tinh_thanh' => 'Phú Yên',
                'dia_chi' => 'TP. Tuy Hòa',
                'vi_do' => 13.0892,
                'kinh_do' => 109.3095,
            ],
            [
                'ten_kho' => 'Kho Khánh Hòa',
                'tinh_thanh' => 'Khánh Hòa',
                'dia_chi' => 'KCN Suối Dầu, Huyện Cam Lâm',
                'vi_do' => 12.2388,
                'kinh_do' => 109.1967,
            ],
            [
                'ten_kho' => 'Kho Đắk Lắk',
                'tinh_thanh' => 'Đắk Lắk',
                'dia_chi' => 'TP. Buôn Ma Thuột',
                'vi_do' => 12.666,
                'kinh_do' => 108.0378,
            ],
            [
                'ten_kho' => 'Kho Lâm Đồng',
                'tinh_thanh' => 'Lâm Đồng',
                'dia_chi' => 'Phường 8, TP. Đà Lạt',
                'vi_do' => 11.9404,
                'kinh_do' => 108.4583,
            ],
            [
                'ten_kho' => 'Kho TP.HCM',
                'tinh_thanh' => 'TP. Hồ Chí Minh',
                'dia_chi' => 'Ấp 1, Xã Tân Thới Nhì, Huyện Hóc Môn',
                'vi_do' => 10.8911,
                'kinh_do' => 106.5903,
            ],
            [
                'ten_kho' => 'Kho Cần Thơ',
                'tinh_thanh' => 'Cần Thơ',
                'dia_chi' => 'QL1A, Phường Hưng Thạnh, Quận Cái Răng',
                'vi_do' => 10.0159,
                'kinh_do' => 105.7689,
            ],
            [
                'ten_kho' => 'Kho An Giang',
                'tinh_thanh' => 'An Giang',
                'dia_chi' => 'TP. Long Xuyên',
                'vi_do' => 10.385,
                'kinh_do' => 105.435,
            ],
            [
                'ten_kho' => 'Kho Kiên Giang',
                'tinh_thanh' => 'Kiên Giang',
                'dia_chi' => 'TP. Rạch Giá',
                'vi_do' => 9.940,
                'kinh_do' => 105.085,
            ],
        ]);
    }
}
