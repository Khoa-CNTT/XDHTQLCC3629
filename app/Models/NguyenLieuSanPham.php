<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NguyenLieuSanPham extends Model
{
    use HasFactory;
    protected $table = 'nguyen_lieu_san_phams';
    protected $fillable = [
        "ma_san_pham",
        "id_nguyen_lieu",
        "so_luong_nguyen_lieu",
        "tinh_trang"
    ];
}
