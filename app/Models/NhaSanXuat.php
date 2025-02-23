<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NhaSanXuat extends Model
{
    use HasFactory;
    protected $table = "nha_san_xuats";
    protected $fillable = [
        "ten_cong_ty",
        "loai_doi_tac",
        "dia_chi",
        "so_dien_thoai",
        "email",
        "ngay_tao",
        "ngay_cap_nhat",
        "tinh_trang"
    ];
}
