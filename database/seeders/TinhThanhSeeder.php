<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TinhThanhSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tinh_thanhs')->delete();

        DB::table('tinh_thanhs')->truncate();

        DB::table('tinh_thanhs')->insert([
            ['ten_tinh_thanh' => 'An Giang', 'vi_do' => 10.5216, 'kinh_do' => 105.1259],
            ['ten_tinh_thanh' => 'Bà Rịa - Vũng Tàu', 'vi_do' => 10.5410, 'kinh_do' => 107.2428],
            ['ten_tinh_thanh' => 'Bạc Liêu', 'vi_do' => 9.2516, 'kinh_do' => 105.5136],
            ['ten_tinh_thanh' => 'Bắc Giang', 'vi_do' => 21.2731, 'kinh_do' => 106.1946],
            ['ten_tinh_thanh' => 'Bắc Kạn', 'vi_do' => 22.1470, 'kinh_do' => 105.8348],
            ['ten_tinh_thanh' => 'Bắc Ninh', 'vi_do' => 21.1861, 'kinh_do' => 106.0764],
            ['ten_tinh_thanh' => 'Bến Tre', 'vi_do' => 10.2434, 'kinh_do' => 106.3758],
            ['ten_tinh_thanh' => 'Bình Dương', 'vi_do' => 11.3254, 'kinh_do' => 106.4770],
            ['ten_tinh_thanh' => 'Bình Định', 'vi_do' => 13.7820, 'kinh_do' => 109.2190],
            ['ten_tinh_thanh' => 'Bình Phước', 'vi_do' => 11.7512, 'kinh_do' => 106.7235],
            ['ten_tinh_thanh' => 'Bình Thuận', 'vi_do' => 11.0904, 'kinh_do' => 108.0721],
            ['ten_tinh_thanh' => 'Cà Mau', 'vi_do' => 9.1768, 'kinh_do' => 105.1524],
            ['ten_tinh_thanh' => 'Cần Thơ', 'vi_do' => 10.0452, 'kinh_do' => 105.7469],
            ['ten_tinh_thanh' => 'Cao Bằng', 'vi_do' => 22.6650, 'kinh_do' => 106.2570],
            ['ten_tinh_thanh' => 'Đà Nẵng', 'vi_do' => 16.0471, 'kinh_do' => 108.2062],
            ['ten_tinh_thanh' => 'Đắk Lắk', 'vi_do' => 12.6667, 'kinh_do' => 108.0500],
            ['ten_tinh_thanh' => 'Đắk Nông', 'vi_do' => 12.2644, 'kinh_do' => 107.6098],
            ['ten_tinh_thanh' => 'Điện Biên', 'vi_do' => 21.6268, 'kinh_do' => 103.1589],
            ['ten_tinh_thanh' => 'Đồng Nai', 'vi_do' => 10.9453, 'kinh_do' => 106.8246],
            ['ten_tinh_thanh' => 'Đồng Tháp', 'vi_do' => 10.4930, 'kinh_do' => 105.6882],
            ['ten_tinh_thanh' => 'Gia Lai', 'vi_do' => 13.8079, 'kinh_do' => 108.1098],
            ['ten_tinh_thanh' => 'Hà Giang', 'vi_do' => 22.8233, 'kinh_do' => 104.9836],
            ['ten_tinh_thanh' => 'Hà Nam', 'vi_do' => 20.5830, 'kinh_do' => 105.9229],
            ['ten_tinh_thanh' => 'Hà Nội', 'vi_do' => 21.0285, 'kinh_do' => 105.8542],
            ['ten_tinh_thanh' => 'Hà Tĩnh', 'vi_do' => 18.3559, 'kinh_do' => 105.8875],
            ['ten_tinh_thanh' => 'Hải Dương', 'vi_do' => 20.9373, 'kinh_do' => 106.3146],
            ['ten_tinh_thanh' => 'Hải Phòng', 'vi_do' => 20.8651, 'kinh_do' => 106.6838],
            ['ten_tinh_thanh' => 'Hậu Giang', 'vi_do' => 9.7579, 'kinh_do' => 105.6410],
            ['ten_tinh_thanh' => 'Hòa Bình', 'vi_do' => 20.8170, 'kinh_do' => 105.3376],
            ['ten_tinh_thanh' => 'Hồ Chí Minh', 'vi_do' => 10.7626, 'kinh_do' => 106.6602],
            ['ten_tinh_thanh' => 'Hưng Yên', 'vi_do' => 20.6460, 'kinh_do' => 106.0511],
            ['ten_tinh_thanh' => 'Khánh Hòa', 'vi_do' => 12.2585, 'kinh_do' => 109.0526],
            ['ten_tinh_thanh' => 'Kiên Giang', 'vi_do' => 10.0287, 'kinh_do' => 105.2179],
            ['ten_tinh_thanh' => 'Kon Tum', 'vi_do' => 14.3490, 'kinh_do' => 107.9846],
            ['ten_tinh_thanh' => 'Lai Châu', 'vi_do' => 22.3964, 'kinh_do' => 103.4581],
            ['ten_tinh_thanh' => 'Lâm Đồng', 'vi_do' => 11.9404, 'kinh_do' => 108.4583],
            ['ten_tinh_thanh' => 'Lạng Sơn', 'vi_do' => 21.8528, 'kinh_do' => 106.7615],
            ['ten_tinh_thanh' => 'Lào Cai', 'vi_do' => 22.4853, 'kinh_do' => 103.9707],
            ['ten_tinh_thanh' => 'Long An', 'vi_do' => 10.5439, 'kinh_do' => 106.4111],
            ['ten_tinh_thanh' => 'Nam Định', 'vi_do' => 20.4388, 'kinh_do' => 106.1621],
            ['ten_tinh_thanh' => 'Nghệ An', 'vi_do' => 18.6796, 'kinh_do' => 105.6813],
            ['ten_tinh_thanh' => 'Ninh Bình', 'vi_do' => 20.2506, 'kinh_do' => 105.9745],
            ['ten_tinh_thanh' => 'Ninh Thuận', 'vi_do' => 11.5675, 'kinh_do' => 108.9886],
            ['ten_tinh_thanh' => 'Phú Thọ', 'vi_do' => 21.3450, 'kinh_do' => 105.2875],
            ['ten_tinh_thanh' => 'Phú Yên', 'vi_do' => 13.0882, 'kinh_do' => 109.0929],
            ['ten_tinh_thanh' => 'Quảng Bình', 'vi_do' => 17.4689, 'kinh_do' => 106.6223],
            ['ten_tinh_thanh' => 'Quảng Nam', 'vi_do' => 15.5394, 'kinh_do' => 108.0190],
            ['ten_tinh_thanh' => 'Quảng Ngãi', 'vi_do' => 15.1200, 'kinh_do' => 108.8000],
            ['ten_tinh_thanh' => 'Quảng Ninh', 'vi_do' => 20.9599, 'kinh_do' => 107.0425],
            ['ten_tinh_thanh' => 'Quảng Trị', 'vi_do' => 16.7460, 'kinh_do' => 107.1890],
            ['ten_tinh_thanh' => 'Sóc Trăng', 'vi_do' => 9.6025, 'kinh_do' => 105.9739],
            ['ten_tinh_thanh' => 'Sơn La', 'vi_do' => 21.3256, 'kinh_do' => 103.9188],
            ['ten_tinh_thanh' => 'Tây Ninh', 'vi_do' => 11.3352, 'kinh_do' => 106.1099],
            ['ten_tinh_thanh' => 'Thái Bình', 'vi_do' => 20.4463, 'kinh_do' => 106.3366],
            ['ten_tinh_thanh' => 'Thái Nguyên', 'vi_do' => 21.5942, 'kinh_do' => 105.8480],
            ['ten_tinh_thanh' => 'Thanh Hóa', 'vi_do' => 19.8079, 'kinh_do' => 105.7764],
            ['ten_tinh_thanh' => 'Thừa Thiên Huế', 'vi_do' => 16.4637, 'kinh_do' => 107.5909],
            ['ten_tinh_thanh' => 'Tiền Giang', 'vi_do' => 10.4490, 'kinh_do' => 106.3421],
            ['ten_tinh_thanh' => 'Trà Vinh', 'vi_do' => 9.8127, 'kinh_do' => 106.2993],
            ['ten_tinh_thanh' => 'Tuyên Quang', 'vi_do' => 21.8230, 'kinh_do' => 105.2140],
            ['ten_tinh_thanh' => 'Vĩnh Long', 'vi_do' => 10.2538, 'kinh_do' => 105.9720],
            ['ten_tinh_thanh' => 'Vĩnh Phúc', 'vi_do' => 21.3089, 'kinh_do' => 105.6049],
            ['ten_tinh_thanh' => 'Yên Bái', 'vi_do' => 21.7051, 'kinh_do' => 104.8702],
        ]);
    }
}
