<?php

namespace App\Http\Controllers;

use App\Models\NhaSanXuat;
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
        // $id_chuc_nang   = 3;
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
        NhaSanXuat::create([
            'ten_cong_ty'   =>  $request->ten_cong_ty,
            'loai_doi_tac'  =>  $request->loai_doi_tac,
            'dia_chi'       =>  $request->dia_chi,
            'so_dien_thoai' =>  $request->so_dien_thoai,
            'email'         =>  $request->email,
            'password'      =>  $request->password,
            'tinh_trang'    =>  $request->tinh_trang
        ]);
        return response()->json([
            'status'    =>  true,
            'message'   =>  'Đã tạo mới tỉnh thành thành công!'
        ]);
    }

    public function searchNhaSanXuat(Request $request)
    {
        // $id_chuc_nang   = 2;
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
        // $id_chuc_nang   = 4;
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
        // $id_chuc_nang   = 5;
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
            NhaSanXuat::where('id', $request->id)
                ->update([
                    'ten_cong_ty'   =>  $request->ten_cong_ty,
                    'loai_doi_tac'  =>  $request->loai_doi_tac,
                    'dia_chi'       =>  $request->dia_chi,
                    'so_dien_thoai' =>  $request->so_dien_thoai,
                    'email'         =>  $request->email,
                    // 'ngay_cap_nhat' =>  $request->ngay_cap_nhat,
                    'tinh_trang'    =>  $request->tinh_trang
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
