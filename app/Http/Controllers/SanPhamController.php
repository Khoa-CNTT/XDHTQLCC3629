<?php

namespace App\Http\Controllers;

use App\Models\DaiLy;
use App\Models\DanhMucSanPham;
use App\Models\NhaSanXuat;
use App\Models\SanPham;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SanPhamController extends Controller
{
    public function getdata()
    {
        $data = SanPham::join('danh_muc_san_phams', 'san_phams.id_danh_muc','danh_muc_san_phams.id')
                        ->join('san_pham_n_s_x_e_s', 'san_pham_n_s_x_e_s.id_san_pham', 'san_phams.id')
                        ->join('nha_san_xuats', 'nha_san_xuats.id', 'san_pham_n_s_x_e_s.id_nha_san_xuat')
                        ->select('san_phams.*','danh_muc_san_phams.ten_danh_muc', 'nha_san_xuats.ten_cong_ty', 'nha_san_xuats.id as nsx_id')
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
            'tinh_trang'         =>  $request->tinh_trang,
            'hinh_anh'           =>  $request->hinh_anh
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
                    'tinh_trang'         =>  $request->tinh_trang,
                    'hinh_anh'           =>  $request->hinh_anh
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
            ->where('ten_cong_ty', 'like', $key)
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

    //get data theo id nhà sản xuất
    public function getDataByUser(){
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json([
                'message' => 'Bạn cần đăng nhập!',
                'status'  => false,
            ], 401);
        } elseif($user && $user instanceof DaiLy) {
            $list_san_pham = SanPham::join('san_pham_n_s_x_e_s', 'san_phams.id','san_pham_n_s_x_e_s.id_san_pham')
            ->join('nha_san_xuats', 'nha_san_xuats.id', 'san_pham_n_s_x_e_s.id_nha_san_xuat')
            ->where('san_phams.tinh_trang', '1')
            ->select('san_phams.id',
                    'san_phams.ten_san_pham',
                    'nha_san_xuats.ten_cong_ty',
                    'san_phams.hinh_anh',
                    'san_phams.so_luong_ton_kho',
                    'san_phams.gia_ban',
                    'san_phams.don_vi_tinh') //get để nhóm ở groupby
            ->orderBy('nha_san_xuats.id') // Sắp xếp theo nhà sản xuất
            ->get()
            ->groupBy('ten_cong_ty'); // Nhóm theo ID nhà sản xuất
            $check = 2;
            return response()->json([
                'status'    =>      true,
                'data'      =>      $list_san_pham,
                'check'     =>      $check,
            ]);
        }
        // elseif($user && $user instanceof NhaSanXuat) {
        //     $id_nha_san_xuat = $user->id;
        //     // Lấy danh sách sản phẩm của nhà sản xuất này
        //     $list_san_pham = SanPham::join('san_pham_n_s_x_e_s', 'san_pham_n_s_x_e_s.id_san_pham', 'san_phams.id')
        //     ->join('nha_san_xuats', 'nha_san_xuats.id', 'san_pham_n_s_x_e_s.id_nha_san_xuat')
        //     ->select('san_phams.id',
        //             'san_phams.ten_san_pham',
        //             'nha_san_xuats.ten_cong_ty',
        //             'san_phams.hinh_anh')
        //     ->where('nha_san_xuats.id', $id_nha_san_xuat)->get();
        //     $check = 1;
        //     return response()->json([
        //         'status'    =>      true,
        //         'data'      =>      $list_san_pham,
        //         'check'     =>      $check,
        //     ]);
        // }
        else {
            return response()->json([
            ], 401);
        }
    }

    public function getDataByIDSanPham(Request $request){
        $data = SanPham::join('san_pham_n_s_x_e_s', 'san_phams.id','san_pham_n_s_x_e_s.id_san_pham')
        ->join('nha_san_xuats', 'nha_san_xuats.id', 'san_pham_n_s_x_e_s.id_nha_san_xuat')
        ->where('san_phams.id', $request->id)
        ->select('san_phams.id',
                'san_phams.ten_san_pham',
                'nha_san_xuats.ten_cong_ty',
                'nha_san_xuats.id as nha_san_xuat_id',
                'san_phams.hinh_anh',
                'san_phams.so_luong_ton_kho',
                'san_phams.gia_ban',
                'san_phams.don_vi_tinh', //get để nhóm ở groupby
                'san_phams.mo_ta',) //get để nhóm ở groupby
        ->first();

        return response()->json([
            'chi_tiet_san_pham'     =>      $data,
            'status'                =>      true
        ]);
    }
}
