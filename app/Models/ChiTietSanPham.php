<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChiTietSanPham extends Model
{
    use HasFactory;
    protected $table = 'chi_tiet_san_phams';
    protected $fillable = [
        "ma_don_hang",
        "ma_san_pham",
        "ghi_chu",
        "don_gia",
        "so_luong",
        "don_vi_tinh",
        "tinh_trang"
    ];
}
