<?php

namespace App\Http\Controllers;

use App\Models\NhaSanXuat;
use App\Models\QuanHuyen;
use App\Models\TinhThanh;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NhaSanXuatController extends Controller
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
        // $id_chuc_nang   = 1;
        // $user   =  Auth::guard('sanctum')->user();
        // $check  =   ChiTietChucNang::where('id_chuc_vu', $user->id_chuc_vu)
        //     ->where('id_chuc_nang', $id_chuc_nang)
        //     ->first();
        // if (!$check) {
        //     return response()->json([
        //         'status'    =>  false,
        //         'message'   =>  'Bạn không đủ quyền truy cập chức năng này!',
        //     ]);
        // }
        $data = NhaSanXuat::get();

        return response()->json([
            'status'    =>  true,
            'nha_san_xuat' => $data
        ]);
    }

    public function createNhaSanXuat(Request $request)
    {
        $ten_tinh =  TinhThanh::find($request->tinh_thanh_id);
        $ten_huyen =  QuanHuyen::where("id", $request->quan_huyen_id)
            ->value("ten_quan_huyen");
        NhaSanXuat::create([
            'ten_cong_ty'   =>  $request->ten_cong_ty,
            'loai_doi_tac'  =>  $request->loai_doi_tac,
            'dia_chi'       =>  $request->dia_chi . ', ' . $ten_huyen . ', ' . $ten_tinh->ten_tinh_thanh,
            'so_dien_thoai' =>  $request->so_dien_thoai,
            'email'         =>  $request->email,
            'ngay_cap_nhat' =>  now(),
            'password'      =>  bcrypt($request->password),
            'tinh_trang'    =>  $request->tinh_trang,
            'kinh_do'       =>  $ten_tinh->kinh_do,
            'vi_do'         =>  $ten_tinh->vi_do,
            'loai_tai_khoan'   => 'Nhà Sản Xuất',
            'dia_chi_vi'   => 'TGdU79UeooERfKuVYm9RPLJXRbG9zPBHSd'
        ]);
        return response()->json([
            'status'    =>  true,
            'message'   =>  'Đã tạo mới NSX thành công!'
        ]);
    }

    public function searchNhaSanXuat(Request $request)
    {
        $key = "%" . $request->abc . "%";

        $data   = NhaSanXuat::where('ten_cong_ty', 'like', $key)
            ->get();

        return response()->json([
            'status'    =>  true,
            'nha_san_xuat'  =>  $data,
        ]);
    }

    public function deleteNhaSanXuat($id)
    {
        try {
            NhaSanXuat::where('id', $id)->delete();
            return response()->json([
                'status'            =>   true,
                'message'           =>   'Xóa nhà sản xuất thành công!',
            ]);
        } catch (Exception $e) {
            Log::info("Lỗi", $e);
            return response()->json([
                'status'            =>   false,
                'message'           =>   'Có lỗi khi xóa nhà sản xuất',
            ]);
        }
    }

    public function updateNhaSanXuat(Request $request)
    {
        $ten_tinh =  TinhThanh::find($request->tinh_thanh_id);
        $ten_huyen =  QuanHuyen::where("id", $request->quan_huyen_id)
            ->value("ten_quan_huyen");
        try {
            NhaSanXuat::where('id', $request->id)
                ->update([
                    'ten_cong_ty'   =>  $request->ten_cong_ty,
                    'loai_doi_tac'  =>  $request->loai_doi_tac,
                    'dia_chi'       =>  $request->dia_chi . ', ' . $ten_huyen . ', ' . $ten_tinh->ten_tinh_thanh,
                    'so_dien_thoai' =>  $request->so_dien_thoai,
                    'email'         =>  $request->email,
                    'ngay_cap_nhat' =>  now(),
                    'tinh_trang'    =>  $request->tinh_trang,
                    'kinh_do'       =>  $ten_tinh->kinh_do,
                    'vi_do'         =>  $ten_tinh->vi_do,
                ]);
            return response()->json([
                'status'            =>   true,
                'message'           =>   'Đã cập nhật thành công nhà sản xuất ' . $request->ten_cong_ty,
            ]);
        } catch (Exception $e) {
            Log::info("Lỗi", $e);
            return response()->json([
                'status'            =>   false,
                'message'           =>   'Có lỗi khi cập nhật thông tin nhà sản xuất',
            ]);
        }
    }

    public function doiTinhTrangNhaSanXuat(Request $request)
    {
        // $id_chuc_nang   = 6;
        // $user   =  Auth::guard('sanctum')->user();
        // $check  =   ChiTietChucNang::where('id_chuc_vu', $user->id_chuc_vu)
        //     ->where('id_chuc_nang', $id_chuc_nang)
        //     ->first();
        // if (!$check) {
        //     return response()->json([
        //         'status'    =>  false,
        //         'message'   =>  'Bạn không đủ quyền truy cập chức năng này!',
        //     ]);
        // }
        try {
            if ($request->tinh_trang == 1) {
                $tinh_trang_moi = 0;
            } else {
                $tinh_trang_moi = 1;
            }
            NhaSanXuat::where('id', $request->id)->update([
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
