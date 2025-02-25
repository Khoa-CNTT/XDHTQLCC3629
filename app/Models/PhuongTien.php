<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhuongTien extends Model
{
    use HasFactory;
    protected $table = "phuong_tiens";
    protected $fillable = [
        "ma_phuong_tien",
        "ten_phuong_tien",
        "tinh_trang"
    ];
}
