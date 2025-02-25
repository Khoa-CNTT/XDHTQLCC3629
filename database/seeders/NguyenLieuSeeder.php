<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NguyenLieuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('nguyen_lieus')->delete();

        DB::table('nguyen_lieus')->truncate();

        DB::table('nguyen_lieus')->insert([
            ['ma_nguyen_lieu' => 'NL001', 'ten_nguyen_lieu' =>  'Linh kiện điện tử', 'ma_lo_hang' => 'LO7930', 'ma_nha_cung_cap' => ''],
            ['ma_nguyen_lieu' => 'NL002', 'ten_nguyen_lieu' =>  'Pin lithium', 'ma_lo_hang' => 'LO3613', 'ma_nha_cung_cap' => ''],
            ['ma_nguyen_lieu' => 'NL003', 'ten_nguyen_lieu' =>  'Chip xử lý', 'ma_lo_hang' => 'LO4774', 'ma_nha_cung_cap' => ''],
            ['ma_nguyen_lieu' => 'NL004', 'ten_nguyen_lieu' =>  'Màn hình LED', 'ma_lo_hang' => 'LO3163', 'ma_nha_cung_cap' => ''],
            ['ma_nguyen_lieu' => 'NL005', 'ten_nguyen_lieu' =>  'Bảng mạch', 'ma_lo_hang' => 'LO5137', 'ma_nha_cung_cap' => ''],
            ['ma_nguyen_lieu' => 'NL006', 'ten_nguyen_lieu' =>  'Đường', 'ma_lo_hang' => 'LO7038', 'ma_nha_cung_cap' => ''],
            ['ma_nguyen_lieu' => 'NL007', 'ten_nguyen_lieu' =>  'Sữa', 'ma_lo_hang' => 'LO4599', 'ma_nha_cung_cap' => ''],
            ['ma_nguyen_lieu' => 'NL008', 'ten_nguyen_lieu' =>  'Bột mì', 'ma_lo_hang' => 'LO4141', 'ma_nha_cung_cap' => ''],
            ['ma_nguyen_lieu' => 'NL009', 'ten_nguyen_lieu' =>  'Dầu ăn', 'ma_lo_hang' => 'LO7506', 'ma_nha_cung_cap' => ''],
            ['ma_nguyen_lieu' => 'NL010', 'ten_nguyen_lieu' =>  'Hương liệu', 'ma_lo_hang' => 'LO4563', 'ma_nha_cung_cap' => ''],
            ['ma_nguyen_lieu' => 'NL011', 'ten_nguyen_lieu' =>  'Vải cotton', 'ma_lo_hang' => 'LO5306', 'ma_nha_cung_cap' => ''],
            ['ma_nguyen_lieu' => 'NL012', 'ten_nguyen_lieu' =>  'Da tổng hợp', 'ma_lo_hang' => 'LO9476', 'ma_nha_cung_cap' => ''],
            ['ma_nguyen_lieu' => 'NL013', 'ten_nguyen_lieu' =>  'Dây kéo', 'ma_lo_hang' => 'LO5764', 'ma_nha_cung_cap' => ''],
            ['ma_nguyen_lieu' => 'NL014', 'ten_nguyen_lieu' =>  'Nút áo', 'ma_lo_hang' => 'LO3457', 'ma_nha_cung_cap' => ''],
            ['ma_nguyen_lieu' => 'NL015', 'ten_nguyen_lieu' =>  'Len cashmere', 'ma_lo_hang' => 'LO4699', 'ma_nha_cung_cap' => ''],
            ['ma_nguyen_lieu' => 'NL016', 'ten_nguyen_lieu' =>  'Gỗ công nghiệp', 'ma_lo_hang' => 'LO1704', 'ma_nha_cung_cap' => ''],
            ['ma_nguyen_lieu' => 'NL017', 'ten_nguyen_lieu' =>  'Sơn PU', 'ma_lo_hang' => 'LO4656', 'ma_nha_cung_cap' => ''],
            ['ma_nguyen_lieu' => 'NL018', 'ten_nguyen_lieu' =>  'Kim loại không gỉ', 'ma_lo_hang' => 'LO7737', 'ma_nha_cung_cap' => ''],
            ['ma_nguyen_lieu' => 'NL019', 'ten_nguyen_lieu' =>  'Nhựa ABS', 'ma_lo_hang' => 'LO6897', 'ma_nha_cung_cap' => ''],
            ['ma_nguyen_lieu' => 'NL020', 'ten_nguyen_lieu' =>  'Kính cường lực', 'ma_lo_hang' => 'LO2488', 'ma_nha_cung_cap' => ''],
            ['ma_nguyen_lieu' => 'NL021', 'ten_nguyen_lieu' =>  'Tinh dầu thiên nhiên', 'ma_lo_hang' => 'LO2201', 'ma_nha_cung_cap' => ''],
            ['ma_nguyen_lieu' => 'NL022', 'ten_nguyen_lieu' =>  'Sáp ong', 'ma_lo_hang' => 'LO5138', 'ma_nha_cung_cap' => ''],
            ['ma_nguyen_lieu' => 'NL023', 'ten_nguyen_lieu' =>  'Chiết xuất lô hội', 'ma_lo_hang' => 'LO6028', 'ma_nha_cung_cap' => ''],
            ['ma_nguyen_lieu' => 'NL024', 'ten_nguyen_lieu' =>  'Vitamin E', 'ma_lo_hang' => 'LO7087', 'ma_nha_cung_cap' => ''],
            ['ma_nguyen_lieu' => 'NL025', 'ten_nguyen_lieu' =>  'Collagen', 'ma_lo_hang' => 'LO3683', 'ma_nha_cung_cap' => ''],
            ['ma_nguyen_lieu' => 'NL026', 'ten_nguyen_lieu' =>  'Nhân sâm', 'ma_lo_hang' => 'LO5879', 'ma_nha_cung_cap' => ''],
            ['ma_nguyen_lieu' => 'NL027', 'ten_nguyen_lieu' =>  'Tinh bột nghệ', 'ma_lo_hang' => 'LO4659', 'ma_nha_cung_cap' => ''],
            ['ma_nguyen_lieu' => 'NL028', 'ten_nguyen_lieu' =>  'Omega-3', 'ma_lo_hang' => 'LO8146', 'ma_nha_cung_cap' => ''],
            ['ma_nguyen_lieu' => 'NL029', 'ten_nguyen_lieu' =>  'Protein thực vật', 'ma_lo_hang' => 'LO9869', 'ma_nha_cung_cap' => ''],
            ['ma_nguyen_lieu' => 'NL030', 'ten_nguyen_lieu' =>  'Canxi hữu cơ', 'ma_lo_hang' => 'LO5378', 'ma_nha_cung_cap' => ''],
            ['ma_nguyen_lieu' => 'NL031', 'ten_nguyen_lieu' =>  'Cao su non', 'ma_lo_hang' => 'LO5920', 'ma_nha_cung_cap' => ''],
            ['ma_nguyen_lieu' => 'NL032', 'ten_nguyen_lieu' =>  'Vải polyester', 'ma_lo_hang' => 'LO2783', 'ma_nha_cung_cap' => ''],
            ['ma_nguyen_lieu' => 'NL033', 'ten_nguyen_lieu' =>  'Nhựa tổng hợp', 'ma_lo_hang' => 'LO2746', 'ma_nha_cung_cap' => ''],
            ['ma_nguyen_lieu' => 'NL034', 'ten_nguyen_lieu' =>  'Thép không gỉ', 'ma_lo_hang' => 'LO3519', 'ma_nha_cung_cap' => ''],
            ['ma_nguyen_lieu' => 'NL035', 'ten_nguyen_lieu' =>  'Mút EVA', 'ma_lo_hang' => 'LO6268', 'ma_nha_cung_cap' => ''],
            ['ma_nguyen_lieu' => 'NL036', 'ten_nguyen_lieu' =>  'Giấy tái chế', 'ma_lo_hang' => 'LO3546', 'ma_nha_cung_cap' => ''],
            ['ma_nguyen_lieu' => 'NL037', 'ten_nguyen_lieu' =>  'Mực in', 'ma_lo_hang' => 'LO7283', 'ma_nha_cung_cap' => ''],
            ['ma_nguyen_lieu' => 'NL038', 'ten_nguyen_lieu' =>  'Bìa cứng', 'ma_lo_hang' => 'LO7155', 'ma_nha_cung_cap' => ''],
            ['ma_nguyen_lieu' => 'NL039', 'ten_nguyen_lieu' =>  'Gỗ balsa', 'ma_lo_hang' => 'LO6963', 'ma_nha_cung_cap' => ''],
            ['ma_nguyen_lieu' => 'NL040', 'ten_nguyen_lieu' =>  'Nhựa PP', 'ma_lo_hang' => 'LO2461', 'ma_nha_cung_cap' => ''],
            ['ma_nguyen_lieu' => 'NL041', 'ten_nguyen_lieu' =>  'Hợp kim nhôm', 'ma_lo_hang' => 'LO1798', 'ma_nha_cung_cap' => ''],
            ['ma_nguyen_lieu' => 'NL042', 'ten_nguyen_lieu' =>  'Dầu động cơ', 'ma_lo_hang' => 'LO2109', 'ma_nha_cung_cap' => ''],
            ['ma_nguyen_lieu' => 'NL043', 'ten_nguyen_lieu' =>  'Cao su tự nhiên', 'ma_lo_hang' => 'LO9050', 'ma_nha_cung_cap' => ''],
            ['ma_nguyen_lieu' => 'NL044', 'ten_nguyen_lieu' =>  'Bộ lọc khí', 'ma_lo_hang' => 'LO9366', 'ma_nha_cung_cap' => ''],
            ['ma_nguyen_lieu' => 'NL045', 'ten_nguyen_lieu' =>  'Ắc quy', 'ma_lo_hang' => 'LO7707', 'ma_nha_cung_cap' => ''],
            ['ma_nguyen_lieu' => 'NL046', 'ten_nguyen_lieu' =>  'Nhựa ABS an toàn', 'ma_lo_hang' => 'LO2941', 'ma_nha_cung_cap' => ''],
            ['ma_nguyen_lieu' => 'NL047', 'ten_nguyen_lieu' =>  'Vải bông', 'ma_lo_hang' => 'LO2877', 'ma_nha_cung_cap' => ''],
            ['ma_nguyen_lieu' => 'NL048', 'ten_nguyen_lieu' =>  'Gỗ tự nhiên', 'ma_lo_hang' => 'LO9845', 'ma_nha_cung_cap' => ''],
            ['ma_nguyen_lieu' => 'NL049', 'ten_nguyen_lieu' =>  'Màu thực phẩm', 'ma_lo_hang' => 'LO4767', 'ma_nha_cung_cap' => ''],
            ['ma_nguyen_lieu' => 'NL050', 'ten_nguyen_lieu' =>  'Silicone y tế', 'ma_lo_hang' => 'LO3990', 'ma_nha_cung_cap' => ''],
        ]);
    }
}
