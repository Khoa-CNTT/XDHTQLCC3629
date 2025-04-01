<?php

namespace App\Http\Controllers;

use App\Models\SanPhamNSX;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SanPhamNSXController extends Controller
{
    public function getdata()
    {
        $data = SanPhamNSX::join('nha_san_xuats', 'san_pham_n_s_x_e_s.id_nha_san_xuat', 'nha_san_xuats.id')
            ->join('san_phams', 'san_pham_n_s_x_e_s.id_san_pham', 'san_phams.id')
            ->select('san_pham_n_s_x_e_s.*', 'nha_san_xuats.ten_cong_ty', 'san_phams.ma_san_pham', 'san_phams.ten_san_pham', 'nha_san_xuats.ten_cong_ty')
            ->get();
        return response()->json([
            'status'    =>  true,
            'san_pham_nsx'  =>  $data
        ]);
    }

    public function createSanPhamNSX(Request $request)
    {
        $data   =   $request->all();
        SanPhamNSX::create([
            'id_san_pham'         =>  $request->id_san_pham,
            'id_nha_san_xuat'     =>  $request->id_nha_san_xuat,
            'ma_lo_hang'          =>  $request->ma_lo_hang,
            'ngay_san_xuat'       =>  $request->ngay_san_xuat,
            'tinh_trang'          =>  $request->tinh_trang
        ]);
        return response()->json([
            'status'    =>  true,
            'message'   =>  'Đã tạo mới thành công!'
        ]);
    }

    public function updateSanPhamNSX(Request $request)
    {
        try {
            SanPhamNSX::where('id', $request->id)
                ->update([
                    'id_san_pham'         =>  $request->id_san_pham,
                    'id_nha_san_xuat'     =>  $request->id_nha_san_xuat,
                    'ma_lo_hang'          =>  $request->ma_lo_hang,
                    'ngay_san_xuat'       =>  $request->ngay_san_xuat,
                    'tinh_trang'          =>  $request->tinh_trang
                ]);
            return response()->json([
                'status'            =>   true,
                'message'           =>   'Đã cập nhật thành công ' . $request->ten_cong_ty,
            ]);
        } catch (Exception $e) {
            Log::info("Lỗi", $e);
            return response()->json([
                'status'            =>   false,
                'message'           =>   'Có lỗi khi cập nhật thông tin',
            ]);
        }
    }

    public function searchSanPhamNSX(Request $request)
    {
        $key = "%" . $request->abc . "%";

        $data   = SanPhamNSX::where('id_nha_san_xuat', 'like', $key)
            ->get();

        return response()->json([
            'status'    =>  true,
            'san_pham_nsx'  =>  $data,
        ]);
    }

    public function deleteSanPhamNSX($id)
    {
        try {
            SanPhamNSX::where('id', $id)->delete();
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

    public function doiTinhTrangSanPhamNSX(Request $request)
    {
        try {
            $tinh_trang_moi = $request->tinh_trang == 1 ? 0 : 1; 
            SanPhamNSX::where('id', $request->id)->update([
                'tinh_trang' => $tinh_trang_moi
            ]);
            return response()->json([
                'status' => true,
                'message' => 'Đã đổi trạng thái thành công',
            ]);
        } catch (Exception $e) {
            Log::info("Lỗi", $e);
            return response()->json([
                'status' => false,
                'message' => 'Có lỗi khi đổi trạng thái',
            ]);
        }
    }
}
