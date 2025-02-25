<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NguyenLieu extends Model
{
    use HasFactory;
    protected $table = 'nguyen_lieus';
    protected $fillable = [
        "ma_nguyen_lieu",
        "ten_nguyen_lieu",
        "ma_lo_hang",
        "ma_nha_cung_cap",
        "tinh_trang"
    ];
}
