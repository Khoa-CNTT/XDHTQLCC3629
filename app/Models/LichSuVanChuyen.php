<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LichSuVanChuyen extends Model
{
    use HasFactory;
    protected $table = "lich_su_van_chuyens";
    protected $fillable = [
        "id_don_hang",
        "id_kho_hang",
        "id_don_vi_van_chuyen",
        "id_nha_san_xuat",
        "thoi_gian_den",
        "thoi_gian_di",
        "thu_tu",
        "mo_ta",
        "tinh_trang",
        "id_dai_ly",
        "tuyen_so",
        "transaction_hash",
        "metadata_uri",
        "token_id",
        "ma_van_don",
    ];
}
