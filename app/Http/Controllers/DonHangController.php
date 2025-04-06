<?php

namespace App\Http\Controllers;

use App\Models\DaiLy;
use App\Models\DonHang;
use App\Models\LichSuDonHang;
use App\Models\NhanVien;
use App\Models\NhaSanXuat;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DonHangController extends Controller
{
    public function getData(){
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json([
                'message' => 'Bạn cần đăng nhập!',
                'status'  => false,
            ], 401);
        } elseif($user && $user instanceof DaiLy) {
            $user_id = $user->id;
            $list_don_hang = DonHang::
            where('don_hangs.user_id', $user_id)
            ->select('don_hangs.ngay_dat',
                    'don_hangs.ngay_giao',
                    'don_hangs.tong_tien',
                    'don_hangs.tinh_trang',
                    'don_hangs.tinh_trang_thanh_toan',
                    'don_hangs.id')
            ->get();
            return response()->json([
                'status'    =>      true,
                'data'      =>      $list_don_hang,
            ]);
        }
    }

    public function getDataChiTiet(Request $request){
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json([
                'message' => 'Bạn cần đăng nhập!',
                'status'  => false,
            ], 401);
        } elseif($user && $user instanceof DaiLy) {
            $user_id = $user->id;
            $list_chi_tiet_don_hang = LichSuDonHang::
            where('lich_su_don_hangs.user_id', $user_id)
            ->where('lich_su_don_hangs.id_don_hang', $request->id_don_hang)
            ->join('san_phams', 'lich_su_don_hangs.id_san_pham', '=', 'san_phams.id')
            ->join('nha_san_xuats', 'lich_su_don_hangs.id_nha_san_xuat', '=', 'nha_san_xuats.id')
            ->join('don_vi_van_chuyens', 'lich_su_don_hangs.id_don_vi_van_chuyen', '=', 'don_vi_van_chuyens.id')
            ->select(
                'lich_su_don_hangs.*',
                'san_phams.ten_san_pham',
                'san_phams.hinh_anh',
                'nha_san_xuats.ten_cong_ty as ten_nha_san_xuat',
                'don_vi_van_chuyens.ten_cong_ty as ten_dvvc',
                'don_vi_van_chuyens.cuoc_van_chuyen',
            )
            ->get();
            return response()->json([
                'status'    =>      true,
                'data'      =>      $list_chi_tiet_don_hang,
            ]);
        }
    }

    public function huyDonHang(Request $request){
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json([
                'message' => 'Bạn cần đăng nhập!',
                'status'  => false,
            ], 401);
        } elseif($user && $user instanceof DaiLy) {
            try {
                if ($request->tinh_trang == 1 || $request->tinh_trang == 0) {
                    $tinh_trang_moi = 4;
                }
                DonHang::where('id', $request->id)->update([
                    'tinh_trang'    =>  $tinh_trang_moi
                ]);
                return response()->json([
                    'status'            =>   true,
                    'message'           =>   'Hủy đơn hàng thành công!',
                ]);
            } catch (Exception $e) {
                Log::info("Lỗi", $e);
                return response()->json([
                    'status'            =>   false,
                    'message'           =>   'Có lỗi khi hủy đơn hàng',
                ]);
            }
        }
    }

    //admin get dữ liệu
    public function getDataForAdmin(){
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json([
                'message' => 'Bạn cần đăng nhập!',
                'status'  => false,
            ], 401);
        } elseif($user && $user instanceof NhanVien) {
            $list_don_hang = DonHang::
            join('dai_lies', 'dai_lies.id', 'don_hangs.user_id')
            ->select('don_hangs.ngay_dat',
                    'don_hangs.ngay_giao',
                    'don_hangs.tong_tien',
                    'don_hangs.tinh_trang',
                    'don_hangs.tinh_trang_thanh_toan',
                    'don_hangs.id',
                    'dai_lies.ten_cong_ty as ten_dai_ly')
            ->get();
            return response()->json([
                'status'    =>      true,
                'data'      =>      $list_don_hang,
            ]);
        }
    }

    public function huyDonHangAdmin(Request $request){
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json([
                'message' => 'Bạn cần đăng nhập!',
                'status'  => false,
            ], 401);
        } elseif($user && $user instanceof NhanVien) {
            try {
                if ($request->tinh_trang == 1 || $request->tinh_trang == 0) {
                    $tinh_trang_moi = 4;
                }
                DonHang::where('id', $request->id)->update([
                    'tinh_trang'    =>  $tinh_trang_moi
                ]);
                LichSuDonHang::where('id_don_hang', $request->id)->update([
                    'tinh_trang'    =>  $tinh_trang_moi
                ]);
                return response()->json([
                    'status'            =>   true,
                    'message'           =>   'Hủy đơn hàng thành công!',
                ]);
            } catch (Exception $e) {
                Log::info("Lỗi", $e);
                return response()->json([
                    'status'            =>   false,
                    'message'           =>   'Có lỗi khi hủy đơn hàng',
                ]);
            }
        }
    }

    public function getDataChiTietAdmin(Request $request){
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json([
                'message' => 'Bạn cần đăng nhập!',
                'status'  => false,
            ], 401);
        } elseif($user && $user instanceof NhanVien) {
            $list_chi_tiet_don_hang = LichSuDonHang::
            where('lich_su_don_hangs.id_don_hang', $request->id_don_hang)
            ->join('san_phams', 'lich_su_don_hangs.id_san_pham', '=', 'san_phams.id')
            ->join('nha_san_xuats', 'lich_su_don_hangs.id_nha_san_xuat', '=', 'nha_san_xuats.id')
            ->join('don_vi_van_chuyens', 'lich_su_don_hangs.id_don_vi_van_chuyen', '=', 'don_vi_van_chuyens.id')
            ->select(
                'lich_su_don_hangs.*',
                'san_phams.ten_san_pham',
                'san_phams.hinh_anh',
                'nha_san_xuats.ten_cong_ty as ten_nha_san_xuat',
                'don_vi_van_chuyens.ten_cong_ty as ten_dvvc',
                'don_vi_van_chuyens.cuoc_van_chuyen',
            )
            ->get();
            return response()->json([
                'status'    =>      true,
                'data'      =>      $list_chi_tiet_don_hang,
            ]);
        }
    }

    public function xacNhanDonHangAdmin(Request $request){
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json([
                'message' => 'Bạn cần đăng nhập!',
                'status'  => false,
            ], 401);
        } elseif($user && $user instanceof NhanVien) {
            try {
                $id_nguoi_duyet = $user->id;
                if ($request->tinh_trang == 0) {
                    $tinh_trang_moi = 1;
                }
                DonHang::where('id', $request->id)->update([
                    'tinh_trang'        =>  $tinh_trang_moi,
                    'id_nguoi_duyet'    =>  $id_nguoi_duyet
                ]);
                LichSuDonHang::where('id_don_hang', $request->id)->update([
                    'tinh_trang'        =>  $tinh_trang_moi,
                ]);
                return response()->json([
                    'status'            =>   true,
                    'message'           =>   'Xác nhận đơn hàng thành công!',
                ]);
            } catch (Exception $e) {
                Log::info("Lỗi", $e);
                return response()->json([
                    'status'            =>   false,
                    'message'           =>   'Có lỗi khi xác nhận đơn hàng',
                ]);
            }
        }
    }

    // nhà sản xuất
    public function getDataForNSX(){
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json([
                'message' => 'Bạn cần đăng nhập!',
                'status'  => false,
            ], 401);
        } elseif($user && $user instanceof NhaSanXuat) {
            $user_id = $user->id;
            $list_don_hang = LichSuDonHang::
                where('lich_su_don_hangs.id_nha_san_xuat', $user_id)
                ->where('lich_su_don_hangs.tinh_trang', '!=', 0)
                ->join('san_phams', 'san_phams.id', 'lich_su_don_hangs.id_san_pham')
                ->join('don_vi_van_chuyens', 'don_vi_van_chuyens.id', 'lich_su_don_hangs.id_don_vi_van_chuyen')
                ->join('dai_lies', 'dai_lies.id', 'lich_su_don_hangs.user_id')
                ->join('don_hangs', 'don_hangs.id', 'lich_su_don_hangs.id_don_hang')
                ->select(
                    'lich_su_don_hangs.*',
                    'san_phams.ten_san_pham',
                    'san_phams.hinh_anh',
                    'don_vi_van_chuyens.ten_cong_ty as ten_dvvc',
                    'dai_lies.ten_cong_ty as ten_khach_hang',
                    'don_hangs.ngay_dat',
                    'don_hangs.tinh_trang_thanh_toan',
                )
                ->get();
            return response()->json([
                'status'    =>      true,
                'data'      =>      $list_don_hang,
            ]);
        }
    }

    public function xacNhanDonHangNSX(Request $request){
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json([
                'message' => 'Bạn cần đăng nhập!',
                'status'  => false,
            ], 401);
        } elseif($user && $user instanceof NhaSanXuat) {
            try {
                if ($request->tinh_trang == 1) {
                    $tinh_trang_moi_nsx = 2;
                }
                LichSuDonHang::where('id_don_hang', $request->id_don_hang)->update([
                    'tinh_trang'        =>  $tinh_trang_moi_nsx,
                ]);
                return response()->json([
                    'status'            =>   true,
                    'message'           =>   'Xác nhận thành công!',
                ]);
            } catch (Exception $e) {
                Log::info("Lỗi", $e);
                return response()->json([
                    'status'            =>   false,
                    'message'           =>   'Có lỗi khi xác nhận',
                ]);
            }
        }
    }
}
