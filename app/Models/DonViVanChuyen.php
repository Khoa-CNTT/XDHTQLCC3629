<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class DonViVanChuyen extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $table = 'don_vi_van_chuyens';
    protected $fillable = [
        "ten_cong_ty",
        "email",
        "password",
        "so_dien_thoai",
        "dia_chi",
        "cuoc_van_chuyen",
        "tinh_trang",
        "loai_tai_khoan"
    ];
}
