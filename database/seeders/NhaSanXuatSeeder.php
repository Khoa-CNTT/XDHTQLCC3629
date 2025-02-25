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
            ['ten_cong_ty' => 'Vinamilk', 'loai_doi_tac' =>  '1', 'dia_chi' => '10 Tân Lập, Bình Thạnh, TP.HCM', 'so_dien_thoai' => '0912345679', 'email' => 'contact@vinamilk.com.vn','ngay_cap_nhat' => '2025-02-24'],
            ['ten_cong_ty' => 'Hoà Phát Furniture', 'loai_doi_tac' =>  '2', 'dia_chi' => '643 Nguyễn Văn Cừ, quận Long Biên, Hà Nội, Việt Nam', 'so_dien_thoai' => '0912345680', 'email' => 'contact@hoaphat.com.vn','ngay_cap_nhat' => '2025-02-25'],
            ['ten_cong_ty' => 'Sony Electronics', 'loai_doi_tac' =>  '3', 'dia_chi' => 'Tòa nhà Sony, Quận 1, TP.HCM', 'so_dien_thoai' => '0912345681', 'email' => 'support@sony.com.vn','ngay_cap_nhat' => '2025-02-26'],
            ['ten_cong_ty' => 'Unilever', 'loai_doi_tac' =>  '4', 'dia_chi' => '156 Nguyễn Lương Bằng, Q.7, TP.HCM', 'so_dien_thoai' => '0912345682', 'email' => 'hotro@unilever.com','ngay_cap_nhat' => '2025-02-27'],
            ['ten_cong_ty' => 'Nike', 'loai_doi_tac' =>  '5', 'dia_chi' => 'KCN Nhơn Trạch, Đồng Nai', 'so_dien_thoai' => '0912345683', 'email' => 'sales@nike.com','ngay_cap_nhat' => '2025-02-28'],
            ['ten_cong_ty' => 'Zara', 'loai_doi_tac' =>  '6', 'dia_chi' => 'Zara Tower, Cầu Giấy, Hà Nội', 'so_dien_thoai' => '0912345684', 'email' => 'contact@zara.com','ngay_cap_nhat' => '2025-02-28'],
            ['ten_cong_ty' => 'Nhã Nam', 'loai_doi_tac' =>  '7', 'dia_chi' => 'Số 59 Đinh Tiên Hoàng, Hà Nội', 'so_dien_thoai' => '0912345685', 'email' => 'info@nhanam.vn','ngay_cap_nhat' => '2025-02-21'],
            ['ten_cong_ty' => 'Toyota', 'loai_doi_tac' =>  '8', 'dia_chi' => 'KCN Vĩnh Phúc, Vĩnh Phúc', 'so_dien_thoai' => '0912345686', 'email' => 'toyota@toyota.com.vn','ngay_cap_nhat' => '2025-02-22'],
            ['ten_cong_ty' => 'Fisher-Price', 'loai_doi_tac' =>  '9', 'dia_chi' => 'KCN Bình Dương, Bình Dương', 'so_dien_thoai' => '0912345687', 'email' => 'contact@fisherprice.com','ngay_cap_nhat' => '2025-02-25'],
            ['ten_cong_ty' => 'Blackmores', 'loai_doi_tac' =>  '10', 'dia_chi' => 'KCN Củ Chi, TP.HCM', 'so_dien_thoai' => '0912345688', 'email' => 'support@blackmores.com','ngay_cap_nhat' => '2025-02-12'],
        ]);
    }
}
