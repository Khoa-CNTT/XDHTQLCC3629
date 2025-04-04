<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LichSuVanChuyen extends Model
{
    use HasFactory;
    protected $table = "lich_su_van_chuyens";
    protected $fillable = [
        "id_don_hang",
        "id_don_vi_van_chuyen",
        "dia_diem_hien_tai",
        "thoi_gian_cap_nhat",
        "tinh_trang(",
    ];
}
