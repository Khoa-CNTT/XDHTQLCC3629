<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;

class NhanVien extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $table = "nhan_viens";
    protected $fillable = [
        "ho_ten",
        "email",
        "password",
        "id_chuc_vu",
        "tinh_trang"
    ];
}
