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
            [
                'ten_cong_ty' => 'Vinamilk',
                'loai_doi_tac' =>  '1',
                'dia_chi' => '10 Tân Lập, Long Biên, Hà Nội',
                'so_dien_thoai' => '0912345679',
                'email' => 'contact@vinamilk.com.vn',
                'ngay_cap_nhat' => '2025-03-10',
                'password' => bcrypt('123456'),
                'loai_tai_khoan' => 'Nhà Sản Xuất',
                'vi_do' => 21.0285, 'kinh_do' => 105.8542,
                'dia_chi_vi' => 'TVkbgRQW9C1XBawpj5skDmUutCwXXbVR9P'
            ],
            [
                'ten_cong_ty' => 'Hoà Phát Furniture',
                'loai_doi_tac' =>  '1',
                'dia_chi' => '643 Nguyễn Văn Cừ, quận Long Biên, Hà Nội',
                'so_dien_thoai' => '0912345680', 'email' => 'contact@hoaphat.com.vn',
                'ngay_cap_nhat' => '2025-03-11',
                'password' => bcrypt('123456'),
                'loai_tai_khoan' => 'Nhà Sản Xuất',
                'vi_do' => 21.0285, 'kinh_do' => 105.8542,
                'dia_chi_vi' => 'TUtmUFRyUP93fJPR4QPQTo2VU2MfzRWF5f'
            ],
            [
                'ten_cong_ty' => 'Sony Electronics',
                'loai_doi_tac' =>  '1',
                'dia_chi' => 'Tòa nhà Sony, Hồng Lĩnh, Hà Tĩnh',
                'so_dien_thoai' => '0912345681',
                'email' => 'support@sony.com.vn',
                'ngay_cap_nhat' => '2025-03-12',
                'password' => bcrypt('123456'),
                'loai_tai_khoan' => 'Nhà Sản Xuất',
                'vi_do' => 18.3559, 'kinh_do' => 105.8875,
                'dia_chi_vi' => 'TRsGW93gQvsVzsRfnHDxjXLMxCzon4Gmtr'
            ],
            [
                'ten_cong_ty' => 'Unilever',
                'loai_doi_tac' =>  '1',
                'dia_chi' => '156 Nguyễn Lương Bằng, Cao Phong, Hòa Bình',
                'so_dien_thoai' => '0912345682',
                'email' => 'hotro@unilever.com',
                'ngay_cap_nhat' => '2025-03-13',
                'password' => bcrypt('123456'),
                'loai_tai_khoan' => 'Nhà Sản Xuất',
                'vi_do' => 20.8170, 'kinh_do' => 105.3376,
                'dia_chi_vi' => 'TGdU79UeooERfKuVYm9RPLJXRbG9zPBHSd'
            ],
            [
                'ten_cong_ty' => 'Nike',
                'loai_doi_tac' =>  '1',
                'dia_chi' => 'KCN Nhơn Trạch, Đồng Hới',
                'so_dien_thoai' => '0912345683',
                'email' => 'sales@nike.com',
                'ngay_cap_nhat' => '2025-03-14',
                'password' => bcrypt('123456'),
                'loai_tai_khoan' => 'Nhà Sản Xuất',
                'vi_do' => 17.4689, 'kinh_do' => 106.6223,
                'dia_chi_vi' => 'TGdU79UeooERfKuVYm9RPLJXRbG9zPBHSd'
            ],
            [
                'ten_cong_ty' => 'Zara',
                'loai_doi_tac' =>  '1',
                'dia_chi' => 'Zara Tower, Cầu Giấy, Hà Nội',
                'so_dien_thoai' => '0912345684',
                'email' => 'contact@zara.com',
                'ngay_cap_nhat' => '2025-03-15',
                'password' => bcrypt('123456'),
                'loai_tai_khoan' => 'Nhà Sản Xuất',
                'vi_do' => 21.0285, 'kinh_do' => 105.8542,
                'dia_chi_vi' => 'TGdU79UeooERfKuVYm9RPLJXRbG9zPBHSd'
            ],
            [
                'ten_cong_ty' => 'Nhã Nam',
                'loai_doi_tac' =>  '1',
                'dia_chi' => 'Số 59 Đinh Tiên Hoàng, Hà Nội',
                'so_dien_thoai' => '0912345685',
                'email' => 'info@nhanam.vn',
                'ngay_cap_nhat' => '2025-03-16',
                'password' => bcrypt('123456'),
                'loai_tai_khoan' => 'Nhà Sản Xuất',
                'vi_do' => 21.0285, 'kinh_do' => 105.8542,
                'dia_chi_vi' => 'TGdU79UeooERfKuVYm9RPLJXRbG9zPBHSd'
            ],
            [
                'ten_cong_ty' => 'Toyota',
                'loai_doi_tac' =>  '1',
                'dia_chi' => 'KCN Hòa Cầm, Đà Nẵng',
                'so_dien_thoai' => '0912345686',
                'email' => 'toyota@toyota.com.vn',
                'ngay_cap_nhat' => '2025-03-17',
                'password' => bcrypt('123456'),
                'loai_tai_khoan' => 'Nhà Sản Xuất',
                'vi_do' => 16.0471, 'kinh_do' => 108.2062,
                'dia_chi_vi' => 'TGdU79UeooERfKuVYm9RPLJXRbG9zPBHSd'
            ],
            [
                'ten_cong_ty' => 'Fisher-Price',
                'loai_doi_tac' =>  '1',
                'dia_chi' => 'KCN Visip, Quảng Ngãi',
                'so_dien_thoai' => '0912345687',
                'email' => 'contact@fisherprice.com',
                'ngay_cap_nhat' => '2025-03-18',
                'password' => bcrypt('123456'),
                'loai_tai_khoan' => 'Nhà Sản Xuất',
                'vi_do' => 15.1200, 'kinh_do' => 108.8000,
                'dia_chi_vi' => 'TGdU79UeooERfKuVYm9RPLJXRbG9zPBHSd'
            ],
            [
                'ten_cong_ty' => 'Blackmores',
                'loai_doi_tac' =>  '1',
                'dia_chi' => 'KCN Bồng Sơn, Bình Định',
                'so_dien_thoai' => '0912345688',
                'email' => 'support@blackmores.com',
                'ngay_cap_nhat' => '2025-03-19',
                'password' => bcrypt('123456'),
                'loai_tai_khoan' => 'Nhà Sản Xuất',
                'vi_do' => 13.7820, 'kinh_do' => 109.2190,
                'dia_chi_vi' => 'TGdU79UeooERfKuVYm9RPLJXRbG9zPBHSd'
            ],
        ]);
    }
}
