<?php

namespace App\Http\Controllers;

use App\Models\ChiTietSanPham;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChiTietSanPhamController extends Controller
{
    public function getdata()
    {
        $data = ChiTietSanPham::join('san_phams', 'chi_tiet_san_phams.ma_san_pham','san_phams.ma_san_pham')
                        ->select('chi_tiet_san_phams.*','san_phams.ma_san_pham','san_phams.ten_san_pham')
                        ->get();
        return response()->json([
            'status'    =>  true,
            'chi_tiet_san_pham'  =>  $data
        ]);
    }

    public function createChiTietSP(Request $request)
    {
        $data   =   $request->all();
        ChiTietSanPham::create([
            'ma_don_hang'       =>  $request->ma_don_hang,
            'ma_san_pham'       =>  $request->ma_san_pham,
            'ghi_chu'           =>  $request->ghi_chu,
            'don_gia'           =>  $request->don_gia,
            'so_luong'          =>  $request->so_luong,
            'don_vi_tinh'       =>  $request->don_vi_tinh,
            'tinh_trang'        =>  $request->tinh_trang
        ]);
        return response()->json([
            'status'    =>  true,
            'message'   =>  'Đã tạo mới thành công!'
        ]);
    }

    public function updateChiTietSP(Request $request)
    {
        try {
            ChiTietSanPham::where('id', $request->id)
                ->update([
                    'ma_don_hang'       =>  $request->ma_don_hang,
                    'ma_san_pham'       =>  $request->ma_san_pham,
                    'ghi_chu'           =>  $request->ghi_chu,
                    'don_gia'           =>  $request->don_gia,
                    'so_luong'          =>  $request->so_luong,
                    'don_vi_tinh'       =>  $request->don_vi_tinh,
                    'tinh_trang'        =>  $request->tinh_trang
                ]);
            return response()->json([
                'status'            =>   true,
                'message'           =>   'Đã cập nhật thành công ' . $request->ma_san_pham,
            ]);
        } catch (Exception $e) {
            Log::info("Lỗi", $e);
            return response()->json([
                'status'            =>   false,
                'message'           =>   'Có lỗi khi cập nhật thông tin',
            ]);
        }
    }

    public function searchChiTietSP(Request $request)
    {
        $key = "%" . $request->abc . "%";

        $data   = ChiTietSanPham::where('ma_san_pham', 'like', $key)
            ->get();

        return response()->json([
            'status'    =>  true,
            'chi_tiet_san_pham'  =>  $data,
        ]);
    }

    public function deleteChiTietSP($id)
    {
        try {
            ChiTietSanPham::where('id', $id)->delete();
            return response()->json([
                'status'            =>   true,
                'message'           =>   'Xóa thành công!',
            ]);
        } catch (Exception $e) {
            Log::info("Lỗi", $e);
            return response()->json([
                'status'            =>   false,
                'message'           =>   'Có lỗi khi xóa',
            ]);
        }
    }

    public function doiTinhTrangChiTietSP(Request $request)
    {
        try {
            if ($request->tinh_trang == 1) {
                $tinh_trang_moi = 0;
            } else {
                $tinh_trang_moi = 1;
            }
            ChiTietSanPham::where('id', $request->id)->update([
                'tinh_trang'    =>  $tinh_trang_moi
            ]);
            return response()->json([
                'status'            =>   true,
                'message'           =>   'Đã đổi trạng thái thành công',
            ]);
        } catch (Exception $e) {
            Log::info("Lỗi", $e);
            return response()->json([
                'status'            =>   false,
                'message'           =>   'Có lỗi khi đổi trạng thái',
            ]);
        }
    }
}
