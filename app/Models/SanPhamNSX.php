<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SanPhamNSX extends Model
{
    use HasFactory;
    protected $table = 'san_pham_n_s_x_e_s';
    protected $fillable = [
        "id_san_pham",
        "id_nha_san_xuat",
        "ma_lo_hang",
        "ngay_san_xuat",
        "tinh_trang"
    ];
}
