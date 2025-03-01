<?php

namespace App\Http\Controllers;

use App\Models\DanhMucSanPham;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DanhMucSanPhamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view();
    }

    public function getData()
    {
        $data = DanhMucSanPham::get();

        return response()->json([
            'status'    =>  true,
            'ma_danh_muc' => $data
        ]);
    }

    public function searchDanhMuc(Request $request)
    {
        $key = "%" . $request->abc . "%";

        $data   = DanhMucSanPham::where('ten_danh_muc', 'like', $key)
            ->get();

        return response()->json([
            'status'    =>  true,
            'ma_danh_muc'  =>  $data,
        ]);
    }

    public function createDanhMuc(Request $request)
    {
        DanhMucSanPham::create([
            'ma_danh_muc'   =>  $request->ma_danh_muc,
            'ten_danh_muc'  =>  $request->ten_danh_muc,
            'tinh_trang'    =>  $request->tinh_trang
        ]);
        return response()->json([
            'status'    =>  true,
            'message'   =>  'Đã tạo mới danh mục thành công!'
        ]);
    }

    public function deleteDanhMuc($id)
    {
        try {
            DanhMucSanPham::where('id', $id)->delete();
            return response()->json([
                'status'            =>   true,
                'message'           =>   'Xóa danh mục thành công!',
            ]);
        } catch (Exception $e) {
            Log::info("Lỗi", $e);
            return response()->json([
                'status'            =>   false,
                'message'           =>   'Có lỗi khi xóa danh mục',
            ]);
        }
    }

    public function updateDanhMuc(Request $request)
    {
        try {
            DanhMucSanPham::where('id', $request->id)
                ->update([
                    'ma_danh_muc'   =>  $request->ma_danh_muc,
                    'ten_danh_muc'  =>  $request->ten_danh_muc,
                    'tinh_trang'    =>  $request->tinh_trang
                ]);
            return response()->json([
                'status'            =>   true,
                'message'           =>   'Đã cập nhật thành công danh mục ' . $request->ten_danh_muc,
            ]);
        } catch (Exception $e) {
            Log::info("Lỗi", $e);
            return response()->json([
                'status'            =>   false,
                'message'           =>   'Có lỗi khi cập nhật thông tin danh mục',
            ]);
        }
    }

    public function doiTinhTrangDanhMuc(Request $request)
    {
        try {
            if ($request->tinh_trang == 1) {
                $tinh_trang_moi = 0;
            } else {
                $tinh_trang_moi = 1;
            }
            DanhMucSanPham::where('id', $request->id)->update([
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
