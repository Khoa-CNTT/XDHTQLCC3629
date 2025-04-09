<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KhoTrungChuyen extends Model
{
    use HasFactory;
    protected $table = 'kho_trung_chuyens';

    protected $fillable = [
        "ten_kho",
        "tinh_thanh",
        "dia_chi",
        "vi_do",
        "kinh_do",
        "loai_kho",
        "mo_ta",
    ];
}
