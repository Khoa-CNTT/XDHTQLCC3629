<?php

namespace App\Http\Controllers;

use App\Models\DaiLy;
use App\Models\DonHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DonHangController extends Controller
{
    public function getData(){
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json([
                'message' => 'Bạn cần đăng nhập!',
                'status'  => false,
            ], 401);
        } elseif($user && $user instanceof DaiLy) {
            $user_id = $user->id;
            $list_don_hang = DonHang::
            where('don_hangs.user_id', $user_id)
            ->select('don_hangs.ngay_dat',
                    'don_hangs.ngay_giao',
                    'don_hangs.tong_tien',
                    'don_hangs.tinh_trang',
                    'don_hangs.tinh_trang_thanh_toan')
            ->get();
            return response()->json([
                'status'    =>      true,
                'data'      =>      $list_don_hang,
            ]);
        }
    }
}
