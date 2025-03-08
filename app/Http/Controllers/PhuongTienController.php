<?php

namespace App\Http\Controllers;

use App\Models\PhuongTien;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PhuongTienController extends Controller
{
    public function index()
    {
        return view();
    }

    public function getData()
    {
        $data = PhuongTien::get();

        return response()->json([
            'status'    =>  true,
            'phuong_tien' => $data
        ]);
    }
    public function createPhuongTien(Request $request)
    {
        PhuongTien::create([
            'ma_phuong_tien'        =>  $request->ma_phuong_tien,
            'ten_phuong_tien'       =>  $request->ten_phuong_tien,
            'tinh_trang'            =>  $request->tinh_trang
        ]);
        return response()->json([
            'status'    =>  true,
            'message'   =>  'Đã tạo mới phương tiện thành công!'
        ]);
    }

    public function searchPhuongTien(Request $request)
    {
        $key = "%" . $request->abc . "%";

        $data   = PhuongTien::where('ten_phuong_tien', 'like', $key)
            ->get();

        return response()->json([
            'status'    =>  true,
            'phuong_tien'  =>  $data,
        ]);
    }

    public function deletePhuongTien($id)
    {
        try {
            PhuongTien::where('id', $id)->delete();
            return response()->json([
                'status'            =>   true,
                'message'           =>   'Xóa phương tiện thành công!',
            ]);
        } catch (Exception $e) {
            Log::info("Lỗi", $e);
            return response()->json([
                'status'            =>   false,
                'message'           =>   'Có lỗi khi xóa phương tiện',
            ]);
        }
    }

    public function updatePhuongTien(Request $request)
    {
        try {
            PhuongTien::where('id', $request->id)
                ->update([
                    'ma_phuong_tien'        =>  $request->ma_phuong_tien,
                    'ten_phuong_tien'       =>  $request->ten_phuong_tien,
                    'tinh_trang'            =>  $request->tinh_trang
                ]);
            return response()->json([
                'status'            =>   true,
                'message'           =>   'Đã cập nhật thành công phương tiện' . $request->ten_phuong_tien,
            ]);
        } catch (Exception $e) {
            Log::info("Lỗi", $e);
            return response()->json([
                'status'            =>   false,
                'message'           =>   'Có lỗi khi cập nhật thông tin phương tiện',
            ]);
        }
    }

    public function doiTinhTrangPhuongTien(Request $request)
    {
        try {
            if ($request->tinh_trang == 1) {
                $tinh_trang_moi = 0;
            } else {
                $tinh_trang_moi = 1;
            }
            PhuongTien::where('id', $request->id)->update([
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
