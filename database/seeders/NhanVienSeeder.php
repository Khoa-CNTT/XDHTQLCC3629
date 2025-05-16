<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NhanVienSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('nhan_viens')->delete();

        DB::table('nhan_viens')->truncate();

        DB::table('nhan_viens')->insert([
            [
                'ho_ten' => 'Mai Xuân Tùng',
                'email' =>  'maitung801@gmail.com',
                'password' => bcrypt('123456'),
                'id_chuc_vu' => '1',
                'loai_tai_khoan' => 'Nhân Viên',
                'dia_chi_vi' => 'TMEFhnpj9whh9bTCfVQ35kBuPfZpsZj2Sj'
            ],
            [
                'ho_ten' => 'Lê Anh Xuân',
                'email' =>  'xuanbake123@gmail.com',
                'password' => bcrypt('123456'),
                'id_chuc_vu' => '1',
                'loai_tai_khoan' => 'Nhân Viên',
                'dia_chi_vi' => 'TGdU79UeooERfKuVYm9RPLJXRbG9zPBHSd'
            ],
            [
                'ho_ten' => 'Văn Quý Hưng',
                'email' =>  'quyhung180603@gmail.com',
                'password' => bcrypt('123456'),
                'id_chuc_vu' => '1',
                'loai_tai_khoan' => 'Nhân Viên',
                'dia_chi_vi' => 'TKJ1sdrvJA4bWmpj8xrr1a4G6RmB6xTyqC'
            ],
            [
                'ho_ten' => 'Nguyễn Hữu Thiên',
                'email' =>  'huuthien123@gmail.com',
                'password' => bcrypt('123456'),
                'id_chuc_vu' => '1',
                'loai_tai_khoan' => 'Nhân Viên',
                'dia_chi_vi' => 'TDhhmqVkYGBWp8ihRdf2ddMWc5woggZXne'
            ],
        ]);
    }
}
