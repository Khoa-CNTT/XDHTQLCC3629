<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DonHang extends Model
{
    use HasFactory;

    protected $table = 'don_hangs';
    protected $fillable = [
        "ma_don_hang",
        "user_id",
        "id_nguoi_duyet",
        "ngay_dat",
        "ngay_giao",
        "tong_tien",
        "tinh_trang",
        "tinh_trang_thanh_toan",
        "cuoc_van_chuyen",
        "transaction_hash",
        "metadata_uri",
        "token_id",
        "huy_bo_boi",
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($donHang) {
            if (!$donHang->ma_don_hang) {
                $donHang->ma_don_hang = (string) Str::uuid(); // Tạo UUID nếu chưa có
            }
        });
    }
}
