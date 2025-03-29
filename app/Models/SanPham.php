<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SanPham extends Model
{
    use HasFactory;
    protected $table = 'san_phams';
    protected $fillable = [
        "ma_san_pham",
        "ten_san_pham",
        "mo_ta",
        "id_danh_muc",
        "transaction_hash",
        "tinh_trang",
        "hinh_anh",
        "so_luong_ton_kho",
        "gia_ban",
        "don_vi_tinh"
    ];
}
