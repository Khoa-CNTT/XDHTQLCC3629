<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DanhMucSanPham extends Model
{
    use HasFactory;
    protected $table = "danh_muc_san_phams";
    protected $fillable = [
        "ma_danh_muc",
        "ten_danh_muc",
        "tinh_trang"
    ];
}
