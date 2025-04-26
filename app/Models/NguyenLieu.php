<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NguyenLieu extends Model
{
    use HasFactory;
    protected $table = 'nguyen_lieus';
    protected $fillable = [
        "id_nha_san_xuat",
        "id_nha_cung_cap",
        "ma_nguyen_lieu",
        "ten_nguyen_lieu",
        "so_luong",
        "don_vi_tinh",
        "ngay_san_xuat",
        "han_su_dung",
        "tinh_trang"
    ];
}
