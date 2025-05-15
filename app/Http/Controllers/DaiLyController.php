<?php

namespace App\Http\Controllers;

use App\Models\DaiLy;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DaiLyController extends Controller
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
        $data = DaiLy::get();

        return response()->json([
            'status'    =>  true,
            'dai_ly' => $data
        ]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function createDaiLy(Request $request)
    {
        DaiLy::create([
            'ten_cong_ty'   =>  $request->ten_cong_ty,
            'email'         =>  $request->email,
            'password'      =>  bcrypt($request->password),
            'dia_chi'       =>  $request->dia_chi,
            'so_dien_thoai' =>  $request->so_dien_thoai,
            'tinh_trang'    =>  $request->tinh_trang
        ]);
        return response()->json([
            'status'    =>  true,
            'message'   =>  'Đã tạo mới đại lý thành công!'
        ]);
    }

    public function searchDaiLy(Request $request)
    {
        $key = "%" . $request->abc . "%";

        $data   = DaiLy::where('ten_cong_ty', 'like', $key)
            ->get();

        return response()->json([
            'status'    =>  true,
            'dai_ly'  =>  $data,
        ]);
    }

    public function deleteDaiLy($id)
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
            DaiLy::where('id', $id)->delete();
            return response()->json([
                'status'            =>   true,
                'message'           =>   'Xóa đại lý thành công!',
            ]);
        } catch (Exception $e) {
            Log::info("Lỗi", $e);
            return response()->json([
                'status'            =>   false,
                'message'           =>   'Có lỗi khi xóa đại lý',
            ]);
        }
    }

    public function updateDaiLy(Request $request)
    {
        try {
            DaiLy::where('id', $request->id)
                ->update([
                    'ten_cong_ty'   =>  $request->ten_cong_ty,
                    'email'         =>  $request->email,
                    'dia_chi'       =>  $request->dia_chi,
                    'so_dien_thoai' =>  $request->so_dien_thoai,
                    'tinh_trang'    =>  $request->tinh_trang
                ]);
            return response()->json([
                'status'            =>   true,
                'message'           =>   'Đã cập nhật thành công đại lýlý ' . $request->ten_cong_ty,
            ]);
        } catch (Exception $e) {
            Log::info("Lỗi", $e);
            return response()->json([
                'status'            =>   false,
                'message'           =>   'Có lỗi khi cập nhật thông tin đại lý',
            ]);
        }
    }

    public function doiTinhTrangDaiLy(Request $request)
    {
        try {
            if ($request->tinh_trang == 1) {
                $tinh_trang_moi = 0;
            } else {
                $tinh_trang_moi = 1;
            }
            DaiLy::where('id', $request->id)->update([
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
