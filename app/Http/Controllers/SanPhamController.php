<?php

namespace App\Http\Controllers;

use App\Models\DanhMucSanPham;
use App\Models\SanPham;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SanPhamController extends Controller
{
    public function getdata()
    {
        $data = SanPham::join('danh_muc_san_phams', 'san_phams.id_danh_muc','danh_muc_san_phams.id')
                        ->select('san_phams.*','danh_muc_san_phams.ten_danh_muc')
                        ->get();
        return response()->json([
            'status'    =>  true,
            'san_pham'  =>  $data
        ]);
    }
   
    public function createSanPham(Request $request)
    {
        $data   =   $request->all();
        SanPham::create([
            'ma_san_pham'        =>  $request->ma_san_pham,
            'ten_san_pham'       =>  $request->ten_san_pham,
            'mo_ta'              =>  $request->mo_ta,
            'id_danh_muc'        =>  $request->id_danh_muc,
            'transaction_hash'   =>  $request->transaction_hash,
            'tinh_trang'         =>  $request->tinh_trang
        ]);
        return response()->json([
            'status'    =>  true,
            'message'   =>  'Đã tạo mới sản phẩm thành công!'
        ]);
    }

    public function updateSanPham(Request $request)
    {
        try {
            SanPham::where('id', $request->id)
                ->update([
                    'ma_san_pham'        =>  $request->ma_san_pham,
                    'ten_san_pham'       =>  $request->ten_san_pham,
                    'mo_ta'              =>  $request->mo_ta,
                    'id_danh_muc'        =>  $request->id_danh_muc,
                    'transaction_hash'   =>  $request->transaction_hash,
                    'tinh_trang'         =>  $request->tinh_trang
                ]);
            return response()->json([
                'status'            =>   true,
                'message'           =>   'Đã cập nhật thành công sản phẩm ' . $request->ten_san_pham,
            ]);
        } catch (Exception $e) {
            Log::info("Lỗi", $e);
            return response()->json([
                'status'            =>   false,
                'message'           =>   'Có lỗi khi cập nhật thông tin sản phẩm',
            ]);
        }
    }

    public function searchSanPham(Request $request)
    {
        $key = "%" . $request->abc . "%";

        $data   = SanPham::where('ten_san_pham', 'like', $key)
            ->get();

        return response()->json([
            'status'    =>  true,
            'san_pham'  =>  $data,
        ]);
    }

    public function deleteSanPham($id)
    {
        try {
            SanPham::where('id', $id)->delete();
            return response()->json([
                'status'            =>   true,
                'message'           =>   'Xóa sản phẩm thành công!',
            ]);
        } catch (Exception $e) {
            Log::info("Lỗi", $e);
            return response()->json([
                'status'            =>   false,
                'message'           =>   'Có lỗi khi xóa sản phẩm',
            ]);
        }
    }

    public function doiTinhTrangSanPham(Request $request)
    {
        try {
            if ($request->tinh_trang == 1) {
                $tinh_trang_moi = 0;
            } else {
                $tinh_trang_moi = 1;
            }
            SanPham::where('id', $request->id)->update([
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
