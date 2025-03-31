<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LichSuDonHang extends Model
{
    use HasFactory;
    protected $table = "lich_su_don_hangs";
    protected $fillable = [
        "user_id",
        "id_don_hang",
        "id_san_pham",
        "don_gia",
        "so_luong",
        "tinh_trang",
    ];
}
