<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DanhMucSanPhamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('danh_muc_san_phams')->delete();

        DB::table('danh_muc_san_phams')->truncate();

        DB::table('danh_muc_san_phams')->insert([
            ['ma_danh_muc' => 'DM01', 'ten_danh_muc' =>  'Điện tử & Công nghệ'],
            ['ma_danh_muc' => 'DM02', 'ten_danh_muc' =>  'Thực phẩm & Đồ uống'],
            ['ma_danh_muc' => 'DM03', 'ten_danh_muc' =>  'Thời trang & Phụ kiện'],
            ['ma_danh_muc' => 'DM04', 'ten_danh_muc' =>  'Gia dụng & Nội thất'],
            ['ma_danh_muc' => 'DM05', 'ten_danh_muc' =>  'Mỹ phẩm & Chăm sóc cá nhân'],
            ['ma_danh_muc' => 'DM06', 'ten_danh_muc' =>  'Sức khỏe & Dinh dưỡng'],
            ['ma_danh_muc' => 'DM07', 'ten_danh_muc' =>  'Thiết bị & Dụng cụ thể thao'],
            ['ma_danh_muc' => 'DM08', 'ten_danh_muc' =>  'Sách & Văn phòng phẩm'],
            ['ma_danh_muc' => 'DM09', 'ten_danh_muc' =>  'Ô tô & Xe máy'],
            ['ma_danh_muc' => 'DM10', 'ten_danh_muc' =>  'Đồ chơi & Đồ dùng trẻ em'],
        ]);
    }
}
