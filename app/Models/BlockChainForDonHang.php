<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockChainForDonHang extends Model
{
    use HasFactory;
    protected $table = 'block_chain_for_don_hangs';
    protected $fillable = [
        "id_don_hang",
        "action",
        "transaction_hash",
        "metadata_uri",
        "token_id",
        "id_user",
        "loai_tai_khoan"
    ];
}
