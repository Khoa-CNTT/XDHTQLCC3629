<?php

namespace App\Http\Controllers;

use App\Models\NguyenLieu;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NguyenLieuController extends Controller
{
    public function getData()
    {
        $data = NguyenLieu::get();

        return response()->json([
            'status'    =>  true,
            'nguyen_lieu' => $data
        ]);
    }
    public function changeTrangthai(Request $request)
    {
        try {
            if ($request->tinh_trang == 1) {
                $tinh_trang_moi = 0;
            } else {
                $tinh_trang_moi = 1;
            }
            NguyenLieu::where('id', $request->id)->update([
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
    public function createNguyenLieu(Request $request)
    {
        $data   =   $request->all();
        NguyenLieu::create([
            'ma_nguyen_lieu'        =>  $request->ma_nguyen_lieu,
            'ten_nguyen_lieu'       =>  $request->ten_nguyen_lieu,
            'ma_lo_hang'            =>  $request->ma_lo_hang,
            'ma_nha_cung_cap'       =>  $request->ma_nha_cung_cap,
            'tinh_trang'            =>  $request->tinh_trang
        ]);
        return response()->json([
            'status'    =>  true,
            'message'   =>  'Đã tạo mới nguyên liệu thành công!'
        ]);
    }
    public function updateNguyenLieu(Request $request)
    {
        try {
            NguyenLieu::where('id', $request->id)
                ->update([
                    'ma_nguyen_lieu'        =>  $request->ma_nguyen_lieu,
                    'ten_nguyen_lieu'       =>  $request->ten_nguyen_lieu,
                    'ma_lo_hang'            =>  $request->ma_lo_hang,
                    'ma_nha_cung_cap'       =>  $request->ma_nha_cung_cap,
                    'tinh_trang'            =>  $request->tinh_trang
                ]);
            return response()->json([
                'status'            =>   true,
                'message'           =>   'Đã cập nhật thành công nguyên liệu',
            ]);
        } catch (Exception $e) {
            Log::info("Lỗi", $e);
            return response()->json([
                'status'            =>   false,
                'message'           =>   'Có lỗi khi cập nhật thông tin nguyên liệu',
            ]);
        }
    }
    public function deleteNguyenLieu($id)
    {
        $data   =   NguyenLieu::where('id', $id)->first();
        if ($data) {
            $data->delete();
            return response()->json([
                'status'    =>   true,
                'message'   =>   'Đã xóa nguyên liệu thành công!'
            ]);
        } else {
            return response()->json([
                'status'    =>   false,
                'message'   =>   'Không tìm được nguyên liệu để xóa!'
            ]);
        }
    }
    public function searchNguyenLieu(Request $request)
    {
        $key = "%" . $request->abc . "%";

        $data   = NguyenLieu::where('ten_nguyen_lieu', 'like', $key)
            ->get();

        return response()->json([
            'status'    =>  true,
            'nguyen_lieu'  =>  $data,
        ]);
    }
}
