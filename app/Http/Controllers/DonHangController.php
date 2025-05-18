<?php

namespace App\Http\Controllers;

use App\Mail\SendMail;
use App\Models\BlockChainForDonHang;
use App\Models\DaiLy;
use App\Models\DonHang;
use App\Models\DonViVanChuyen;
use App\Models\KhoTrungChuyen;
use App\Models\LichSuDonHang;
use App\Models\LichSuVanChuyen;
use App\Models\NhanVien;
use App\Models\NhaSanXuat;
use App\Models\SanPham;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Services\PathFindingService;
use App\Services\PinataService;
use Illuminate\Support\Facades\Mail;

class DonHangController extends Controller
{
    //đại lý
    public function getData()
    {
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'message' => 'Bạn cần đăng nhập!',
                'status'  => false,
            ], 401);
        } elseif ($user && $user instanceof DaiLy) {
            $user_id = $user->id;

            // Lấy danh sách đơn hàng của đại lý
            $don_hangs = DonHang::where('don_hangs.user_id', $user_id)
                ->select(
                    'don_hangs.id',
                    'don_hangs.ma_don_hang',
                    'don_hangs.ngay_dat',
                    'don_hangs.ngay_giao',
                    'don_hangs.tinh_trang',
                    'don_hangs.tinh_trang_thanh_toan'
                )
                ->get();

            // Gắn thêm tổng tiền thực tế cho từng đơn
            $result = $don_hangs->map(function ($don_hang) {
                $items = LichSuDonHang::where('id_don_hang', $don_hang->id)
                    ->where('tinh_trang', '!=', 4) // Bỏ sản phẩm bị huỷ
                    ->get();

                $tong_tien_san_pham = $items->sum(function ($item) {
                    return $item->don_gia * $item->so_luong;
                });

                // Lấy duy nhất cuoc_van_chuyen theo mỗi id_nha_san_xuat
                $tong_cuoc_van_chuyen = $items
                    ->groupBy('id_nha_san_xuat')
                    ->sum(function ($group) {
                        return $group->first()->cuoc_van_chuyen ?? 0;
                    });

                return [
                    'id'                        => $don_hang->id,
                    'ma_don_hang'              => $don_hang->ma_don_hang,
                    'ngay_dat'                 => $don_hang->ngay_dat,
                    'ngay_giao'                => $don_hang->ngay_giao,
                    'tinh_trang'               => $don_hang->tinh_trang,
                    'tinh_trang_thanh_toan'    => $don_hang->tinh_trang_thanh_toan,
                    'tong_tien_san_pham'       => $tong_tien_san_pham,
                    'tong_cuoc_van_chuyen'     => $tong_cuoc_van_chuyen,
                    'tong_tien_can_thanh_toan' => $tong_tien_san_pham + $tong_cuoc_van_chuyen,
                ];
            });

            return response()->json([
                'status' => true,
                'data'   => $result,
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

    public function getDataChiTiet(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json([
                'message' => 'Bạn cần đăng nhập!',
                'status'  => false,
            ], 401);
        } elseif ($user && $user instanceof DaiLy) {
            $user_id = $user->id;
            $list_chi_tiet_don_hang = LichSuDonHang::where('lich_su_don_hangs.user_id', $user_id)
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

    public function huyDonHang(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json([
                'message' => 'Bạn cần đăng nhập!',
                'status'  => false,
            ], 401);
        } elseif ($user && $user instanceof DaiLy) {
            try {
                $id_nguoi_thuc_hien = $user->id;
                if ($request->input('v.tinh_trang') == 1 || $request->input('v.tinh_trang') == 0) {
                    $tinh_trang_moi = 4;
                }

                DonHang::where('id', $request->input('v.id'))->update([
                    'tinh_trang'    =>  $tinh_trang_moi,
                    'tinh_trang_thanh_toan'    =>  3,
                    'huy_bo_boi'    => 'dai_ly',
                ]);

                LichSuDonHang::where('id_don_hang', $request->input('v.id'))->update([
                    'tinh_trang'    =>  $tinh_trang_moi,
                    'huy_bo_boi'    => 'dai_ly',
                ]);

                $thoiGianCapNhat = Carbon::now("Asia/Ho_Chi_Minh");
                $metadata = [
                    'name' => 'Bằng chứng hủy đơn hàng của chủ đơn hàng',
                    'order_code' => $request->input('v.ma_don_hang'),
                    'time_of_execution' => $thoiGianCapNhat,
                    'user_execution' => $request->input('orderData.nguoi_thuc_hien'),
                    'status' => 'Đã hủy',
                    'description' => 'Đại lý hủy đơn hàng',
                    'attributes' => [
                        [
                            'trait_type' => 'Tổng tiền',
                            'value' => $request->input('v.tong_tien_can_thanh_toan')
                        ],
                        [
                            'trait_type' => 'Tình trạng thanh toán',
                            'value' => $request->input('v.tinh_trang_thanh_toan') == 1 ?
                                'Đã thanh toán' : 'Chưa thanh toán',
                        ],
                    ]
                ];

                $pinataService = new PinataService(); // Đảm bảo đã use đúng namespace
                $metadataUri = $pinataService->uploadMetadata($metadata);

                $to_address = $request->input('orderData.dia_chi_vi');

                $address = $request->input('wallet_address', $to_address);

                $txHash = $pinataService->mintNFTtoApi($address, $metadataUri); // truyền từ frontend

                BlockChainForDonHang::create([
                    'id_don_hang'               =>  $request->input('v.id'),
                    'action'                    =>  'Hủy đơn hàng',
                    'transaction_hash'          =>  $txHash['transactionHash'],
                    'metadata_uri'              =>  $metadataUri,
                    'token_id'                  =>  $txHash['tokenId'],
                    'id_user'                   =>  $id_nguoi_thuc_hien,
                    'loai_tai_khoan'            =>  $request->input('orderData.loai_tai_khoan')
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Xác nhận đơn hàng thành công!',
                    'transaction_hash' => $txHash['transactionHash'],
                    'metadata_uri' => $metadataUri,
                    'token_id' => $txHash['tokenId']
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Lỗi xác nhận đơn hàng: ' . $e->getMessage()
                ]);
            }
        }
    }

    public function xacNhanDonHangDaiLy(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json([
                'message' => 'Bạn cần đăng nhập!',
                'status'  => false,
            ], 401);
        } elseif ($user instanceof DaiLy) {
            try {
                DB::beginTransaction();
                $id_nguoi_thuc_hien = $user->id;
                if ($request->input('v.tinh_trang') != 6) {
                    return response()->json([
                        'message' => 'Trạng thái không hợp lệ để xác nhận!',
                        'status'  => false,
                    ]);
                }

                // Kiểm tra tất cả sản phẩm đã đạt trạng thái 6 chưa
                $countChuaHoanThanh = LichSuDonHang::where('id_don_hang', $request->input('v.id'))
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
                DonHang::where('id', $request->input('v.id'))->update([
                    'tinh_trang' => $tinh_trang_moi,
                ]);

                // Cập nhật toàn bộ chi tiết đơn hàng về tình trạng 3
                LichSuDonHang::where('id_don_hang', $request->input('v.id'))->update([
                    'tinh_trang' => $tinh_trang_moi,
                ]);

                // Cập nhật tình trạng chặng cuối của lịch sử vận chuyển
                $changCuois = LichSuVanChuyen::where('id_don_hang', $request->input('v.id'))
                    ->whereNull('id_kho_hang')
                    ->whereNull('thoi_gian_di')
                    ->get();

                foreach ($changCuois as $changCuoi) {
                    $changCuoi->update([
                        'tinh_trang'   => 2,
                        'thoi_gian_di' => Carbon::now('Asia/Ho_Chi_Minh'),
                    ]);
                }

                $thoiGianCapNhat = Carbon::now('Asia/Ho_Chi_Minh');
                $metadata = [
                    'name' => 'Bằng chứng xác nhận đã nhận được hàng của chủ đơn hàng',
                    'order_code' => $request->input('v.ma_don_hang'),
                    'time_of_execution' => $thoiGianCapNhat,
                    'user_execution' => $request->input('orderData.nguoi_thuc_hien'),
                    'status' => 'Đã hoàn thành',
                    'description' => 'Đại lý xác nhận đã nhận được hàng',
                    'attributes' => [
                        [
                            'trait_type' => 'Tổng tiền',
                            'value' => $request->input('v.tong_tien_can_thanh_toan')
                        ],
                        [
                            'trait_type' => 'Tình trạng thanh toán',
                            'value' => $request->input('v.tinh_trang_thanh_toan') == 1 ?
                                'Đã thanh toán' : 'Chưa thanh toán',
                        ],
                    ]
                ];

                $pinataService = new PinataService(); // Đảm bảo đã use đúng namespace
                $metadataUri = $pinataService->uploadMetadata($metadata);

                $to_address = $request->input('orderData.dia_chi_vi');

                $address = $request->input('wallet_address', $to_address);

                $txHash = $pinataService->mintNFTtoApi($address, $metadataUri); // truyền từ frontend

                BlockChainForDonHang::create([
                    'id_don_hang'               =>  $request->input('v.id'),
                    'action'                    =>  'Hoàn thành đơn hàng',
                    'transaction_hash'          =>  $txHash['transactionHash'],
                    'metadata_uri'              =>  $metadataUri,
                    'token_id'                  =>  $txHash['tokenId'],
                    'id_user'                   =>  $id_nguoi_thuc_hien,
                    'loai_tai_khoan'            =>  $request->input('orderData.loai_tai_khoan')
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Xác nhận đơn hàng thành công!',
                    'transaction_hash' => $txHash['transactionHash'],
                    'metadata_uri' => $metadataUri,
                    'token_id' => $txHash['tokenId']
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Lỗi xác nhận của đại lý: ' . $e->getMessage());
                return response()->json([
                    'success'  => false,
                    'message' => 'Lỗi: ' . $e->getMessage(),
                ]);
            }
        }
    }

    public function getDataOrderOnBlockChain(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json([
                'message' => 'Bạn cần đăng nhập!',
                'status'  => false,
            ], 401);
        }

        $list_info = BlockChainForDonHang::join('don_hangs', 'don_hangs.id', 'block_chain_for_don_hangs.id_don_hang')
            ->where('block_chain_for_don_hangs.id_don_hang', $request->id_don_hang)
            ->select(
                'block_chain_for_don_hangs.transaction_hash',
                'block_chain_for_don_hangs.metadata_uri',
                'block_chain_for_don_hangs.token_id',
                'don_hangs.ma_don_hang',
                'block_chain_for_don_hangs.action',
                'block_chain_for_don_hangs.loai_tai_khoan',
                'block_chain_for_don_hangs.id_user as id_nguoi_thuc_hien'
            )
            ->get();

        // Gắn thêm tên người thực hiện
        foreach ($list_info as $info) {
            if ($info->loai_tai_khoan === 'Đại Lý') {
                $ten = DB::table('dai_lies')->where('id', $info->id_nguoi_thuc_hien)->value('ten_cong_ty');
            } elseif ($info->loai_tai_khoan === 'Nhân Viên') {
                $ten = DB::table('nhan_viens')->where('id', $info->id_nguoi_thuc_hien)->value('ho_ten');
            } elseif ($info->loai_tai_khoan === 'Nhà Sản Xuất') {
                $ten = DB::table('nha_san_xuats')->where('id', $info->id_nguoi_thuc_hien)->value('ten_cong_ty');
            } elseif ($info->loai_tai_khoan === 'Đơn vị vận chuyển') {
                $ten = DB::table('don_vi_van_chuyens')->where('id', $info->id_nguoi_thuc_hien)->value('ten_cong_ty');
            } else {
                $ten = 'Không xác định';
            }
            $info->nguoi_thuc_hien = $ten;
        }

        return response()->json([
            'status' => true,
            'data' => $list_info,
        ]);
    }

    public function getDataHistoryTransport(Request $request)
    {
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'message' => 'Bạn cần đăng nhập!',
                'status'  => false,
            ], 401);
        } else {
            $user_id = $user->id;
            // Lấy toàn bộ bản ghi ứng với đơn hàng và đại lý
            $records = LichSuVanChuyen::join('don_hangs', 'lich_su_van_chuyens.id_don_hang', '=', 'don_hangs.id')
                ->where('lich_su_van_chuyens.id_dai_ly', $user_id)
                ->where('lich_su_van_chuyens.id_don_hang', $request->id_don_hang)
                ->whereNotNull('lich_su_van_chuyens.transaction_hash')
                ->whereNotNull('lich_su_van_chuyens.metadata_uri')
                ->whereNotNull('lich_su_van_chuyens.token_id')
                ->orderBy('lich_su_van_chuyens.tuyen_so')
                ->orderBy('lich_su_van_chuyens.id')
                ->select(
                    'lich_su_van_chuyens.transaction_hash',
                    'lich_su_van_chuyens.metadata_uri',
                    'lich_su_van_chuyens.token_id',
                    'lich_su_van_chuyens.tuyen_so',
                    'don_hangs.ma_don_hang'
                )
                ->get();

            // Group theo `tuyen_so` và lấy hàng đầu tiên mỗi nhóm
            $filtered = $records->groupBy('tuyen_so')->map(function ($group) {
                $first = $group->first();
                return [
                    'tuyen_so'        => $first->tuyen_so,
                    'transaction_hash' => $first->transaction_hash,
                    'metadata_uri'    => $first->metadata_uri,
                    'token_id'        => $first->token_id,
                    'ma_don_hang'     => $first->ma_don_hang
                ];
            })->values();

            return response()->json([
                'status' => true,
                'data'   => $filtered,
            ]);
        }
    }

    //admin get dữ liệu
    public function getDataForAdmin()
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json([
                'message' => 'Bạn cần đăng nhập!',
                'status'  => false,
            ], 401);
        } elseif ($user && $user instanceof NhanVien) {
            $list_don_hang = DonHang::join('dai_lies', 'dai_lies.id', 'don_hangs.user_id')
                ->select(
                    'don_hangs.ngay_dat',
                    'don_hangs.user_id',
                    'don_hangs.ngay_giao',
                    'don_hangs.tong_tien',
                    'don_hangs.tinh_trang',
                    'don_hangs.tinh_trang_thanh_toan',
                    'don_hangs.id',
                    'dai_lies.ten_cong_ty as ten_dai_ly',
                    'don_hangs.ma_don_hang'
                )
                ->get();

            return response()->json([
                'status'    => true,
                'data'      => $list_don_hang,
            ]);
        }
    }

    public function huyDonHangAdmin(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json([
                'message' => 'Bạn cần đăng nhập!',
                'status'  => false,
            ], 401);
        } elseif ($user && $user instanceof NhanVien) {
            try {
                $id_nguoi_thuc_hien = $user->id;
                if ($request->input('v.tinh_trang') == 1 || $request->input('v.tinh_trang') == 0) {
                    $tinh_trang_moi = 4;
                }
                DonHang::where('id', $request->input('v.id'))->update([
                    'tinh_trang'    =>  $tinh_trang_moi,
                    'tinh_trang_thanh_toan'    =>  3,
                    'huy_bo_boi'    => 'nhan_vien',
                ]);
                LichSuDonHang::where('id_don_hang', $request->input('v.id'))->update([
                    'tinh_trang'    =>  $tinh_trang_moi,
                    'huy_bo_boi'    => 'nhan_vien',
                ]);
                $thoiGianCapNhat = Carbon::now('Asia/Ho_Chi_Minh');
                $metadata = [
                    'name' => 'Bằng chứng nhân viên hủy đơn hàng',
                    'order_code' => $request->input('v.ma_don_hang'),
                    'time_of_execution' => $thoiGianCapNhat,
                    'status' => 'Đã hủy',
                    'description' => 'Nhân viên hủy đơn hàng',
                    'attributes' => [
                        [
                            'trait_type' => 'Người thực hiện',
                            'value' => $request->input('orderData.nguoi_thuc_hien')
                        ],
                        [
                            'trait_type' => 'Chức vụ',
                            'value' => $request->input('orderData.loai_tai_khoan')
                        ],
                        [
                            'trait_type' => 'Chủ đơn hàng',
                            'value' => $request->input('v.ten_dai_ly')
                        ],
                        [
                            'trait_type' => 'Tổng tiền',
                            'value' => $request->input('v.tong_tien')
                        ],
                        [
                            'trait_type' => 'Tình trạng thanh toán',
                            'value' => $request->input('v.tinh_trang_thanh_toan') == 1 ?
                                'Đã thanh toán' : 'Chưa thanh toán',
                        ],
                    ]
                ];

                $pinataService = new PinataService(); // Đảm bảo đã use đúng namespace
                $metadataUri = $pinataService->uploadMetadata($metadata);

                $to_address = $request->input('orderData.dia_chi_vi');

                $address = $request->input('wallet_address', $to_address);

                $txHash = $pinataService->mintNFTtoApi($address, $metadataUri); // truyền từ frontend

                BlockChainForDonHang::create([
                    'id_don_hang'               =>  $request->input('v.id'),
                    'action'                    =>  'Hủy đơn hàng',
                    'transaction_hash'          =>  $txHash['transactionHash'],
                    'metadata_uri'              =>  $metadataUri,
                    'token_id'                  =>  $txHash['tokenId'],
                    'id_user'                   =>  $id_nguoi_thuc_hien,
                    'loai_tai_khoan'            =>  $request->input('orderData.loai_tai_khoan')
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Hủy đơn hàng thành công!',
                    'transaction_hash' => $txHash['transactionHash'],
                    'metadata_uri' => $metadataUri,
                    'token_id' => $txHash['tokenId']
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Lỗi Hủy đơn hàng: ' . $e->getMessage()
                ]);
            }
        }
    }

    public function getDataChiTietAdmin(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json([
                'message' => 'Bạn cần đăng nhập!',
                'status'  => false,
            ], 401);
        } elseif ($user && $user instanceof NhanVien) {
            $list_chi_tiet_don_hang = LichSuDonHang::where('lich_su_don_hangs.id_don_hang', $request->id_don_hang)
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

    public function xacNhanDonHangAdmin(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json([
                'message' => 'Bạn cần đăng nhập!',
                'status'  => false,
            ], 401);
        } elseif ($user && $user instanceof NhanVien) {
            try {
                $id_nguoi_duyet = $user->id;
                if ($request->input('v.tinh_trang') == 0) {
                    $tinh_trang_moi = 1;
                }
                DonHang::where('id', $request->input('v.id'))->update([
                    'tinh_trang'        =>  $tinh_trang_moi,
                    'id_nguoi_duyet'    =>  $id_nguoi_duyet
                ]);
                LichSuDonHang::where('id_don_hang', $request->input('v.id'))->update([
                    'tinh_trang'        =>  $tinh_trang_moi,
                ]);
                $thoiGianCapNhat = Carbon::now('Asia/Ho_Chi_Minh');


                //gửi mail
                $idDonHang = $request->input('v.id');   // lấy id của đơn hàng đang chọn
                $donHang = DonHang::find($idDonHang);  // tìm đúng đơn hàng đó
                $daiLy = DaiLy::find($donHang->user_id);  // tìm qua bên đại lý


                $dataMail['ten_cong_ty'] = $daiLy->ten_cong_ty;
                $dataMail['id_don_hang'] = $donHang->id;
                $dataMail['tong_tien'] = $donHang->tong_tien;
                $link_qr  = "https://img.vietqr.io/image/MB-0328045024-compact2.jpg?amount=" . $donHang->tong_tien . "&addInfo=TTDP" . $donHang->ma_don_hang;
                $dataMail['ma_qr_code']     =  $link_qr;

                Mail::to($daiLy->email)->send(new SendMail('THANH TOÁN ĐƠN ĐẶT HÀNG', 'form_thanh_toan', $dataMail));
                //done gửi mail

                $metadata = [
                    'name' => 'Bằng chứng nhân viên xác nhận đơn hàng',
                    'order_code' => $request->input('v.ma_don_hang'),
                    'time_of_execution' => $thoiGianCapNhat,
                    'status' => 'Đã xác nhận',
                    'description' => 'Nhân viên xác nhận đơn hàng',
                    'attributes' => [
                        [
                            'trait_type' => 'Người thực hiện',
                            'value' => $request->input('orderData.nguoi_thuc_hien')
                        ],
                        [
                            'trait_type' => 'Chức vụ',
                            'value' => $request->input('orderData.loai_tai_khoan')
                        ],
                        [
                            'trait_type' => 'Chủ đơn hàng',
                            'value' => $request->input('v.ten_dai_ly')
                        ],
                        [
                            'trait_type' => 'Tổng tiền',
                            'value' => $request->input('v.tong_tien')
                        ],
                        [
                            'trait_type' => 'Tình trạng thanh toán',
                            'value' => $request->input('v.tinh_trang_thanh_toan') == 1 ?
                                'Đã thanh toán' : 'Chưa thanh toán',
                        ],
                    ]
                ];

                $pinataService = new PinataService(); // Đảm bảo đã use đúng namespace
                $metadataUri = $pinataService->uploadMetadata($metadata);

                $to_address = $request->input('orderData.dia_chi_vi');

                $address = $request->input('wallet_address', $to_address);

                $txHash = $pinataService->mintNFTtoApi($address, $metadataUri); // truyền từ frontend

                BlockChainForDonHang::create([
                    'id_don_hang'               =>  $request->input('v.id'),
                    'action'                    =>  'Xác nhận đơn hàng',
                    'transaction_hash'          =>  $txHash['transactionHash'],
                    'metadata_uri'              =>  $metadataUri,
                    'token_id'                  =>  $txHash['tokenId'],
                    'id_user'                   =>  $id_nguoi_duyet,
                    'loai_tai_khoan'            =>  $request->input('orderData.loai_tai_khoan')
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Xác nhận đơn hàng thành công!',
                    'transaction_hash' => $txHash['transactionHash'],
                    'metadata_uri' => $metadataUri,
                    'token_id' => $txHash['tokenId']
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Lỗi xác nhận đơn hàng: ' . $e->getMessage()
                ]);
            }
        }
    }

    // nhà sản xuất
    public function getDataChiTietForNSX(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json([
                'message' => 'Bạn cần đăng nhập!',
                'status'  => false,
            ], 401);
        } elseif ($user && $user instanceof NhaSanXuat) {
            $user_id = $user->id;
            $list_don_hang = LichSuDonHang::where('lich_su_don_hangs.id_nha_san_xuat', $user_id)
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

    public function getDataForNSX()
    {
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
                    ->join('don_hangs', function ($join) {
                        $join->on('don_hangs.id', '=', 'lich_su_don_hangs.id_don_hang')
                            ->where(function ($query) {
                                $query->whereNull('don_hangs.huy_bo_boi')
                                    ->orWhereNotIn('don_hangs.huy_bo_boi', ['dai_ly', 'nhan_vien']);
                            });
                    })
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
                        'don_hangs.ma_don_hang',
                        'don_hangs.ngay_giao',
                        'don_vi_van_chuyens.id as id_dvvc'
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
                        'tinh_trang_chi_tiet_don_hang' => $first->tinh_trang_chi_tiet_don_hang,
                        'ma_don_hang'            => $first->ma_don_hang,
                        'ngay_giao'              => $first->ngay_giao,
                        'id_dvvc'                => $first->id_dvvc,
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
                                'tinh_trang_chi_tiet_don_hang' => $item->tinh_trang_chi_tiet_don_hang
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

    public function xacNhanDonHangNSX(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user || !($user instanceof NhaSanXuat)) {
            return response()->json([
                'message' => 'Bạn cần đăng nhập với tư cách nhà sản xuất!',
                'status'  => false,
            ], 401);
        }

        try {
            DB::beginTransaction();

            if ($request->input('v.tinh_trang_don_hang') != 1) {
                return response()->json([
                    'message' => 'Trạng thái không hợp lệ để xác nhận!',
                    'status'  => false,
                ]);
            }

            $tinh_trang_moi_nsx = 2;

            // Cập nhật các sản phẩm của nhà sản xuất hiện tại
            foreach ($request->input('v.san_phams') as $sp) {
                if (isset($sp['id_lich_su_don_hang']) && $sp['id_nha_san_xuat'] == $user->id) {
                    LichSuDonHang::where('id', $sp['id_lich_su_don_hang'])->update([
                        'tinh_trang' => $tinh_trang_moi_nsx,
                    ]);
                }
            }

            // Kiểm tra toàn bộ sản phẩm trong đơn hàng đã xác nhận hết chưa
            $idDonHang = $request->input('v.id_don_hang');

            // Kiểm tra còn sản phẩm nào chưa xác nhận (khác trạng thái 2)
            $con_sp_chua_xac_nhan = LichSuDonHang::where('id_don_hang', $idDonHang)
                ->where('tinh_trang', '!=', $tinh_trang_moi_nsx)
                ->exists();

            // Kiểm tra còn sản phẩm nào đang ở trạng thái "đang chuẩn bị" (1)
            $con_sp_dang_chuan_bi = LichSuDonHang::where('id_don_hang', $idDonHang)
                ->where('tinh_trang', 1)
                ->exists();

            // Nếu không còn sản phẩm đang chuẩn bị hoặc chưa xác nhận → cập nhật trạng thái đơn hàng
            if (!$con_sp_chua_xac_nhan || !$con_sp_dang_chuan_bi) {
                DonHang::where('id', $idDonHang)->update([
                    'tinh_trang' => $tinh_trang_moi_nsx,
                ]);
            }

            $sanPhams = $request->input('v.san_phams');
            foreach ($sanPhams as &$sanPham) {
                $nsx = NhaSanXuat::find($sanPham['id_nha_san_xuat']);
                $sanPham['ten_san_pham']        = $sanPham['ten_san_pham'] ?? 'Không rõ';
                $sanPham['ten_nha_san_xuat']    = $nsx ? $nsx->ten_cong_ty : 'Không rõ';
                $sanPham['dia_chi']             = $nsx ? $nsx->dia_chi : 'Không rõ';
                $sanPham['email']               = $nsx ? $nsx->email : 'Không rõ';
                $sanPham['so_dien_thoai']       = $nsx ? $nsx->so_dien_thoai : 'Không rõ';

                unset(
                    $sanPham['id_lich_su_don_hang'],
                    $sanPham['id_nha_san_xuat'],
                    $sanPham['id_san_pham'],
                    $sanPham['tinh_trang_chi_tiet_don_hang'],
                    $sanPham['ten_dvvc'],
                );
            }

            $id_dvvc = $request->input('v.id_dvvc');
            $dvvc = DonViVanChuyen::find($id_dvvc);
            $thongTinDonViVanChuyen = [
                'ten_cong_ty'    => $dvvc->ten_cong_ty ?? 'Không rõ',
                'dia_chi'        => $dvvc->dia_chi ?? 'Không rõ',
                'email'          => $dvvc->email ?? 'Không rõ',
                'so_dien_thoai'  => $dvvc->so_dien_thoai ?? 'Không rõ',
            ];

            $thoiGianCapNhat = Carbon::now('Asia/Ho_Chi_Minh');
            $metadata = [
                'name' => 'Bằng chứng nhà sản xuất xác nhận đơn hàng',
                'order_code' => $request->input('v.ma_don_hang'),
                'time_of_execution' => $thoiGianCapNhat,
                'user_execution' => $request->input('orderData.nguoi_thuc_hien'),
                'status' => 'Đã xác nhận',
                'description' => 'Thông tin đơn hàng',
                'attributes' => [
                    [
                        'trait_type' => 'Tên khách hàng',
                        'value' => $request->input('v.ten_khach_hang'),
                    ],
                    [
                        'trait_type' => 'Tổng tiền sản phẩm',
                        'value' => $request->input('v.tong_tien_san_pham'),
                    ],
                    [
                        'trait_type' => 'Tổng cước vận chuyển',
                        'value' => $request->input('v.tong_cuoc_van_chuyen'),
                    ],
                    [
                        'trait_type' => 'Tổng tiền đơn hàng',
                        'value' => $request->input('v.tong_tien_don_hang'),
                    ],
                    [
                        'trait_type' => 'Ngày đặt hàng',
                        'value' => $request->input('v.ngay_dat'),
                    ],
                    [
                        'trait_type' => 'Ngày giao dự kiến',
                        'value' => $request->input('v.ngay_giao'),
                    ],
                    [
                        'trait_type' => 'Trạng thái thanh toán',
                        'value' => $request->input('v.tinh_trang_thanh_toan') == 1 ?
                            'Đã thanh toán' : 'Chưa thanh toán',
                    ],
                    [
                        'trait_type' => 'Sản phẩm',
                        'value' => $sanPhams,
                    ],
                    [
                        'trait_type' => 'Đơn vị vận chuyển',
                        'value' => $thongTinDonViVanChuyen,
                    ],
                ]
            ];

            $pinataService = new PinataService(); // Đảm bảo đã use đúng namespace
            $metadataUri = $pinataService->uploadMetadata($metadata);

            $to_address = $request->input('orderData.dia_chi_vi');

            $address = $request->input('wallet_address', $to_address);

            $txHash = $pinataService->mintNFTtoApi($address, $metadataUri); // truyền từ frontend

            BlockChainForDonHang::create([
                'id_don_hang'               =>  $request->input('v.id_don_hang'),
                'action'                    =>  'Xác nhận đơn hàng',
                'transaction_hash'          =>  $txHash['transactionHash'],
                'metadata_uri'              =>  $metadataUri,
                'token_id'                  =>  $txHash['tokenId'],
                'id_user'                   =>  $request->input('v.id_nha_san_xuat'),
                'loai_tai_khoan'            =>  $request->input('orderData.loai_tai_khoan')
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Xác nhận đơn hàng thành công!',
                'transaction_hash' => $txHash['transactionHash'],
                'metadata_uri' => $metadataUri,
                'token_id' => $txHash['tokenId']
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xác nhận đơn hàng: ' . $e->getMessage()
            ]);
        }
    }

    public function getDataOrderOnBlockChainForNSX(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json([
                'message' => 'Bạn cần đăng nhập!',
                'status'  => false,
            ], 401);
        }

        $list_info = BlockChainForDonHang::join('don_hangs', 'don_hangs.id', 'block_chain_for_don_hangs.id_don_hang')
            ->where('block_chain_for_don_hangs.id_don_hang', $request->id_don_hang)
            ->where(function ($query) use ($request) {
                $query->where(function ($q) {
                    // Các bản ghi KHÔNG PHẢI nhà sản xuất => cho xem
                    $q->where('block_chain_for_don_hangs.loai_tai_khoan', '!=', 'Nhà Sản Xuất');
                })->orWhere(function ($q) use ($request) {
                    // Các bản ghi là NSX và chính người dùng đăng nhập tạo ra => cho xem
                    $q->where('block_chain_for_don_hangs.loai_tai_khoan', $request->loai_tai_khoan)
                        ->where('block_chain_for_don_hangs.id_user', $request->id_nsx);
                });
            })
            ->select(
                'block_chain_for_don_hangs.transaction_hash',
                'block_chain_for_don_hangs.metadata_uri',
                'block_chain_for_don_hangs.token_id',
                'don_hangs.ma_don_hang',
                'block_chain_for_don_hangs.action',
                'block_chain_for_don_hangs.loai_tai_khoan',
                'block_chain_for_don_hangs.id_user as id_nguoi_thuc_hien'
            )
            ->get();

        // Gắn thêm tên người thực hiện
        foreach ($list_info as $info) {
            if ($info->loai_tai_khoan === 'Đại Lý') {
                $ten = DB::table('dai_lies')->where('id', $info->id_nguoi_thuc_hien)->value('ten_cong_ty');
            } elseif ($info->loai_tai_khoan === 'Nhân Viên') {
                $ten = DB::table('nhan_viens')->where('id', $info->id_nguoi_thuc_hien)->value('ho_ten');
            } elseif ($info->loai_tai_khoan === 'Nhà Sản Xuất') {
                $ten = DB::table('nha_san_xuats')->where('id', $info->id_nguoi_thuc_hien)->value('ten_cong_ty');
            } elseif ($info->loai_tai_khoan === 'Đơn vị vận chuyển') {
                $ten = DB::table('don_vi_van_chuyens')->where('id', $info->id_nguoi_thuc_hien)->value('ten_cong_ty');
            } else {
                $ten = 'Không xác định';
            }
            $info->nguoi_thuc_hien = $ten;
        }

        return response()->json([
            'status' => true,
            'data' => $list_info,
        ]);
    }

    public function getDataHistoryTransportForNSX(Request $request)
    {
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'message' => 'Bạn cần đăng nhập!',
                'status'  => false,
            ], 401);
        } else {
            $user_id = $user->id;
            // Lấy toàn bộ bản ghi ứng với đơn hàng và đại lý
            $records = LichSuVanChuyen::join('don_hangs', 'lich_su_van_chuyens.id_don_hang', '=', 'don_hangs.id')
                ->where('lich_su_van_chuyens.id_dai_ly', $user_id)
                ->where('lich_su_van_chuyens.id_don_hang', $request->id_don_hang)
                ->orderBy('lich_su_van_chuyens.tuyen_so')
                ->orderBy('lich_su_van_chuyens.id')
                ->select(
                    'lich_su_van_chuyens.transaction_hash',
                    'lich_su_van_chuyens.metadata_uri',
                    'lich_su_van_chuyens.token_id',
                    'lich_su_van_chuyens.tuyen_so',
                    'don_hangs.ma_don_hang'
                )
                ->get();

            // Group theo `tuyen_so` và lấy hàng đầu tiên mỗi nhóm
            $filtered = $records->groupBy('tuyen_so')->map(function ($group) {
                $first = $group->first();
                return [
                    'tuyen_so'        => $first->tuyen_so,
                    'transaction_hash' => $first->transaction_hash,
                    'metadata_uri'    => $first->metadata_uri,
                    'token_id'        => $first->token_id,
                    'ma_don_hang'     => $first->ma_don_hang
                ];
            })->values();

            return response()->json([
                'status' => true,
                'data'   => $filtered,
            ]);
        }
    }

    public function huyDonHangNSX(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user || !($user instanceof NhaSanXuat)) {
            return response()->json([
                'message' => 'Bạn cần đăng nhập với tư cách nhà sản xuất!',
                'status'  => false,
            ], 401);
        }

        try {
            DB::beginTransaction();

            if ($request->input('v.tinh_trang_don_hang') != 1) {
                return response()->json([
                    'message' => 'Trạng thái không hợp lệ để xác nhận!',
                    'status'  => false,
                ]);
            }

            $tinh_trang_moi_nsx = 4;

            // Cập nhật các sản phẩm của nhà sản xuất hiện tại
            foreach ($request->input('v.san_phams') as $sp) {
                if (isset($sp['id_lich_su_don_hang']) && $sp['id_nha_san_xuat'] == $user->id) {
                    LichSuDonHang::where('id', $sp['id_lich_su_don_hang'])->update([
                        'tinh_trang' => $tinh_trang_moi_nsx,
                    ]);
                }
            }

            $idDonHang = $request->input('v.id_don_hang');

            $count_sp_tong = LichSuDonHang::where('id_don_hang', $idDonHang)->count();
            $count_sp_huy = LichSuDonHang::where('id_don_hang', $idDonHang)
                ->where('tinh_trang', 4)
                ->count();

            $count_sp_xac_nhan = LichSuDonHang::where('id_don_hang', $idDonHang)
                ->where('tinh_trang', 2)
                ->count();

            $count_sp_dang_chuan_bi = LichSuDonHang::where('id_don_hang', $idDonHang)
                ->where('tinh_trang', 1)
                ->count();

            // Nếu toàn bộ sản phẩm đều đã hủy
            if ($count_sp_huy === $count_sp_tong) {
                DonHang::where('id', $idDonHang)->update([
                    'tinh_trang' => 4, // đơn hàng bị hủy hoàn toàn
                    'huy_bo_boi' => 'nha_san_xuat',
                ]);
            }
            // Nếu có sản phẩm được xác nhận, và không còn sản phẩm đang chuẩn bị
            else if ($count_sp_xac_nhan > 0 && $count_sp_dang_chuan_bi == 0) {
                DonHang::where('id', $idDonHang)->update([
                    'tinh_trang' => 2, // đã chuẩn bị xong
                ]);
            }

            $sanPhams = $request->input('v.san_phams');
            foreach ($sanPhams as &$sanPham) {
                $nsx = NhaSanXuat::find($sanPham['id_nha_san_xuat']);
                $sanPham['ten_san_pham']        = $sanPham['ten_san_pham'] ?? 'Không rõ';
                $sanPham['ten_nha_san_xuat']    = $nsx ? $nsx->ten_cong_ty : 'Không rõ';
                $sanPham['dia_chi_nsx']         = $nsx ? $nsx->dia_chi : 'Không rõ';
                $sanPham['email_nsx']           = $nsx ? $nsx->email : 'Không rõ';
                $sanPham['so_dien_thoai_nsx']   = $nsx ? $nsx->so_dien_thoai : 'Không rõ';

                unset(
                    $sanPham['id_lich_su_don_hang'],
                    $sanPham['id_nha_san_xuat'],
                    $sanPham['id_san_pham'],
                    $sanPham['tinh_trang_chi_tiet_don_hang'],
                    $sanPham['ten_dvvc'],
                );
            }

            $id_dvvc = $request->input('v.id_dvvc');
            $dvvc = DonViVanChuyen::find($id_dvvc);
            $thongTinDonViVanChuyen = [
                'ten_cong_ty'    => $dvvc->ten_cong_ty ?? 'Không rõ',
                'dia_chi'        => $dvvc->dia_chi ?? 'Không rõ',
                'email'          => $dvvc->email ?? 'Không rõ',
                'so_dien_thoai'  => $dvvc->so_dien_thoai ?? 'Không rõ',
            ];

            $thoiGianCapNhat = Carbon::now('Asia/Ho_Chi_Minh');
            $metadata = [
                'name' => 'Bằng chứng nhà sản xuất hủy đơn hàng',
                'order_code' => $request->input('v.ma_don_hang'),
                'time_of_execution' => $thoiGianCapNhat,
                'user_execution' => $request->input('orderData.nguoi_thuc_hien'),
                'status' => 'Đã hủy',
                'description' => 'Thông tin đơn hàng',
                'attributes' => [
                    [
                        'trait_type' => 'Tên khách hàng',
                        'value' => $request->input('v.ten_khach_hang'),
                    ],
                    [
                        'trait_type' => 'Tổng tiền sản phẩm',
                        'value' => $request->input('v.tong_tien_san_pham'),
                    ],
                    [
                        'trait_type' => 'Tổng cước vận chuyển',
                        'value' => $request->input('v.tong_cuoc_van_chuyen'),
                    ],
                    [
                        'trait_type' => 'Tổng tiền đơn hàng',
                        'value' => $request->input('v.tong_tien_don_hang'),
                    ],
                    [
                        'trait_type' => 'Ngày đặt hàng',
                        'value' => $request->input('v.ngay_dat'),
                    ],
                    [
                        'trait_type' => 'Ngày giao dự kiến',
                        'value' => $request->input('v.ngay_giao'),
                    ],
                    [
                        'trait_type' => 'Trạng thái thanh toán',
                        'value' => $request->input('v.tinh_trang_thanh_toan') == 1 ?
                            'Đã thanh toán' : 'Chưa thanh toán',
                    ],
                    [
                        'trait_type' => 'Sản phẩm',
                        'value' => $sanPhams,
                    ],
                    [
                        'trait_type' => 'Đơn vị vận chuyển',
                        'value' => $thongTinDonViVanChuyen,
                    ],
                ]
            ];

            $pinataService = new PinataService(); // Đảm bảo đã use đúng namespace
            $metadataUri = $pinataService->uploadMetadata($metadata);

            $to_address = $request->input('orderData.dia_chi_vi');

            $address = $request->input('wallet_address', $to_address);

            $txHash = $pinataService->mintNFTtoApi($address, $metadataUri); // truyền từ frontend

            BlockChainForDonHang::create([
                'id_don_hang'               =>  $request->input('v.id_don_hang'),
                'action'                    =>  'Hủy đơn hàng',
                'transaction_hash'          =>  $txHash['transactionHash'],
                'metadata_uri'              =>  $metadataUri,
                'token_id'                  =>  $txHash['tokenId'],
                'id_user'                   =>  $request->input('v.id_nha_san_xuat'),
                'loai_tai_khoan'            =>  $request->input('orderData.loai_tai_khoan')
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Xác nhận đơn hàng thành công!',
                'transaction_hash' => $txHash['transactionHash'],
                'metadata_uri' => $metadataUri,
                'token_id' => $txHash['tokenId']
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xác nhận đơn hàng: ' . $e->getMessage()
            ]);
        }
    }

    //đơn vị vận chuyển
    public function getDataForDVVC()
    {
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
                    ->whereNotIn('lich_su_don_hangs.tinh_trang', [0, 1, 4])
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
                        'don_hangs.ma_don_hang',
                        'don_hangs.id as id_don_hang',
                        'don_hangs.ngay_giao',
                        'dai_lies.so_dien_thoai as so_dien_thoai_dai_ly',
                        'lich_su_don_hangs.tinh_trang as tinh_trang_lich_su_don_hang'
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
                        'ma_don_hang'            => $first->ma_don_hang,
                        'id_don_hang'            => $first->id_don_hang,
                        'ngay_giao'              => $first->ngay_giao,
                        'so_dien_thoai_dai_ly'   => $first->so_dien_thoai_dai_ly,
                        'tinh_trang_lich_su_don_hang' => $first->tinh_trang_lich_su_don_hang,
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
            // Kiểm tra trạng thái đơn hàng
            $data_res = $request->input('v.id_don_hang');
            $lichSuTaoMoi = [];
            $donHang = DonHang::findOrFail($data_res['id_don_hang']);
            if ($donHang->tinh_trang != 2) {
                return response()->json([
                    'message' => 'Đơn hàng chưa sẵn sàng để xác nhận vận chuyển!',
                    'status'  => false,
                ]);
            }

            // Kiểm tra còn sản phẩm nào chưa được NSX chuẩn bị xong hay không
            $sanPhamsChuaXong = LichSuDonHang::where('id_don_hang', $data_res["id_don_hang"])
                ->where('id_don_vi_van_chuyen', $data_res["id_dvvc"]) // chỉ sản phẩm của đơn vị vận chuyển hiện tại
                ->whereIn('id_nha_san_xuat', $data_res["id_cac_nsx"]) // chỉ các NSX mà ĐVVC phụ trách
                ->whereIn('tinh_trang', [0, 1]) // chưa xác nhận hoặc đang chuẩn bị
                ->count();

            if ($sanPhamsChuaXong > 0) {
                return response()->json([
                    'message' => 'Không thể xác nhận vận chuyển vì còn sản phẩm chưa chuẩn bị xong!',
                    'status'  => false,
                ]);
            }

            // Cập nhật trạng thái đơn hàng và sản phẩm
            $tinhTrangMoi = 5;
            $idDonHang = $request->input('v.id_don_hang.id_don_hang');
            $idDvvc = $request->input('v.id_don_hang.id_dvvc');

            // Bước 1: Cập nhật trạng thái sản phẩm do đơn vị vận chuyển này phụ trách
            LichSuDonHang::where('id_don_hang', $idDonHang)
                ->where('id_don_vi_van_chuyen', $idDvvc)
                ->update([
                    'tinh_trang' => $tinhTrangMoi,
                ]);

            // Bước 2: Kiểm tra còn sản phẩm nào của đơn hàng chưa được xác nhận (khác trạng thái 5)
            $con_sp_chua_duoc_dvvc_khac_xac_nhan = LichSuDonHang::where('id_don_hang', $idDonHang)
                ->where('tinh_trang', '!=', $tinhTrangMoi)
                ->exists();

            // Bước 3: Nếu không còn sản phẩm nào chưa được xác nhận → cập nhật đơn hàng
            if (!$con_sp_chua_duoc_dvvc_khac_xac_nhan) {
                DonHang::where('id', $idDonHang)->update([
                    'tinh_trang' => $tinhTrangMoi,
                ]);
            }
            // Lấy danh sách NSX và đại lý
            $danhSachNhaSanXuat = $data_res['id_cac_nsx'];
            $idDaiLy = $request->input('v.id_dai_ly');
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
                $lichSuTaoMoi[] = LichSuVanChuyen::create([
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

                        $lichSuTaoMoi[] = LichSuVanChuyen::create([
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
                $lichSuTaoMoi[] = LichSuVanChuyen::create([
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

            $dsNSX = NhaSanXuat::whereIn('id', collect($lichSuTaoMoi)
                ->pluck('id_nha_san_xuat'))
                ->select('id', 'ten_cong_ty', 'dia_chi', 'email')
                ->get()
                ->keyBy('id');
            $dsKho = KhoTrungChuyen::whereIn('id', collect($lichSuTaoMoi)
                ->pluck('id_kho_hang'))
                ->pluck('dia_chi', 'id');

            // Nhóm các chặng theo tuyến
            $nhomTuyen = collect($lichSuTaoMoi)->groupBy('tuyen_so');

            $tuyenVanChuyen = [];

            foreach ($nhomTuyen as $tuyenSo => $cacChang) {
                $changDauTien = $cacChang->first();
                $nsx = $dsNSX[$changDauTien->id_nha_san_xuat] ?? null;

                $chiTietChang = [];
                foreach ($cacChang as $chang) {
                    $diaChiKho = $dsKho[$chang->id_kho_hang] ?? 'Không có địa chỉ kho';

                    $chiTietChang[] = [
                        'vi_tri_can_den' => $chang->mo_ta,
                        'dia_chi'    => $diaChiKho,
                    ];
                }

                $tuyenVanChuyen[] = [
                    'tuyen_so'       => $tuyenSo,
                    'ten_nsx'        => $nsx->ten_cong_ty ?? 'Không rõ',
                    'dia_chi_nsx'    => $nsx->dia_chi ?? 'Không rõ',
                    'cac_chang'      => $chiTietChang,
                ];
            }

            $thoiGianCapNhat = Carbon::now('Asia/Ho_Chi_Minh');
            $sanPhams = $request->input('v.id_don_hang.san_phams');
            foreach ($sanPhams as &$sanPham) {
                $sp = SanPham::find($sanPham['id_san_pham']);
                $sanPham['ten_nsx'];
                $sanPham['ten_san_pham']    = $sp ? $sp->ten_san_pham : 'Không rõ';
                $sanPham['hinh_anh']        = $sp ? $sp->hinh_anh : 'Không rõ';
                $sanPham['mo_ta']           = $sp ? $sp->mo_ta : 'Không rõ';
                $sanPham['don_gia']         = $sp ? $sp->gia_ban : 'Không rõ';
                $sanPham['don_vi_tinh']     = $sp ? $sp->don_vi_tinh : 'Không rõ';

                unset(
                    $sanPham['cuoc_van_chuyen'],
                    $sanPham['hinh_anh'],
                    $sanPham['id_lich_su_don_hang'],
                    $sanPham['id_san_pham'],
                    $sanPham['so_luong'],
                    $sanPham['ten_sp'],
                    $sanPham['tinh_trang'],
                );
            }

            // 🔐 Mint dữ liệu lên blockchain
            $metadata = [
                'name' => 'Bằng chứng đơn vị vận chuyển xác nhận đơn hàng',
                'order_code' => $request->input('v.id_don_hang.ma_don_hang'),
                'time_of_execution' => $thoiGianCapNhat,
                'user_execution' => $request->input('orderData.nguoi_thuc_hien'),
                'status' => 'Đã xác nhận vận chuyển',
                'description' => 'Thông tin đơn hàng',
                'attributes' => [
                    [
                        'trait_type' => 'Người nhận',
                        'value' => $request->input('v.id_don_hang.ten_khach_hang')
                    ],
                    [
                        'trait_type' => 'Địa chỉ',
                        'value' => $request->input('v.id_don_hang.dia_chi_dai_ly')
                    ],
                    [
                        'trait_type' => 'Số điện thoại',
                        'value' => $request->input('v.id_don_hang.so_dien_thoai_dai_ly')
                    ],
                    [
                        'trait_type' => 'Ngày đặt',
                        'value' => $request->input('v.id_don_hang.ngay_dat')
                    ],
                    [
                        'trait_type' => 'Ngày giao (dự kiến)',
                        'value' => $request->input('v.id_don_hang.ngay_giao')
                    ],
                    [
                        'trait_type' => 'Tổng tiền',
                        'value' => $request->input('v.id_don_hang.tong_tien_don_hang')
                    ],
                    [
                        'trait_type' => 'Tổng cước vận chuyển',
                        'value' => $request->input('v.id_don_hang.tong_cuoc_van_chuyen')
                    ],
                    [
                        'trait_type' => 'Sản phẩm',
                        'value' => $sanPhams
                    ],
                    [
                        'trait_type' => 'Lộ trình vận chuyển',
                        'value' => $tuyenVanChuyen
                    ],
                ]
            ];

            $pinataService = new PinataService(); // Đảm bảo đã use đúng namespace
            $metadataUri = $pinataService->uploadMetadata($metadata);

            $to_address = $request->input('orderData.dia_chi_vi');

            $address = $request->input('wallet_address', $to_address);

            $txHash = $pinataService->mintNFTtoApi($address, $metadataUri); // truyền từ frontend

            BlockChainForDonHang::create([
                'id_don_hang'               =>  $request->input('v.id_don_hang.id_don_hang'),
                'action'                    =>  'Xác nhận đơn hàng',
                'transaction_hash'          =>  $txHash['transactionHash'],
                'metadata_uri'              =>  $metadataUri,
                'token_id'                  =>  $txHash['tokenId'],
                'id_user'                   =>  $request->input('v.id_don_hang.id_dvvc'),
                'loai_tai_khoan'            =>  $request->input('orderData.loai_tai_khoan')
            ]);
            return response()->json([
                'status'  => true,
                'message' => 'Xác nhận vận chuyển thành công!',
                'du_lieu_lo_trinh' => $lichSuTaoMoi, // Trả về dữ liệu lịch sử vận chuyển
                'transaction_hash' => $txHash['transactionHash'],
                'metadata_uri' => $metadataUri,
                'token_id' => $txHash['tokenId']
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

    public function getDataChiTietForDVVC(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json([
                'message' => 'Bạn cần đăng nhập!',
                'status'  => false,
            ], 401);
        }
        if ($user instanceof DonViVanChuyen) {
            $user_id = $user->id;
            $tinh_trang_huy = 4;
            try {
                $list_chi_tiet_don_hang = LichSuDonHang::where('lich_su_don_hangs.id_don_vi_van_chuyen', $user_id)
                    ->where('lich_su_don_hangs.tinh_trang', '!=', $tinh_trang_huy)
                    ->where('lich_su_don_hangs.id_don_hang', $request->id_don_hang)
                    ->join('san_phams', 'lich_su_don_hangs.id_san_pham', '=', 'san_phams.id')
                    ->join('nha_san_xuats', 'lich_su_don_hangs.id_nha_san_xuat', '=', 'nha_san_xuats.id')
                    ->join('don_vi_van_chuyens', 'lich_su_don_hangs.id_don_vi_van_chuyen', '=', 'don_vi_van_chuyens.id')
                    // ->join('don_hangs', 'don_hangs.id', '=', 'lich_su_don_hangs.id_don_hang')
                    // ->join('dai_lies', 'dai_lies.id', '=', 'lich_su_don_hangs.user_id')
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
                        // 'lich_su_don_hangs.user_id as id_dai_ly',
                        // 'dai_lies.dia_chi as dia_chi_dai_ly',
                        // 'don_hangs.ma_don_hang',
                        // 'don_hangs.id as id_don_hang',
                        // 'don_hangs.ngay_giao',
                        // 'dai_lies.so_dien_thoai as so_dien_thoai_dai_ly'
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
            ->select(
                'don_hangs.ngay_dat',
                'don_hangs.user_id',
                'don_hangs.ngay_giao',
                'don_hangs.tong_tien',
                'don_hangs.tinh_trang',
                'don_hangs.tinh_trang_thanh_toan',
                'don_hangs.id',
                'don_hangs.ma_don_hang',
                'dai_lies.ten_cong_ty as ten_dai_ly'
            )
            ->get();
        return response()->json([
            'status'    =>      true,
            'data'      =>      $data,
        ]);
    }

    public function getLichTrinh(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json([
                'message' => 'Bạn cần đăng nhập!',
                'status'  => false,
            ], 401);
        }
        if ($user instanceof DonViVanChuyen) {
            $user_id = $user->id;
            try {
                $list_lich_trinh = LichSuVanChuyen::where('lich_su_van_chuyens.id_don_vi_van_chuyen', $user_id)
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
                        'lich_su_van_chuyens.transaction_hash',
                        'lich_su_van_chuyens.metadata_uri',
                        'lich_su_van_chuyens.token_id',
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

        // Cập nhật thời gian đến
        $lichTrinh->thoi_gian_den = Carbon::now('Asia/Ho_Chi_Minh');
        $lichTrinh->tinh_trang = 1; // Đã đến
        $lichTrinh->save();

        // Kiểm tra nếu là điểm cuối (không có kho và mô tả chứa "Vị trí đại lý")
        $isDiemCuoi = !$lichTrinh->id_kho_hang && Str::contains($lichTrinh->mo_ta, 'Vị trí đại lý');

        if ($isDiemCuoi) {
            // Lấy tất cả các chặng "điểm cuối" của đơn hàng
            $diemCuoiList = LichSuVanChuyen::where('id_don_hang', $lichTrinh->id_don_hang)
                ->whereNull('id_kho_hang')
                ->where('mo_ta', 'like', '%Vị trí đại lý%')
                ->get();

            // Kiểm tra xem tất cả điểm cuối đã đến chưa
            $tatCaDiemCuoiDaDen = $diemCuoiList->every(function ($item) {
                return !empty($item->thoi_gian_den);
            });

            // Nếu tất cả điểm cuối đã đến
            if ($tatCaDiemCuoiDaDen) {
                // Cập nhật trạng thái từng chi tiết đơn hàng liên quan
                LichSuDonHang::where('id_don_hang', $lichTrinh->id_don_hang)
                    ->where('tinh_trang', '<>', 6)
                    ->update(['tinh_trang' => 6]);

                // Kiểm tra xem tất cả chi tiết đơn hàng đã giao xong chưa
                $tatCaChiTietDaGiao = LichSuDonHang::where('id_don_hang', $lichTrinh->id_don_hang)
                    ->where('tinh_trang', '<>', 6)
                    ->count() === 0;

                // Nếu tất cả đã giao thì cập nhật đơn hàng
                if ($tatCaChiTietDaGiao) {
                    $donHang = DonHang::find($lichTrinh->id_don_hang);
                    if ($donHang) {
                        $donHang->tinh_trang = 6; // Đã giao – chờ đại lý xác nhận
                        $donHang->save();
                    }
                }
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
                'lich_su_don_hangs.id as id_lich_su_don_hang',
                'don_hangs.ma_don_hang',
                'don_hangs.id as id_don_hang',
                'don_hangs.ngay_giao',
                'dai_lies.so_dien_thoai as so_dien_thoai_dai_ly'
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
                        'ma_don_hang'            => $item->ma_don_hang,
                        'id_don_hang'            => $item->id_don_hang,
                        'ngay_giao'              => $item->ngay_giao,
                        'so_dien_thoai_dai_ly'   => $item->so_dien_thoai_dai_ly,
                    ];
                })->values()
            ];
        })->values();

        return response()->json([
            'status' => true,
            'data'   => $grouped,
        ]);
    }

    public function getDataOrderOnBlockChainForDVVC(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json([
                'message' => 'Bạn cần đăng nhập!',
                'status'  => false,
            ], 401);
        }

        $list_info = BlockChainForDonHang::join('don_hangs', 'don_hangs.id', 'block_chain_for_don_hangs.id_don_hang')
            ->where('block_chain_for_don_hangs.id_don_hang', $request->id_don_hang)
            ->where('block_chain_for_don_hangs.action', '!=', 'Hủy đơn hàng')
            ->select(
                'block_chain_for_don_hangs.transaction_hash',
                'block_chain_for_don_hangs.metadata_uri',
                'block_chain_for_don_hangs.token_id',
                'don_hangs.ma_don_hang',
                'block_chain_for_don_hangs.action',
                'block_chain_for_don_hangs.loai_tai_khoan',
                'block_chain_for_don_hangs.id_user as id_nguoi_thuc_hien'
            )
            ->get();

        // Gắn thêm tên người thực hiện
        foreach ($list_info as $info) {
            if ($info->loai_tai_khoan === 'Đại Lý') {
                $ten = DB::table('dai_lies')->where('id', $info->id_nguoi_thuc_hien)->value('ten_cong_ty');
            } elseif ($info->loai_tai_khoan === 'Nhân Viên') {
                $ten = DB::table('nhan_viens')->where('id', $info->id_nguoi_thuc_hien)->value('ho_ten');
            } elseif ($info->loai_tai_khoan === 'Nhà Sản Xuất') {
                $ten = DB::table('nha_san_xuats')->where('id', $info->id_nguoi_thuc_hien)->value('ten_cong_ty');
            } elseif ($info->loai_tai_khoan === 'Đơn vị vận chuyển') {
                $ten = DB::table('don_vi_van_chuyens')->where('id', $info->id_nguoi_thuc_hien)->value('ten_cong_ty');
            } else {
                $ten = 'Không xác định';
            }
            $info->nguoi_thuc_hien = $ten;
        }

        return response()->json([
            'status' => true,
            'data' => $list_info,
        ]);
    }
}
