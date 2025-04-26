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
            ['id_nha_san_xuat' => '3', 'id_nha_cung_cap' => '3', 'ma_nguyen_lieu' => 'NL001', 'ten_nguyen_lieu' =>  'Linh kiện điện tử', 'so_luong' => '20', 'don_vi_tinh' => 'kg', 'ngay_san_xuat' => '2024-01-01', 'han_su_dung' => '2025-01-01', 'tinh_trang' => 1],
            ['id_nha_san_xuat' => '3', 'id_nha_cung_cap' => '3', 'ma_nguyen_lieu' => 'NL002', 'ten_nguyen_lieu' =>  'Pin lithium', 'so_luong' => '20', 'don_vi_tinh' => 'kg', 'ngay_san_xuat' => '2024-01-01', 'han_su_dung' => '2025-01-01', 'tinh_trang' => 1],
            ['id_nha_san_xuat' => '3', 'id_nha_cung_cap' => '3', 'ma_nguyen_lieu' => 'NL003', 'ten_nguyen_lieu' =>  'Chip xử lý', 'so_luong' => '20', 'don_vi_tinh' => 'kg', 'ngay_san_xuat' => '2024-01-01', 'han_su_dung' => '2025-01-01', 'tinh_trang' => 1],
            ['id_nha_san_xuat' => '3', 'id_nha_cung_cap' => '3', 'ma_nguyen_lieu' => 'NL004', 'ten_nguyen_lieu' =>  'Màn hình LED', 'so_luong' => '20', 'don_vi_tinh' => 'kg', 'ngay_san_xuat' => '2024-01-01', 'han_su_dung' => '2025-01-01', 'tinh_trang' => 1],
            ['id_nha_san_xuat' => '3', 'id_nha_cung_cap' => '3', 'ma_nguyen_lieu' => 'NL005', 'ten_nguyen_lieu' =>  'Bảng mạch', 'so_luong' => '20', 'don_vi_tinh' => 'kg', 'ngay_san_xuat' => '2024-01-01', 'han_su_dung' => '2025-01-01', 'tinh_trang' => 1],
            ['id_nha_san_xuat' => '1', 'id_nha_cung_cap' => '1', 'ma_nguyen_lieu' => 'NL006', 'ten_nguyen_lieu' =>  'Đường', 'so_luong' => '20', 'don_vi_tinh' => 'kg', 'ngay_san_xuat' => '2024-01-01', 'han_su_dung' => '2025-01-01', 'tinh_trang' => 1],
            ['id_nha_san_xuat' => '1', 'id_nha_cung_cap' => '1', 'ma_nguyen_lieu' => 'NL007', 'ten_nguyen_lieu' =>  'Sữa', 'so_luong' => '20', 'don_vi_tinh' => 'kg', 'ngay_san_xuat' => '2024-01-01', 'han_su_dung' => '2025-01-01', 'tinh_trang' => 1],
            ['id_nha_san_xuat' => '1', 'id_nha_cung_cap' => '1', 'ma_nguyen_lieu' => 'NL008', 'ten_nguyen_lieu' =>  'Bột mì', 'so_luong' => '20', 'don_vi_tinh' => 'kg', 'ngay_san_xuat' => '2024-01-01', 'han_su_dung' => '2025-01-01', 'tinh_trang' => 1],
            ['id_nha_san_xuat' => '1', 'id_nha_cung_cap' => '1', 'ma_nguyen_lieu' => 'NL009', 'ten_nguyen_lieu' =>  'Dầu ăn', 'so_luong' => '20', 'don_vi_tinh' => 'kg', 'ngay_san_xuat' => '2024-01-01', 'han_su_dung' => '2025-01-01', 'tinh_trang' => 1],
            ['id_nha_san_xuat' => '1', 'id_nha_cung_cap' => '1', 'ma_nguyen_lieu' => 'NL010', 'ten_nguyen_lieu' =>  'Hương liệu', 'so_luong' => '20', 'don_vi_tinh' => 'kg', 'ngay_san_xuat' => '2024-01-01', 'han_su_dung' => '2025-01-01', 'tinh_trang' => 1],
            ['id_nha_san_xuat' => '6', 'id_nha_cung_cap' => '6', 'ma_nguyen_lieu' => 'NL011', 'ten_nguyen_lieu' =>  'Vải cotton', 'so_luong' => '20', 'don_vi_tinh' => 'kg', 'ngay_san_xuat' => '2024-01-01', 'han_su_dung' => '2025-01-01', 'tinh_trang' => 1],
            ['id_nha_san_xuat' => '6', 'id_nha_cung_cap' => '6', 'ma_nguyen_lieu' => 'NL012', 'ten_nguyen_lieu' =>  'Da tổng hợp', 'so_luong' => '20', 'don_vi_tinh' => 'kg', 'ngay_san_xuat' => '2024-01-01', 'han_su_dung' => '2025-01-01', 'tinh_trang' => 1],
            ['id_nha_san_xuat' => '6', 'id_nha_cung_cap' => '6', 'ma_nguyen_lieu' => 'NL013', 'ten_nguyen_lieu' =>  'Dây kéo', 'so_luong' => '20', 'don_vi_tinh' => 'kg', 'ngay_san_xuat' => '2024-01-01', 'han_su_dung' => '2025-01-01', 'tinh_trang' => 1],
            ['id_nha_san_xuat' => '6', 'id_nha_cung_cap' => '6', 'ma_nguyen_lieu' => 'NL014', 'ten_nguyen_lieu' =>  'Nút áo', 'so_luong' => '20', 'don_vi_tinh' => 'kg', 'ngay_san_xuat' => '2024-01-01', 'han_su_dung' => '2025-01-01', 'tinh_trang' => 1],
            ['id_nha_san_xuat' => '6', 'id_nha_cung_cap' => '6', 'ma_nguyen_lieu' => 'NL015', 'ten_nguyen_lieu' =>  'Len cashmere', 'so_luong' => '20', 'don_vi_tinh' => 'kg', 'ngay_san_xuat' => '2024-01-01', 'han_su_dung' => '2025-01-01', 'tinh_trang' => 1],
            ['id_nha_san_xuat' => '9', 'id_nha_cung_cap' => '9', 'ma_nguyen_lieu' => 'NL016', 'ten_nguyen_lieu' =>  'Gỗ công nghiệp', 'so_luong' => '20', 'don_vi_tinh' => 'kg', 'ngay_san_xuat' => '2024-01-01', 'han_su_dung' => '2025-01-01', 'tinh_trang' => 1],
            ['id_nha_san_xuat' => '9', 'id_nha_cung_cap' => '9', 'ma_nguyen_lieu' => 'NL017', 'ten_nguyen_lieu' =>  'Sơn PU', 'so_luong' => '20', 'don_vi_tinh' => 'kg', 'ngay_san_xuat' => '2024-01-01', 'han_su_dung' => '2025-01-01', 'tinh_trang' => 1],
            ['id_nha_san_xuat' => '9', 'id_nha_cung_cap' => '9', 'ma_nguyen_lieu' => 'NL018', 'ten_nguyen_lieu' =>  'Kim loại không gỉ', 'so_luong' => '20', 'don_vi_tinh' => 'kg', 'ngay_san_xuat' => '2024-01-01', 'han_su_dung' => '2025-01-01', 'tinh_trang' => 1],
            ['id_nha_san_xuat' => '8', 'id_nha_cung_cap' => '8', 'ma_nguyen_lieu' => 'NL019', 'ten_nguyen_lieu' =>  'Nhựa ABS', 'so_luong' => '20', 'don_vi_tinh' => 'kg', 'ngay_san_xuat' => '2024-01-01', 'han_su_dung' => '2025-01-01', 'tinh_trang' => 1],
            ['id_nha_san_xuat' => '8', 'id_nha_cung_cap' => '8', 'ma_nguyen_lieu' => 'NL020', 'ten_nguyen_lieu' =>  'Kính cường lực', 'so_luong' => '20', 'don_vi_tinh' => 'kg', 'ngay_san_xuat' => '2024-01-01', 'han_su_dung' => '2025-01-01', 'tinh_trang' => 1],
            ['id_nha_san_xuat' => '4', 'id_nha_cung_cap' => '4', 'ma_nguyen_lieu' => 'NL021', 'ten_nguyen_lieu' =>  'Tinh dầu thiên nhiên', 'so_luong' => '20', 'don_vi_tinh' => 'kg', 'ngay_san_xuat' => '2024-01-01', 'han_su_dung' => '2025-01-01', 'tinh_trang' => 1],
            ['id_nha_san_xuat' => '4', 'id_nha_cung_cap' => '4', 'ma_nguyen_lieu' => 'NL022', 'ten_nguyen_lieu' =>  'Sáp ong', 'so_luong' => '20', 'don_vi_tinh' => 'kg', 'ngay_san_xuat' => '2024-01-01', 'han_su_dung' => '2025-01-01', 'tinh_trang' => 1],
            ['id_nha_san_xuat' => '4', 'id_nha_cung_cap' => '4', 'ma_nguyen_lieu' => 'NL023', 'ten_nguyen_lieu' =>  'Chiết xuất lô hội', 'so_luong' => '20', 'don_vi_tinh' => 'kg', 'ngay_san_xuat' => '2024-01-01', 'han_su_dung' => '2025-01-01', 'tinh_trang' => 1],
            ['id_nha_san_xuat' => '4', 'id_nha_cung_cap' => '4', 'ma_nguyen_lieu' => 'NL024', 'ten_nguyen_lieu' =>  'Vitamin E', 'so_luong' => '20', 'don_vi_tinh' => 'kg', 'ngay_san_xuat' => '2024-01-01', 'han_su_dung' => '2025-01-01', 'tinh_trang' => 1],
            ['id_nha_san_xuat' => '4', 'id_nha_cung_cap' => '4', 'ma_nguyen_lieu' => 'NL025', 'ten_nguyen_lieu' =>  'Collagen', 'so_luong' => '20', 'don_vi_tinh' => 'kg', 'ngay_san_xuat' => '2024-01-01', 'han_su_dung' => '2025-01-01', 'tinh_trang' => 1],
            ['id_nha_san_xuat' => '10', 'id_nha_cung_cap' => '10', 'ma_nguyen_lieu' => 'NL026', 'ten_nguyen_lieu' =>  'Nhân sâm', 'so_luong' => '20', 'don_vi_tinh' => 'kg', 'ngay_san_xuat' => '2024-01-01', 'han_su_dung' => '2025-01-01', 'tinh_trang' => 1],
            ['id_nha_san_xuat' => '10', 'id_nha_cung_cap' => '10', 'ma_nguyen_lieu' => 'NL027', 'ten_nguyen_lieu' =>  'Tinh bột nghệ', 'so_luong' => '20', 'don_vi_tinh' => 'kg', 'ngay_san_xuat' => '2024-01-01', 'han_su_dung' => '2025-01-01', 'tinh_trang' => 1],
            ['id_nha_san_xuat' => '10', 'id_nha_cung_cap' => '10', 'ma_nguyen_lieu' => 'NL028', 'ten_nguyen_lieu' =>  'Omega-3', 'so_luong' => '20', 'don_vi_tinh' => 'kg', 'ngay_san_xuat' => '2024-01-01', 'han_su_dung' => '2025-01-01', 'tinh_trang' => 1],
            ['id_nha_san_xuat' => '10', 'id_nha_cung_cap' => '10', 'ma_nguyen_lieu' => 'NL029', 'ten_nguyen_lieu' =>  'Protein thực vật', 'so_luong' => '20', 'don_vi_tinh' => 'kg', 'ngay_san_xuat' => '2024-01-01', 'han_su_dung' => '2025-01-01', 'tinh_trang' => 1],
            ['id_nha_san_xuat' => '10', 'id_nha_cung_cap' => '10', 'ma_nguyen_lieu' => 'NL030', 'ten_nguyen_lieu' =>  'Canxi hữu cơ', 'so_luong' => '20', 'don_vi_tinh' => 'kg', 'ngay_san_xuat' => '2024-01-01', 'han_su_dung' => '2025-01-01', 'tinh_trang' => 1],
            ['id_nha_san_xuat' => '5', 'id_nha_cung_cap' => '5', 'ma_nguyen_lieu' => 'NL031', 'ten_nguyen_lieu' =>  'Cao su non', 'so_luong' => '20', 'don_vi_tinh' => 'kg', 'ngay_san_xuat' => '2024-01-01', 'han_su_dung' => '2025-01-01', 'tinh_trang' => 1],
            ['id_nha_san_xuat' => '5', 'id_nha_cung_cap' => '5', 'ma_nguyen_lieu' => 'NL032', 'ten_nguyen_lieu' =>  'Vải polyester', 'so_luong' => '20', 'don_vi_tinh' => 'kg', 'ngay_san_xuat' => '2024-01-01', 'han_su_dung' => '2025-01-01', 'tinh_trang' => 1],
            ['id_nha_san_xuat' => '2', 'id_nha_cung_cap' => '2', 'ma_nguyen_lieu' => 'NL033', 'ten_nguyen_lieu' =>  'Nhựa tổng hợp', 'so_luong' => '20', 'don_vi_tinh' => 'kg', 'ngay_san_xuat' => '2024-01-01', 'han_su_dung' => '2025-01-01', 'tinh_trang' => 1],
            ['id_nha_san_xuat' => '2', 'id_nha_cung_cap' => '2', 'ma_nguyen_lieu' => 'NL034', 'ten_nguyen_lieu' =>  'Thép không gỉ', 'so_luong' => '20', 'don_vi_tinh' => 'kg', 'ngay_san_xuat' => '2024-01-01', 'han_su_dung' => '2025-01-01', 'tinh_trang' => 1],
            ['id_nha_san_xuat' => '7', 'id_nha_cung_cap' => '7', 'ma_nguyen_lieu' => 'NL035', 'ten_nguyen_lieu' =>  'Mút EVA', 'so_luong' => '20', 'don_vi_tinh' => 'kg', 'ngay_san_xuat' => '2024-01-01', 'han_su_dung' => '2025-01-01', 'tinh_trang' => 1],
            ['id_nha_san_xuat' => '7', 'id_nha_cung_cap' => '7', 'ma_nguyen_lieu' => 'NL036', 'ten_nguyen_lieu' =>  'Giấy tái chế', 'so_luong' => '20', 'don_vi_tinh' => 'kg', 'ngay_san_xuat' => '2024-01-01', 'han_su_dung' => '2025-01-01', 'tinh_trang' => 1],
            ['id_nha_san_xuat' => '7', 'id_nha_cung_cap' => '7', 'ma_nguyen_lieu' => 'NL037', 'ten_nguyen_lieu' =>  'Mực in', 'so_luong' => '20', 'don_vi_tinh' => 'kg', 'ngay_san_xuat' => '2024-01-01', 'han_su_dung' => '2025-01-01', 'tinh_trang' => 1],
            ['id_nha_san_xuat' => '7', 'id_nha_cung_cap' => '7', 'ma_nguyen_lieu' => 'NL038', 'ten_nguyen_lieu' =>  'Bìa cứng', 'so_luong' => '20', 'don_vi_tinh' => 'kg', 'ngay_san_xuat' => '2024-01-01', 'han_su_dung' => '2025-01-01', 'tinh_trang' => 1],
            ['id_nha_san_xuat' => '7', 'id_nha_cung_cap' => '7', 'ma_nguyen_lieu' => 'NL039', 'ten_nguyen_lieu' =>  'Gỗ balsa', 'so_luong' => '20', 'don_vi_tinh' => 'kg', 'ngay_san_xuat' => '2024-01-01', 'han_su_dung' => '2025-01-01', 'tinh_trang' => 1],
            ['id_nha_san_xuat' => '8', 'id_nha_cung_cap' => '8', 'ma_nguyen_lieu' => 'NL040', 'ten_nguyen_lieu' =>  'Nhựa PP', 'so_luong' => '20', 'don_vi_tinh' => 'kg', 'ngay_san_xuat' => '2024-01-01', 'han_su_dung' => '2025-01-01', 'tinh_trang' => 1],
            ['id_nha_san_xuat' => '8', 'id_nha_cung_cap' => '8', 'ma_nguyen_lieu' => 'NL041', 'ten_nguyen_lieu' =>  'Hợp kim nhôm', 'so_luong' => '20', 'don_vi_tinh' => 'kg', 'ngay_san_xuat' => '2024-01-01', 'han_su_dung' => '2025-01-01', 'tinh_trang' => 1],
            ['id_nha_san_xuat' => '8', 'id_nha_cung_cap' => '8', 'ma_nguyen_lieu' => 'NL042', 'ten_nguyen_lieu' =>  'Dầu động cơ', 'so_luong' => '20', 'don_vi_tinh' => 'kg', 'ngay_san_xuat' => '2024-01-01', 'han_su_dung' => '2025-01-01', 'tinh_trang' => 1],
            ['id_nha_san_xuat' => '9', 'id_nha_cung_cap' => '9', 'ma_nguyen_lieu' => 'NL043', 'ten_nguyen_lieu' =>  'Cao su tự nhiên', 'so_luong' => '20', 'don_vi_tinh' => 'kg', 'ngay_san_xuat' => '2024-01-01', 'han_su_dung' => '2025-01-01', 'tinh_trang' => 1],
            ['id_nha_san_xuat' => '9', 'id_nha_cung_cap' => '9', 'ma_nguyen_lieu' => 'NL044', 'ten_nguyen_lieu' =>  'Bộ lọc khí', 'so_luong' => '20', 'don_vi_tinh' => 'kg', 'ngay_san_xuat' => '2024-01-01', 'han_su_dung' => '2025-01-01', 'tinh_trang' => 1],
            ['id_nha_san_xuat' => '9', 'id_nha_cung_cap' => '9', 'ma_nguyen_lieu' => 'NL045', 'ten_nguyen_lieu' =>  'Ắc quy', 'so_luong' => '20', 'don_vi_tinh' => 'kg', 'ngay_san_xuat' => '2024-01-01', 'han_su_dung' => '2025-01-01', 'tinh_trang' => 1],
            ['id_nha_san_xuat' => '9', 'id_nha_cung_cap' => '9', 'ma_nguyen_lieu' => 'NL046', 'ten_nguyen_lieu' =>  'Nhựa ABS an toàn', 'so_luong' => '20', 'don_vi_tinh' => 'kg', 'ngay_san_xuat' => '2024-01-01', 'han_su_dung' => '2025-01-01', 'tinh_trang' => 1],
            ['id_nha_san_xuat' => '6', 'id_nha_cung_cap' => '6', 'ma_nguyen_lieu' => 'NL047', 'ten_nguyen_lieu' =>  'Vải bông', 'so_luong' => '20', 'don_vi_tinh' => 'kg', 'ngay_san_xuat' => '2024-01-01', 'han_su_dung' => '2025-01-01', 'tinh_trang' => 1],
            ['id_nha_san_xuat' => '7', 'id_nha_cung_cap' => '7', 'ma_nguyen_lieu' => 'NL048', 'ten_nguyen_lieu' =>  'Gỗ tự nhiên', 'so_luong' => '20', 'don_vi_tinh' => 'kg', 'ngay_san_xuat' => '2024-01-01', 'han_su_dung' => '2025-01-01', 'tinh_trang' => 1],
            ['id_nha_san_xuat' => '7', 'id_nha_cung_cap' => '7', 'ma_nguyen_lieu' => 'NL049', 'ten_nguyen_lieu' =>  'Màu thực phẩm', 'so_luong' => '20', 'don_vi_tinh' => 'kg', 'ngay_san_xuat' => '2024-01-01', 'han_su_dung' => '2025-01-01', 'tinh_trang' => 1],
            ['id_nha_san_xuat' => '9', 'id_nha_cung_cap' => '9', 'ma_nguyen_lieu' => 'NL050', 'ten_nguyen_lieu' =>  'Silicone y tế', 'so_luong' => '20', 'don_vi_tinh' => 'kg', 'ngay_san_xuat' => '2024-01-01', 'han_su_dung' => '2025-01-01', 'tinh_trang' => 1],
        ]);
    }
}
