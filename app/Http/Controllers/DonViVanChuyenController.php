<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DonViVanChuyen;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DonViVanChuyenController extends Controller
{
    public function getData()
    {
        $data = DonViVanChuyen::get();

        return response()->json([
            'status'    =>  true,
            'data' => $data
        ]);
    }
    public function createDVVC(Request $request)
    {
        DonViVanChuyen::create([
            'ten_cong_ty'        =>  $request->ten_cong_ty,
            'email'              =>  $request->email,
            'password'           =>  bcrypt($request->password),
            'so_dien_thoai'      =>  $request->so_dien_thoai,
            'dia_chi'           =>  $request->dia_chi,
            'cuoc_van_chuyen'   =>  $request->cuoc_van_chuyen,
            'tinh_trang'        =>  $request->tinh_trang
        ]);
        return response()->json([
            'status'    =>  true,
            'message'   =>  'Đã tạo mới đơn vị vận chuyển thành công!'
        ]);
    }

    public function searchDVVC(Request $request)
    {
        $key = "%" . $request->abc . "%";

        $data   = DonViVanChuyen::where('ten_cong_ty', 'like', $key)
            ->get();

        return response()->json([
            'status'    =>  true,
            'data'  =>  $data,
        ]);
    }

    public function deleteDVVC($id)
    {
        try {
            DonViVanChuyen::where('id', $id)->delete();
            return response()->json([
                'status'            =>   true,
                'message'           =>   'Xóa đơn vị thành công!',
            ]);
        } catch (Exception $e) {
            Log::info("Lỗi", $e);
            return response()->json([
                'status'            =>   false,
                'message'           =>   'Có lỗi khi xóa đơn vị',
            ]);
        }
    }

    public function updateDVVC(Request $request)
    {
        try {
            $data   = $request->all();
            DonViVanChuyen::find($request->id)->update($data);
            return response()->json([
                'status'            =>   true,
                'message'           =>   'Đã cập nhật thành công đon vị ' . $request->ten_cong_ty,
            ]);
        } catch (Exception $e) {
            Log::info("Lỗi", $e);
            return response()->json([
                'status'            =>   false,
                'message'           =>   'Có lỗi khi cập nhật thông tin đơn vị',
            ]);
        }
    }

    public function doiTinhTrangDVVC(Request $request)
    {
        try {
            if ($request->tinh_trang == 1) {
                $tinh_trang_moi = 0;
            } else {
                $tinh_trang_moi = 1;
            }
            DonViVanChuyen::where('id', $request->id)->update([
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

    public function getDataForDaiLy()
    {
        $data = DonViVanChuyen::
                select('don_vi_van_chuyens.ten_cong_ty',
                        'don_vi_van_chuyens.cuoc_van_chuyen')
                ->get();

        return response()->json([
            'status'    =>  true,
            'data'      => $data
        ]);
    }
}
