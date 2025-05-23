<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiaoDich extends Model
{
    use HasFactory;
    protected $table = 'giao_dichs';

    protected $fillable = [
        'ma_giao_dich',
        'ma_don_hang',
        'mo_ta',
        'gia_tri',
        'ngay_thuc_hien',
        'so_tai_khoan',
        'ma_tham_chieu',
        'so_tai_khoan_doi_ung'
    ];
}
