<?php

namespace App\Http\Controllers;

use App\Models\DaiLy;
use App\Models\DonHang;
use App\Models\DonViVanChuyen;
use App\Models\KhoTrungChuyen;
use App\Models\LichSuDonHang;
use App\Models\LichSuVanChuyen;
use App\Models\NhanVien;
use App\Models\NhaSanXuat;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Services\PathFindingService;

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
    public function getDataNSXchoTrangChu()
    {
        $data = NhaSanXuat::get();

        return response()->json([
            'status'    =>  true,
            'nha_san_xuat_for_homepage' => $data
        ]);
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
                'lich_su_don_hangs.tinh_trang',
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

    // public function xacNhanDonHangDaiLy(Request $request){
    //     $user = Auth::guard('sanctum')->user();
    //     if (!$user) {
    //         return response()->json([
    //             'message' => 'Bạn cần đăng nhập!',
    //             'status'  => false,
    //         ], 401);
    //     } elseif($user instanceof DaiLy) {
    //         try {
    //             DB::beginTransaction();

    //             if ($request->tinh_trang != 6) {
    //                 return response()->json([
    //                     'message' => 'Trạng thái không hợp lệ để xác nhận!',
    //                     'status'  => false,
    //                 ]);
    //             }
    //             $tinh_trang_moi = 3;
    //             // Cập nhật đơn hàng
    //             DonHang::where('id', $request->id)->update([
    //                 'tinh_trang' => $tinh_trang_moi,
    //             ]);
    //             // Cập nhật chi tiết đơn hàng
    //             if (is_array($request->san_phams)) {
    //                 foreach ($request->san_phams as $sp) {
    //                     if (isset($sp['id_lich_su_don_hang'])) {
    //                         LichSuDonHang::where('id', $sp['id_lich_su_don_hang'])->update([
    //                             'tinh_trang' => $tinh_trang_moi,
    //                         ]);
    //                     }
    //                 }
    //             }
    //             // Cập nhật tình trạng chặng cuối của lịch sử vận chuyển
    //             $changCuoi = LichSuVanChuyen::where('id_don_hang', $request->id)
    //                 ->whereNull('id_kho_hang')
    //                 ->whereNull('thoi_gian_di')
    //                 ->orderByDesc('thu_tu') //lấy cái số thứ tự cuối cùng của id đơn hàng
    //                 ->first();
    //             if ($changCuoi) {
    //                 $changCuoi->tinh_trang = 2;
    //                 $changCuoi->thoi_gian_di = Carbon::now('Asia/Ho_Chi_Minh');
    //                 $changCuoi->save();
    //             }
    //             DB::commit();
    //             return response()->json([
    //                 'status'  => true,
    //                 'message' => 'Xác nhận thành công!',
    //             ]);
    //         } catch (\Exception $e) {
    //             DB::rollBack();
    //             Log::error('Lỗi xác nhận của đại lý: ' . $e->getMessage());
    //             return response()->json([
    //                 'status'  => false,
    //                 'message' => 'Lỗi: ' . $e->getMessage(),
    //             ]);
    //         }
    //     }
    // }
    public function xacNhanDonHangDaiLy(Request $request) {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json([
                'message' => 'Bạn cần đăng nhập!',
                'status'  => false,
            ], 401);
        } elseif ($user instanceof DaiLy) {
            try {
                DB::beginTransaction();

                if ($request->tinh_trang != 6) {
                    return response()->json([
                        'message' => 'Trạng thái không hợp lệ để xác nhận!',
                        'status'  => false,
                    ]);
                }

                // Kiểm tra tất cả sản phẩm đã đạt trạng thái 6 chưa
                $countChuaHoanThanh = LichSuDonHang::where('id_don_hang', $request->id)
                    ->where('tinh_trang', '!=', 6)
                    ->count();

                if ($countChuaHoanThanh > 0) {
                    return response()->json([
                        'message' => 'Vẫn còn sản phẩm chưa hoàn thành. Không thể xác nhận!',
                        'status'  => false,
                    ]);
                }

                $tinh_trang_moi = 3;

                // Cập nhật đơn hàng
                DonHang::where('id', $request->id)->update([
                    'tinh_trang' => $tinh_trang_moi,
                ]);

                // Cập nhật toàn bộ chi tiết đơn hàng về tình trạng 3
                LichSuDonHang::where('id_don_hang', $request->id)->update([
                    'tinh_trang' => $tinh_trang_moi,
                ]);

                // Cập nhật tình trạng chặng cuối của lịch sử vận chuyển
                $changCuois = LichSuVanChuyen::where('id_don_hang', $request->id)
                            ->whereNull('id_kho_hang')
                            ->whereNull('thoi_gian_di')
                            ->get();

                foreach ($changCuois as $changCuoi) {
                    $changCuoi->update([
                        'tinh_trang'   => 2,
                        'thoi_gian_di' => Carbon::now('Asia/Ho_Chi_Minh'),
                    ]);
                }

                DB::commit();
                return response()->json([
                    'status'  => true,
                    'message' => 'Xác nhận thành công!',
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Lỗi xác nhận của đại lý: ' . $e->getMessage());
                return response()->json([
                    'status'  => false,
                    'message' => 'Lỗi: ' . $e->getMessage(),
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
                    'don_hangs.user_id',
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
    public function getDataChiTietForNSX(Request $request){
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
                ->where('lich_su_don_hangs.id_don_hang', '=', $request->id_don_hang)
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
                    'dai_lies.id as user_id',
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

    public function getDataForNSX(){
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'message' => 'Bạn cần đăng nhập!',
                'status'  => false,
            ], 401);
        }

        if ($user instanceof NhaSanXuat) {
            try {
                $user_id = $user->id;

                $list_don_hang = LichSuDonHang::where('lich_su_don_hangs.id_nha_san_xuat', $user_id)
                    ->whereNotIn('lich_su_don_hangs.tinh_trang', [0])
                    ->join('san_phams', 'san_phams.id', 'lich_su_don_hangs.id_san_pham')
                    ->join('dai_lies', 'dai_lies.id', 'lich_su_don_hangs.user_id')
                    ->join('don_vi_van_chuyens', 'don_vi_van_chuyens.id', 'lich_su_don_hangs.id_don_vi_van_chuyen')
                    ->join('don_hangs', 'don_hangs.id', 'lich_su_don_hangs.id_don_hang')
                    ->select(
                        'lich_su_don_hangs.*',
                        'san_phams.ten_san_pham',
                        'san_phams.hinh_anh',
                        'don_vi_van_chuyens.ten_cong_ty as ten_dvvc',
                        'dai_lies.ten_cong_ty as ten_khach_hang',
                        'don_hangs.ngay_dat',
                        'dai_lies.id as user_id',
                        'don_hangs.tinh_trang_thanh_toan',
                        'don_hangs.tinh_trang as tinh_trang_don_hang',
                        'lich_su_don_hangs.id as id_lich_su_don_hang',
                        'lich_su_don_hangs.tinh_trang as tinh_trang_chi_tiet_don_hang',
                    )
                    ->get();

                // Gộp lại theo đơn hàng
                $grouped = $list_don_hang->groupBy('id_don_hang')->map(function ($items, $id) {
                    $first = $items->first();

                    // Tổng tiền sản phẩm = ∑ (giá × số lượng)
                    $tong_tien_san_pham = $items->sum(function ($item) {
                        return $item->don_gia * $item->so_luong;
                    });

                    //Gom theo id_nha_san_xuat để tránh trùng cước
                    $cuoc_theo_nsx = $items->groupBy('id_nha_san_xuat')->map(function ($group) {
                        return $group->first()->cuoc_van_chuyen; // Chỉ lấy 1 dòng đại diện
                    });

                    $tong_cuoc_van_chuyen = $cuoc_theo_nsx->sum(); // Tổng cước theo NSX

                    return [
                        'id_don_hang'            => $id,
                        'user_id'                => $first->user_id,
                        'ten_khach_hang'         => $first->ten_khach_hang,
                        'ngay_dat'               => $first->ngay_dat,
                        'tinh_trang_thanh_toan'  => $first->tinh_trang_thanh_toan,
                        'tinh_trang_don_hang'    => $first->tinh_trang_don_hang,
                        'tong_tien_san_pham'     => $tong_tien_san_pham,
                        'tong_cuoc_van_chuyen'   => $tong_cuoc_van_chuyen,
                        'tong_tien_don_hang'     => $tong_tien_san_pham + $tong_cuoc_van_chuyen,
                        'id_nha_san_xuat'        => $first->id_nha_san_xuat,
                        'tinh_trang_chi_tiet_don_hang'        => $first->tinh_trang_chi_tiet_don_hang,
                        'san_phams'              => $items->map(function ($item) {
                            return [
                                'id_san_pham'           => $item->id_san_pham,
                                'ten_san_pham'          => $item->ten_san_pham,
                                'hinh_anh'              => $item->hinh_anh,
                                'so_luong'              => $item->so_luong,
                                'cuoc_van_chuyen'       => $item->cuoc_van_chuyen,
                                'ten_dvvc'              => $item->ten_dvvc,
                                'id_lich_su_don_hang'   => $item->id_lich_su_don_hang,
                                'id_nha_san_xuat'       => $item->id_nha_san_xuat,
                                'tinh_trang_chi_tiet_don_hang'        => $item->tinh_trang_chi_tiet_don_hang
                            ];
                        })->values()
                    ];
                })->values();

                return response()->json([
                    'status' => true,
                    'data'   => $grouped,
                ]);
            } catch (\Exception $e) {
                Log::error("Lỗi khi lấy dữ liệu đơn hàng cho NSX: " . $e->getMessage());
                return response()->json([
                    'status'  => false,
                    'message' => 'Đã xảy ra lỗi khi xử lý dữ liệu',
                ]);
            }
        }
    }

    public function xacNhanDonHangNSX(Request $request){
        $user = Auth::guard('sanctum')->user();
        if (!$user || !($user instanceof NhaSanXuat)) {
            return response()->json([
                'message' => 'Bạn cần đăng nhập với tư cách nhà sản xuất!',
                'status'  => false,
            ], 401);
        }

        try {
            DB::beginTransaction();

            if ($request->tinh_trang_don_hang != 1) {
                return response()->json([
                    'message' => 'Trạng thái không hợp lệ để xác nhận!',
                    'status'  => false,
                ]);
            }

            $tinh_trang_moi_nsx = 2;

            // Cập nhật các sản phẩm của nhà sản xuất hiện tại
            foreach ($request->san_phams as $sp) {
                if (isset($sp['id_lich_su_don_hang']) && $sp['id_nha_san_xuat'] == $user->id) {
                    LichSuDonHang::where('id', $sp['id_lich_su_don_hang'])->update([
                        'tinh_trang' => $tinh_trang_moi_nsx,
                    ]);
                }
            }

            // Kiểm tra toàn bộ sản phẩm trong đơn hàng đã xác nhận hết chưa
            $chua_xac_nhan = LichSuDonHang::where('id_don_hang', $request->id_don_hang)
                ->where('tinh_trang', '!=', $tinh_trang_moi_nsx)
                ->exists();

            if (!$chua_xac_nhan) {
                DonHang::where('id', $request->id_don_hang)->update([
                    'tinh_trang' => $tinh_trang_moi_nsx,
                ]);
            }

            DB::commit();
            return response()->json([
                'status'  => true,
                'message' => 'Xác nhận thành công!',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi xác nhận của nsx: ' . $e->getMessage());

            return response()->json([
                'status'  => false,
                'message' => 'Lỗi: ' . $e->getMessage(),
            ]);
        }
    }

    //đơn vị vận chuyển
    public function getDataForDVVC() {
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'message' => 'Bạn cần đăng nhập!',
                'status'  => false,
            ], 401);
        }

        if ($user instanceof DonViVanChuyen) {
            try {
                $user_id = $user->id;

                $list_don_hang = LichSuDonHang::where('lich_su_don_hangs.id_don_vi_van_chuyen', $user_id)
                    ->whereNotIn('lich_su_don_hangs.tinh_trang', [0, 1])
                    ->join('san_phams', 'san_phams.id', 'lich_su_don_hangs.id_san_pham')
                    ->join('dai_lies', 'dai_lies.id', 'lich_su_don_hangs.user_id')
                    ->join('nha_san_xuats', 'nha_san_xuats.id', 'lich_su_don_hangs.id_nha_san_xuat')
                    ->join('don_hangs', 'don_hangs.id', 'lich_su_don_hangs.id_don_hang')
                    ->select(
                        'lich_su_don_hangs.*',
                        'san_phams.ten_san_pham',
                        'san_phams.hinh_anh',
                        'nha_san_xuats.ten_cong_ty as ten_nsx',
                        'dai_lies.ten_cong_ty as ten_khach_hang',
                        'don_hangs.ngay_dat',
                        'dai_lies.id as user_id',
                        'don_hangs.tinh_trang_thanh_toan',
                        'don_hangs.tinh_trang as tinh_trang_don_hang',
                        'lich_su_don_hangs.id as id_lich_su_don_hang',
                        'lich_su_don_hangs.id_don_vi_van_chuyen as id_dvvc',
                        'nha_san_xuats.id as id_nsx',
                        'dai_lies.dia_chi as dia_chi_dai_ly',
                    )
                    ->get();

                // Gộp lại theo đơn hàng
                $grouped = $list_don_hang->groupBy('id_don_hang')->map(function ($items, $id) {
                    $first = $items->first();

                    // Tổng tiền sản phẩm = ∑ (giá × số lượng)
                    $tong_tien_san_pham = $items->sum(function ($item) {
                        return $item->don_gia * $item->so_luong;
                    });

                    //Gom theo id_nha_san_xuat để tránh trùng cước
                    $cuoc_theo_nsx = $items->groupBy('id_nha_san_xuat')->map(function ($group) {
                        return $group->first()->cuoc_van_chuyen; // Chỉ lấy 1 dòng đại diện
                    });

                    $tong_cuoc_van_chuyen = $cuoc_theo_nsx->sum(); // Tổng cước theo NSX

                    return [
                        'id_don_hang'            => $id,
                        'user_id'                => $first->user_id,
                        'ten_khach_hang'         => $first->ten_khach_hang,
                        'ngay_dat'               => $first->ngay_dat,
                        'tinh_trang_thanh_toan'  => $first->tinh_trang_thanh_toan,
                        'tinh_trang_don_hang'    => $first->tinh_trang_don_hang,
                        'tong_tien_san_pham'     => $tong_tien_san_pham,
                        'tong_cuoc_van_chuyen'   => $tong_cuoc_van_chuyen,
                        'tong_tien_don_hang'     => $tong_tien_san_pham + $tong_cuoc_van_chuyen,
                        'id_dvvc'                => $first->id_dvvc,
                        'id_nsx'                 => $first->id_nsx,
                        'id_cac_nsx'             => $items->pluck('id_nsx')->unique()->values()->toArray(),
                        'dia_chi_dai_ly'         => $first->dia_chi_dai_ly,
                        'san_phams'              => $items->map(function ($item) {
                            return [
                                'id_san_pham'       => $item->id_san_pham,
                                'ten_san_pham'      => $item->ten_san_pham,
                                'hinh_anh'          => $item->hinh_anh,
                                'so_luong'          => $item->so_luong,
                                'tinh_trang'        => $item->tinh_trang,
                                'cuoc_van_chuyen'   => $item->cuoc_van_chuyen,
                                'ten_nsx'           => $item->ten_nsx,
                                'id_lich_su_don_hang'    => $item->id_lich_su_don_hang,
                                'id_nsx_test'=>$item->id_nha_san_xuat
                            ];
                        })->values()
                    ];
                })->values();

                return response()->json([
                    'status' => true,
                    'data'   => $grouped,
                ]);
            } catch (\Exception $e) {
                Log::error("Lỗi khi lấy dữ liệu đơn hàng cho DVVC: " . $e->getMessage());
                return response()->json([
                    'status'  => false,
                    'message' => 'Đã xảy ra lỗi khi xử lý dữ liệu',
                ]);
            }
        }
    }

    public function xacNhanDonHangDVVC(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json([
                'message' => 'Bạn cần đăng nhập!',
                'status'  => false,
            ], 401);
        }

        if (!($user instanceof DonViVanChuyen)) {
            return response()->json([
                'message' => 'Bạn không có quyền thực hiện hành động này!',
                'status'  => false,
            ], 403);
        }

        try {
            DB::beginTransaction();
            // Kiểm tra trạng thái đơn hàng
            $data_res = $request->id_don_hang;

            $donHang = DonHang::findOrFail($data_res['id_don_hang']);
            if ($donHang->tinh_trang != 2) {
                return response()->json([
                    'message' => 'Đơn hàng chưa sẵn sàng để xác nhận vận chuyển!',
                    'status'  => false,
                ]);
            }

            // Kiểm tra còn sản phẩm nào chưa được NSX chuẩn bị xong hay không
            $sanPhamsChuaXong = LichSuDonHang::where('id_don_hang', $data_res["id_don_hang"])
                ->whereIn('tinh_trang', [0, 1])
                ->count();

            if ($sanPhamsChuaXong > 0) {
                return response()->json([
                    'message' => 'Không thể xác nhận vận chuyển vì còn sản phẩm chưa chuẩn bị xong!',
                    'status'  => false,
                ]);
            }

            // Cập nhật trạng thái đơn hàng và sản phẩm
            $tinhTrangMoi = 5;
            $donHang->update(['tinh_trang' => $tinhTrangMoi]);

            foreach ($data_res['san_phams'] as $sp) {
                if (isset($sp['id_lich_su_don_hang'])) {
                    LichSuDonHang::where('id', $sp['id_lich_su_don_hang'])->update([
                        'tinh_trang' => $tinhTrangMoi,
                    ]);
                }
            }

            // Lấy danh sách NSX và đại lý
            $danhSachNhaSanXuat = $request->input('id_don_hang.id_cac_nsx');
            $idDaiLy = $request->id_dai_ly;
            $thuTu = 1;
            $tuyenSo = 1;

            // Với mỗi nhà sản xuất, tạo lộ trình và lịch sử vận chuyển
            foreach ($danhSachNhaSanXuat as $nsxId) {
                // Tìm đường đi cho nhà sản xuất
                $tuyen = $this->pathFindingService->findShortestPathMultipleNSX([$nsxId], $idDaiLy);
                // Kiểm tra xem có tìm thấy tuyến đường hợp lệ không
                if (!isset($tuyen[0]['path_ids']) || empty($tuyen[0]['path_ids'])) {
                    throw new \Exception("Không tìm thấy tuyến đường hợp lệ cho nhà sản xuất ID: $nsxId.");
                }

                // Tạo lịch sử vận chuyển cho nhà sản xuất
                $nsx = NhaSanXuat::find($nsxId);
                if (!$nsx) {
                    throw new \Exception("Không tìm thấy nhà sản xuất ID: $nsxId");
                }

                // 1. Điểm đầu: Nhà sản xuất
                LichSuVanChuyen::create([
                    'id_don_hang'          => $donHang->id,
                    'id_kho_hang'          => null,
                    'id_don_vi_van_chuyen' => $user->id,
                    'id_nha_san_xuat'      => $nsxId,
                    'id_dai_ly'            => $idDaiLy,
                    'thoi_gian_den'        => null,
                    'thoi_gian_di'         => null,
                    'thu_tu'               => $thuTu++,
                    'mo_ta'                => 'Vị trí nhà sản xuất',
                    'tinh_trang'           => 0,
                    'tuyen_so'             => $tuyenSo,
                ]);

                // 2. Các điểm kho trung chuyển
                foreach ($tuyen[0]['path_ids'] as $index => $diem) {
                    if (is_string($diem) && Str::startsWith($diem, 'kho_')) {
                        $idKho = (int) Str::after($diem, 'kho_');
                        $moTa = $tuyen[0]['path_names'][$index] ?? 'Kho trung chuyển';

                        LichSuVanChuyen::create([
                            'id_don_hang'          => $donHang->id,
                            'id_kho_hang'          => $idKho,
                            'id_don_vi_van_chuyen' => $user->id,
                            'id_nha_san_xuat'      => $nsxId,
                            'id_dai_ly'            => $idDaiLy,
                            'thoi_gian_den'        => null,
                            'thoi_gian_di'         => null,
                            'thu_tu'               => $thuTu++,
                            'mo_ta'                => $moTa,
                            'tinh_trang'           => 0,
                            'tuyen_so'             => $tuyenSo,
                        ]);
                    }
                }

                // 3. Điểm cuối: Đại lý
                LichSuVanChuyen::create([
                    'id_don_hang'          => $donHang->id,
                    'id_kho_hang'          => null,
                    'id_don_vi_van_chuyen' => $user->id,
                    'id_nha_san_xuat'      => $nsxId,
                    'id_dai_ly'            => $idDaiLy,
                    'thoi_gian_den'        => null,
                    'thoi_gian_di'         => null,
                    'thu_tu'               => $thuTu++,
                    'mo_ta'                => 'Vị trí đại lý',
                    'tinh_trang'           => 0,
                    'tuyen_so'             => $tuyenSo,
                ]);
                $tuyenSo++;
            }

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Xác nhận vận chuyển thành công!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi xác nhận đơn vị vận chuyển: ' . $e->getMessage());

            return response()->json([
                'status'  => false,
                'message' => 'Lỗi: ' . $e->getMessage(),
            ]);
        }
    }

    public function getDataChiTietForDVVC(Request $request){
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json([
                'message' => 'Bạn cần đăng nhập!',
                'status'  => false,
            ], 401);
        }
        if ($user instanceof DonViVanChuyen) {
            $user_id = $user->id;
            try{
                $list_chi_tiet_don_hang = LichSuDonHang::
                where('lich_su_don_hangs.id_don_vi_van_chuyen', $user_id)
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
                    'lich_su_don_hangs.tinh_trang',
                    'nha_san_xuats.dia_chi as dia_chi_nsx',
                    'nha_san_xuats.id as id_nsx',
                    'lich_su_don_hangs.user_id as id_dai_ly'
                )
                ->get();
                return response()->json([
                    'status'    =>      true,
                    'data'      =>      $list_chi_tiet_don_hang,
                ]);
            } catch (\Exception $e) {
                Log::error("Lỗi khi lấy chi tiết đơn hàng cho DVVC: " . $e->getMessage());

                return response()->json([
                    'status'  => false,
                    'message' => 'Đã xảy ra lỗi khi xử lý dữ liệu.',
                ]);
            }
        }
    }

    protected $pathFindingService;

    public function __construct(PathFindingService $pathFindingService)
    {
        $this->pathFindingService = $pathFindingService;
    }

    // Gợi ý tuyến đường từ nhiều NSX đến đại lý
    public function goiYDuongDi(Request $request)
    {
        $request->validate([
            'danh_sach_nha_san_xuat' => 'required|array|min:1',
            'danh_sach_nha_san_xuat.*' => 'exists:nha_san_xuats,id',
            'id_dai_ly' => 'required|exists:dai_lies,id',
        ]);
        $nhaSanXuatIds = $request->danh_sach_nha_san_xuat;
        $daiLyId = $request->id_dai_ly;
        $nhaSanXuatNames = \App\Models\NhaSanXuat::whereIn('id', $nhaSanXuatIds)
                                                ->pluck('ten_cong_ty', 'id')
                                                ->toArray();
        $results = $this->pathFindingService->findShortestPathMultipleNSX($nhaSanXuatIds, $daiLyId);
        if (empty($results)) {
            return response()->json([
                'error' => 'Không có kế hoạch vận chuyển khả thi.'
            ], 400);
        }
        $totalDistance = array_sum(array_column($results, 'distance'));
        foreach ($results as &$tuyen) {
            $tuyen['nha_san_xuat_name'] = $nhaSanXuatNames[$tuyen['nha_san_xuat_id']] ?? 'Tên không có sẵn';
        }
        return response()->json([
            'success' => true,
            'data' => $results,
            'total_distance' => round($totalDistance, 2) . ' km',
        ]);
    }

    // search cho đơn hàng bên Admin
    public function searchDonHangAdmin(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        $key = "%" . $request->abc . "%";

        $data = DonHang::join('dai_lies', 'dai_lies.id', 'don_hangs.user_id')
                        ->where('ten_cong_ty', 'like', $key)
                        ->select('don_hangs.ngay_dat',
                                'don_hangs.user_id',
                                'don_hangs.ngay_giao',
                                'don_hangs.tong_tien',
                                'don_hangs.tinh_trang',
                                'don_hangs.tinh_trang_thanh_toan',
                                'don_hangs.id',
                                'dai_lies.ten_cong_ty as ten_dai_ly')
                        ->get();
        return response()->json([
            'status'    =>      true,
            'data'      =>      $data,
        ]);
    }

    public function getLichTrinh(Request $request){
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json([
                'message' => 'Bạn cần đăng nhập!',
                'status'  => false,
            ], 401);
        }
        if ($user instanceof DonViVanChuyen) {
            $user_id = $user->id;
            try{
                $list_lich_trinh = LichSuVanChuyen::
                where('lich_su_van_chuyens.id_don_vi_van_chuyen', $user_id)
                ->where('lich_su_van_chuyens.id_don_hang', $request->id_don_hang)
                ->leftJoin('kho_trung_chuyens', 'lich_su_van_chuyens.id_kho_hang', '=', 'kho_trung_chuyens.id')
                ->leftJoin('dai_lies', 'lich_su_van_chuyens.id_dai_ly', '=', 'dai_lies.id')
                ->leftJoin('nha_san_xuats', 'lich_su_van_chuyens.id_nha_san_xuat', '=', 'nha_san_xuats.id')
                ->select(
                    'lich_su_van_chuyens.*',
                    'kho_trung_chuyens.ten_kho',
                    'kho_trung_chuyens.dia_chi as dia_chi_kho',
                    'nha_san_xuats.dia_chi as dia_chi_nsx',
                    'dai_lies.dia_chi as dia_chi_dai_ly',
                )
                ->get();
                return response()->json([
                    'status'    =>      true,
                    'data'      =>      $list_lich_trinh,
                ]);
            } catch (\Exception $e) {
                Log::error("Lỗi khi lấy lịch trình đơn hàng cho DVVC: " . $e->getMessage());
                return response()->json([
                    'status'  => false,
                    'message' => 'Đã xảy ra lỗi khi xử lý dữ liệu.',
                ]);
            }
        }
    }

    public function xacNhanDen(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user || !$user instanceof DonViVanChuyen) {
            return response()->json([
                'status' => false,
                'message' => 'Không xác thực'
            ], 401);
        }
        $lichTrinh = LichSuVanChuyen::find($request->id);
        if (!$lichTrinh) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy lịch trình'
            ], 404);
        }
        if ($lichTrinh->thoi_gian_den) {
            return response()->json([
                'status' => false,
                'message' => 'Đã xác nhận đến rồi'
            ], 400);
        }
        $lichTrinh->thoi_gian_den = Carbon::now('Asia/Ho_Chi_Minh');
        $lichTrinh->tinh_trang = 1; // Đã đến
        $lichTrinh->save();
        // Nếu là chặng cuối (chứa địa chỉ đại lý, không phải kho trung chuyển)
        if (!$lichTrinh->thoi_gian_di && !$lichTrinh->ten_kho) {
            // Tìm đơn hàng tương ứng
            $donHang = DonHang::find($lichTrinh->id_don_hang);
            if ($donHang) {
                $donHang->tinh_trang = 6; // Đã giao – chờ đại lý xác nhận
                $donHang->save();

                LichSuDonHang::where('id_don_hang', $donHang->id)
                ->update(['tinh_trang' => 6]); // Đã giao – chờ đại lý xác nhận
            }
        }
        return response()->json([
            'status' => true,
            'message' => 'Đã xác nhận đã đến'
        ]);
    }

    public function xacNhanDi(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user || !$user instanceof DonViVanChuyen) {
            return response()->json([
                'status' => false,
                'message' => 'Không xác thực'
            ], 401);
        }
        $lichTrinh = LichSuVanChuyen::find($request->id);
        if (!$lichTrinh) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy lịch trình'
            ], 404);
        }
        if (!$lichTrinh->thoi_gian_den) {
            return response()->json([
                'status' => false,
                'message' => 'Chưa xác nhận đến nên không thể rời đi'
            ], 400);
        }
        if ($lichTrinh->thoi_gian_di) {
            return response()->json([
                'status' => false,
                'message' => 'Đã xác nhận rời đi rồi'
            ], 400);
        }
        $lichTrinh->thoi_gian_di = Carbon::now('Asia/Ho_Chi_Minh');
        $lichTrinh->tinh_trang = 2; // Đã rời đi
        $lichTrinh->save();
        return response()->json([
            'status' => true,
            'message' => 'Đã xác nhận đã đi'
        ]);
    }

    // search cho đơn hàng bên nsx
    public function searchDonHangNSX(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;
        $key = "%" . $request->abc . "%";

        $list_don_hang = LichSuDonHang::where('lich_su_don_hangs.id_nha_san_xuat', $user_id)
        ->whereNotIn('lich_su_don_hangs.tinh_trang', [0])
        ->join('san_phams', 'san_phams.id', 'lich_su_don_hangs.id_san_pham')
        ->join('dai_lies', 'dai_lies.id', 'lich_su_don_hangs.user_id')
        ->join('don_vi_van_chuyens', 'don_vi_van_chuyens.id', 'lich_su_don_hangs.id_don_vi_van_chuyen')
        ->join('don_hangs', 'don_hangs.id', 'lich_su_don_hangs.id_don_hang')
        ->where('dai_lies.ten_cong_ty', 'like', $key)
        ->select(
            'lich_su_don_hangs.*',
            'san_phams.ten_san_pham',
            'san_phams.hinh_anh',
            'don_vi_van_chuyens.ten_cong_ty as ten_dvvc',
            'dai_lies.ten_cong_ty as ten_khach_hang',
            'don_hangs.ngay_dat',
            'dai_lies.id as user_id',
            'don_hangs.tinh_trang_thanh_toan',
            'don_hangs.tinh_trang as tinh_trang_don_hang',
            'lich_su_don_hangs.id as id_lich_su_don_hang'
        )
        ->get();
                // Gộp lại theo đơn hàng
        $grouped = $list_don_hang->groupBy('id_don_hang')->map(function ($items, $id) {
            $first = $items->first();

            // Tổng tiền sản phẩm = ∑ (giá × số lượng)
            $tong_tien_san_pham = $items->sum(function ($item) {
                return $item->don_gia * $item->so_luong;
            });

            //Gom theo id_nha_san_xuat để tránh trùng cước
            $cuoc_theo_nsx = $items->groupBy('id_nha_san_xuat')->map(function ($group) {
                return $group->first()->cuoc_van_chuyen; // Chỉ lấy 1 dòng đại diện
            });

            $tong_cuoc_van_chuyen = $cuoc_theo_nsx->sum(); // Tổng cước theo NSX

            return [
                'id_don_hang'            => $id,
                'user_id'                => $first->user_id,
                'ten_khach_hang'         => $first->ten_khach_hang,
                'ngay_dat'               => $first->ngay_dat,
                'tinh_trang_thanh_toan'  => $first->tinh_trang_thanh_toan,
                'tinh_trang_don_hang'    => $first->tinh_trang_don_hang,
                'tong_tien_san_pham'     => $tong_tien_san_pham,
                'tong_cuoc_van_chuyen'   => $tong_cuoc_van_chuyen,
                'tong_tien_don_hang'     => $tong_tien_san_pham + $tong_cuoc_van_chuyen,
                'san_phams'              => $items->map(function ($item) {
                    return [
                        'id_san_pham'       => $item->id_san_pham,
                        'ten_san_pham'      => $item->ten_san_pham,
                        'hinh_anh'          => $item->hinh_anh,
                        'so_luong'          => $item->so_luong,
                        'tinh_trang'        => $item->tinh_trang,
                        'cuoc_van_chuyen'   => $item->cuoc_van_chuyen,
                        'ten_dvvc'          => $item->ten_dvvc,
                        'id_lich_su_don_hang'    => $item->id_lich_su_don_hang,
                    ];
                })->values()
            ];
        })->values();

        return response()->json([
            'status' => true,
            'data'   => $grouped,
        ]);
    }

    // search cho đơn hàng bên Đvvc
    public function searchDonHangDVVC(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;
        $key = "%" . $request->abc . "%";

        $list_don_hang = LichSuDonHang::where('lich_su_don_hangs.id_don_vi_van_chuyen', $user_id)
                    ->whereNotIn('lich_su_don_hangs.tinh_trang', [0, 1])
                    ->join('san_phams', 'san_phams.id', 'lich_su_don_hangs.id_san_pham')
                    ->join('dai_lies', 'dai_lies.id', 'lich_su_don_hangs.user_id')
                    ->join('nha_san_xuats', 'nha_san_xuats.id', 'lich_su_don_hangs.id_nha_san_xuat')
                    ->join('don_hangs', 'don_hangs.id', 'lich_su_don_hangs.id_don_hang')
                    ->where('dai_lies.ten_cong_ty', 'like', $key)
                    ->select(
                        'lich_su_don_hangs.*',
                        'san_phams.ten_san_pham',
                        'san_phams.hinh_anh',
                        'nha_san_xuats.ten_cong_ty as ten_nsx',
                        'dai_lies.ten_cong_ty as ten_khach_hang',
                        'don_hangs.ngay_dat',
                        'dai_lies.id as user_id',
                        'don_hangs.tinh_trang_thanh_toan',
                        'don_hangs.tinh_trang as tinh_trang_don_hang',
                        'lich_su_don_hangs.id as id_lich_su_don_hang'
                    )
                    ->get();

            // Gộp lại theo đơn hàng
            $grouped = $list_don_hang->groupBy('id_don_hang')->map(function ($items, $id) {
                $first = $items->first();

            // Tổng tiền sản phẩm = ∑ (giá × số lượng)
            $tong_tien_san_pham = $items->sum(function ($item) {
                return $item->don_gia * $item->so_luong;
            });

            //Gom theo id_nha_san_xuat để tránh trùng cước
            $cuoc_theo_nsx = $items->groupBy('id_nha_san_xuat')->map(function ($group) {
                return $group->first()->cuoc_van_chuyen; // Chỉ lấy 1 dòng đại diện
            });

            $tong_cuoc_van_chuyen = $cuoc_theo_nsx->sum(); // Tổng cước theo NSX

            return [
                'id_don_hang'            => $id,
                'user_id'                => $first->user_id,
                'ten_khach_hang'         => $first->ten_khach_hang,
                'ngay_dat'               => $first->ngay_dat,
                'tinh_trang_thanh_toan'  => $first->tinh_trang_thanh_toan,
                'tinh_trang_don_hang'    => $first->tinh_trang_don_hang,
                'tong_tien_san_pham'     => $tong_tien_san_pham,
                'tong_cuoc_van_chuyen'   => $tong_cuoc_van_chuyen,
                'tong_tien_don_hang'     => $tong_tien_san_pham + $tong_cuoc_van_chuyen,
                'san_phams'              => $items->map(function ($item) {
                    return [
                        'id_san_pham'       => $item->id_san_pham,
                        'ten_san_pham'      => $item->ten_san_pham,
                        'hinh_anh'          => $item->hinh_anh,
                        'so_luong'          => $item->so_luong,
                        'tinh_trang'        => $item->tinh_trang,
                        'cuoc_van_chuyen'   => $item->cuoc_van_chuyen,
                        'ten_nsx'           => $item->ten_nsx,
                        'id_lich_su_don_hang'    => $item->id_lich_su_don_hang,
                    ];
                })->values()
            ];
        })->values();

        return response()->json([
            'status' => true,
            'data'   => $grouped,
        ]);
        }
}
