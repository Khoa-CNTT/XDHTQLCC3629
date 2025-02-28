<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DaiLy extends Model
{
    use HasFactory;
    protected $table = 'dai_lies';
    protected $fillable = [
        "ten_cong_ty",
        "email",
        "mat_khau",
        "dia_chi",
        "so_dien_thoai",
        "tinh_trang"
    ];
}
