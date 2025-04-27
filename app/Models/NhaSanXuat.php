<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;

class NhaSanXuat extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $table = "nha_san_xuats";
    protected $fillable = [
        "ten_cong_ty",
        "loai_doi_tac",
        "dia_chi",
        "so_dien_thoai",
        "email",
        "ngay_tao",
        "ngay_cap_nhat",
        "tinh_trang",
        "password",
        "loai_tai_khoan",
        "vi_do",
        "kinh_do",
    ];
}
