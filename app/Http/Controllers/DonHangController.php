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
        if (!$user) {
            return response()->json([
                'message' => 'Bạn cần đăng nhập!',
                'status'  => false,
            ], 401);
        } elseif($user && $user instanceof NhaSanXuat) {
            try {
                DB::beginTransaction();

                if ($request->tinh_trang_don_hang != 1) {
                    return response()->json([
                        'message' => 'Trạng thái không hợp lệ để xác nhận!',
                        'status'  => false,
                    ]);
                }
                $tinh_trang_moi_nsx = 2;
                // Cập nhật đơn hàng và toàn bộ sản phẩm
                DonHang::where('id', $request->id_don_hang)->update([
                    'tinh_trang' => $tinh_trang_moi_nsx,
                ]);
                foreach ($request->san_phams as $sp) {
                    if (isset($sp['id_lich_su_don_hang'])) {
                        LichSuDonHang::where('id', $sp['id_lich_su_don_hang'])->update([
                            'tinh_trang' => $tinh_trang_moi_nsx,
                        ]);
                    }
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
                        'nha_san_xuats.id as id_nsx'
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
                        'id_nsx'                => $first->id_nsx,
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

            if ($request->tinh_trang_don_hang != 2) {
                return response()->json([
                    'message' => 'Trạng thái không hợp lệ để xác nhận!',
                    'status'  => false,
                ]);
            }

            $tinh_trang_moi_dvvc = 5;

            // Cập nhật đơn hàng và toàn bộ sản phẩm
            DonHang::where('id', $request->id_don_hang)->update([
                'tinh_trang' => $tinh_trang_moi_dvvc,
            ]);

            foreach ($request->san_phams as $sp) {
                if (isset($sp['id_lich_su_don_hang'])) {
                    LichSuDonHang::where('id', $sp['id_lich_su_don_hang'])->update([
                        'tinh_trang' => $tinh_trang_moi_dvvc,
                    ]);
                }
            }

            $chiTietDonHang = LichSuDonHang::where('id_don_hang', $request->id_don_hang)
                            ->where('id_don_vi_van_chuyen', $user->id)
                            ->get();

            if ($chiTietDonHang->isEmpty()) {
                return response()->json([
                    'message' => 'Không tìm thấy sản phẩm nào cần vận chuyển cho đơn vị hiện tại!',
                    'status' => false,
                ]);
            }

            $nhomTheoNSX = $chiTietDonHang->groupBy('id_nha_san_xuat');

            foreach ($nhomTheoNSX as $nsxId => $sanPhamTheoNSX) {
                $nsx = NhaSanXuat::find($nsxId);

                if (!$nsx) {
                    throw new \Exception("Không tìm thấy nhà sản xuất ID: $nsxId");
                }

                $tinhShop = $this->timTinhTuDiaChi($nsx->dia_chi, $this->dsTinhThanhPho());

                // Tìm kho khớp tỉnh
                $tatCaKho = KhoTrungChuyen::all();
                $khoPhuHop = null;
                foreach ($tatCaKho as $khoItem) {
                    $tinhTrongKho = $this->timTinhTuDiaChi($khoItem->tinh_thanh, $this->dsTinhThanhPho());
                    if ($tinhTrongKho && strtolower($tinhTrongKho) == strtolower($tinhShop)) {
                        $khoPhuHop = $khoItem;
                        break;
                    }
                }
                if (!$khoPhuHop) {
                    $khoPhuHop = KhoTrungChuyen::inRandomOrder()->first();
                    Log::warning("Không tìm được kho đúng tỉnh [$tinhShop], dùng random: ID {$khoPhuHop->id}");
                }

                LichSuVanChuyen::create([
                    'id_don_hang'          => $request->id_don_hang,
                    'id_kho_hang'          => $khoPhuHop->id,
                    'id_don_vi_van_chuyen' => $user->id,
                    'id_nha_san_xuat'      => $nsxId,
                    'thoi_gian_den'        => Carbon::now(),
                    'thoi_gian_di'         => null,
                    'thu_tu'               => 1,
                    'mo_ta'                => 'Lấy hàng từ kho trung chuyển ' . $khoPhuHop->ten_kho,
                    'tinh_trang'           => 0,
                ]);
            }

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Xác nhận thành công!',
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

    private function timTinhTuDiaChi($diaChi, $dsTinh)
    {
        $alias = [
            'TP.HCM' => 'Hồ Chí Minh',
            'TP HCM' => 'Hồ Chí Minh',
            'Tp.HCM' => 'Hồ Chí Minh',
            'TP Hà Nội' => 'Hà Nội',
            'Tp.HN' => 'Hà Nội',
            'TP. HCM' => 'Hồ Chí Minh',
        ];
        $diaChiLower = Str::lower($diaChi);
        foreach ($alias as $vietTat => $dayDu) {
            if (Str::contains($diaChiLower, Str::lower($vietTat))) {
                return $dayDu;
            }
        }
        foreach ($dsTinh as $tinh) {
            if (Str::contains($diaChiLower, Str::lower($tinh))) {
                return $tinh;
            }
        }
        return null;
    }

    private function dsTinhThanhPho()
    {
        return [
            "Hà Nội", "Hồ Chí Minh", "TP.HCM", "Đà Nẵng", "Cần Thơ",
            "Hải Phòng", "Bắc Ninh", "Thanh Hóa", "Nghệ An", "Hà Tĩnh",
            "Bình Dương", "Đồng Nai", "Long An", "Bình Thuận", "Quảng Ninh",
            "Quảng Nam", "Thừa Thiên Huế", "Bình Định", "Khánh Hòa",
            "Vĩnh Long", "Ninh Bình", "Phú Thọ", "Lâm Đồng", "Tiền Giang",
            "Hậu Giang", "Sóc Trăng", "Trà Vinh", "Bến Tre", "An Giang",
            "Kiên Giang", "Tây Ninh", "Bạc Liêu", "Cà Mau", "Đắk Lắk", "Đắk Nông",
            "Gia Lai", "Kon Tum", "Ninh Thuận", "Phú Yên", "Quảng Bình",
            "Quảng Trị", "Lạng Sơn", "Lào Cai", "Yên Bái", "Hòa Bình", "Tuyên Quang"
        ];
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
    // Trả về tuyến đường ngắn nhất từ nhà sản xuất đến đại lý
    public function goiYDuongDi(Request $request)
    {
        $request->validate([
            'id_nha_san_xuat' => 'required|exists:nha_san_xuats,id',
            'id_dai_ly' => 'required|exists:dai_lies,id',
        ]);
        $result = $this->pathFindingService->findShortestPath(
            $request->id_nha_san_xuat,
            $request->id_dai_ly
        );
        return response()->json([
            'success' => true,
            'tuyen_duong_id' => $result['path_ids'],
            'tuyen_duong_ten' => $result['path_names'],
            'tong_khoang_cach' => $result['distance'] . ' km',
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
