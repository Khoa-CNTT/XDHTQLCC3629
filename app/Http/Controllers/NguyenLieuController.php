<?php

namespace App\Http\Controllers;

use App\Models\NguyenLieu;
use App\Models\NhaSanXuat;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $user = auth()->user();
        $nhaSanXuat = NhaSanXuat::where('loai_tai_khoan', 'Nhà Sản Xuất')
            ->where('id', $user->id)
            ->first();
        NguyenLieu::create([
            'id_nha_san_xuat'       =>  $nhaSanXuat->id,
            'ma_nguyen_lieu'        =>  $request->ma_nguyen_lieu,
            'ten_nguyen_lieu'       =>  $request->ten_nguyen_lieu,
            'so_luong'              =>  $request->so_luong,
            'don_vi_tinh'           =>  $request->don_vi_tinh,
            'ngay_san_xuat'         =>  $request->ngay_san_xuat,
            'han_su_dung'           =>  $request->han_su_dung,
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
                    'so_luong'              =>  $request->so_luong,
                    'don_vi_tinh'           =>  $request->don_vi_tinh,
                    'ngay_san_xuat'         =>  $request->ngay_san_xuat,
                    'han_su_dung'           =>  $request->han_su_dung,
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
        $user = Auth::guard('sanctum')->user();
        $key = "%" . $request->abc . "%";

        $data = NguyenLieu::join('nha_san_xuats', 'nha_san_xuats.id', '=', 'nguyen_lieus.id_nha_san_xuat')
            ->where('nha_san_xuats.id', $user->id)
            ->where('ten_nguyen_lieu', 'like', $key)
            ->select(
                'nguyen_lieus.id',
                'nguyen_lieus.id_nha_san_xuat',
                'nguyen_lieus.ma_nguyen_lieu',
                'nguyen_lieus.ten_nguyen_lieu',
                'nguyen_lieus.so_luong',
                'nguyen_lieus.don_vi_tinh',
                'nguyen_lieus.ngay_san_xuat',
                'nguyen_lieus.han_su_dung',
                'nguyen_lieus.tinh_trang',
            )
            ->get();
        return response()->json([
            'status'    =>  true,
            'nguyen_lieu'  =>  $data,
        ]);
    }
    public function getDataNgLieuByUser()
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json([
                'message' => 'Bạn cần đăng nhập!',
                'status'  => false,
            ], 401);
        }
        // Dữ liệu cho NhaSanXuat
        if ($user instanceof NhaSanXuat) {
            $id_nha_san_xuat = $user->id;

            $list_nguyen_lieu = NguyenLieu::join('nha_san_xuats', 'nha_san_xuats.id', '=', 'nguyen_lieus.id_nha_san_xuat')
                ->where('nha_san_xuats.id', $id_nha_san_xuat)
                ->select(
                    'nguyen_lieus.id',
                    'nguyen_lieus.id_nha_san_xuat',
                    'nguyen_lieus.ma_nguyen_lieu',
                    'nguyen_lieus.ten_nguyen_lieu',
                    'nguyen_lieus.so_luong',
                    'nguyen_lieus.don_vi_tinh',
                    'nguyen_lieus.ngay_san_xuat',
                    'nguyen_lieus.han_su_dung',
                    'nguyen_lieus.tinh_trang',
                )
                ->get();
            return response()->json([
                'status' => true,
                'nguyen_lieu' => $list_nguyen_lieu,
            ]);
        }
        return response()->json([], 401);
    }
}
