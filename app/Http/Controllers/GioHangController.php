<?php

namespace App\Http\Controllers;

use App\Models\DaiLy;
use App\Models\GioHang;
use App\Models\SanPham;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GioHangController extends Controller
{
    public function themVaoGioHang(Request $request)
    {
        // Kiểm tra đầu vào
        $request->validate([
            'id_san_pham' => 'required|exists:san_phams,id',
            'so_luong' => 'required|integer|min:1'
        ]);
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;
        // Kiểm tra sản phẩm có tồn tại không
        $sanPham = SanPham::find($request->id_san_pham);
        if (!$sanPham) {
            return response()->json(['status' => false, 'message' => 'Sản phẩm không tồn tại!'], 404);
        }
        // Kiểm tra sản phẩm đã có trong giỏ hàng chưa
        $gioHang = GioHang::where('user_id', $user_id)
            ->where('id_san_pham', $request->id_san_pham)
            ->first();
        // Lấy tổng số lượng sản phẩm đã có trong giỏ hàng
        $tongSoLuongTrongGio = GioHang::where('user_id', $user_id)
            ->where('id_san_pham', $request->id_san_pham)
            ->sum('so_luong');
        // Kiểm tra nếu tổng số lượng vượt quá số lượng tồn kho
        if (($tongSoLuongTrongGio + $request->so_luong) > $sanPham->so_luong_ton_kho) {
            return response()->json([
                'status' => false,
                'message' => 'Không thể thêm vào giỏ hàng! Số lượng sản phẩm vượt quá tồn kho.'
            ], 400);
        }
        if ($gioHang) {
            $gioHang->so_luong += $request->so_luong;
        } else {
            $gioHang = new GioHang();
            $gioHang->user_id = $user_id;
            $gioHang->id_san_pham = $request->id_san_pham;
            $gioHang->so_luong = $request->so_luong;
            $gioHang->don_gia = $request->don_gia;
        }
        $gioHang->save();
        return response()->json(['status' => true, 'message' => 'Đã thêm vào giỏ hàng!']);
    }

    public function getData(){
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json([
                'message' => 'Bạn cần đăng nhập!',
                'status'  => false,
            ], 401);
        } elseif($user && $user instanceof DaiLy) {
            $id_dai_ly = $user->id;
            $san_pham = GioHang::join('san_phams', 'san_phams.id','gio_hangs.id_san_pham')
            ->join('san_pham_n_s_x_e_s', 'san_phams.id','san_pham_n_s_x_e_s.id_san_pham')
            ->join('nha_san_xuats', 'nha_san_xuats.id', 'san_pham_n_s_x_e_s.id_nha_san_xuat')
            ->where('gio_hangs.user_id', $id_dai_ly)
            ->select('san_phams.id',
                    'san_phams.ten_san_pham',
                    'nha_san_xuats.ten_cong_ty',
                    'san_phams.hinh_anh',
                    'gio_hangs.don_gia',
                    'gio_hangs.so_luong',
                    'san_phams.so_luong_ton_kho',
                    'gio_hangs.id') //get để nhóm ở groupby
            ->get();
            // $check = 2;
            return response()->json([
                'status'    =>      true,
                'data'      =>      $san_pham,
                // 'check'     =>      $check,
            ]);
        }
    }

    public function capNhatSoLuong(Request $request)
    {
        // Validate dữ liệu đầu vào
        $request->validate([
            'id' => 'required|exists:gio_hangs,id',
            'so_luong' => 'required|integer|min:1'
        ]);
        $cartItem = GioHang::find($request->id);
        if (!$cartItem) {
            return response()->json(['status' => false, 'message' => 'Sản phẩm không tồn tại trong giỏ hàng'], 404);
        }
        $sanPham = SanPham::find($cartItem->id_san_pham);
        if (!$sanPham) {
            return response()->json(['status' => false, 'message' => 'Sản phẩm không tồn tại'], 404);
        }
        if ($request->so_luong > $sanPham->so_luong_ton_kho) {
            return response()->json([
                'status' => false,
                'message' => 'Số lượng vượt quá số lượng tồn kho'
            ], 400);
        }
        $cartItem->so_luong = $request->so_luong;
        $cartItem->save();
        return response()->json([
            'status' => true,
            'message' => 'Cập nhật số lượng thành công',
            'data' => $cartItem
        ]);
    }
}
