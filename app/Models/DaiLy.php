<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class DaiLy extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $table = 'dai_lies';
    protected $fillable = [
        "ten_cong_ty",
        "email",
        "password",
        "dia_chi",
        "so_dien_thoai",
        "tinh_trang",
        "vi_do",
        "kinh_do",
    ];
}
