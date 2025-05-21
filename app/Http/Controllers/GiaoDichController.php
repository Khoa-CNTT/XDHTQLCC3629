<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\BlockChainForDonHang;
use App\Models\DonHang;
use App\Models\DonViVanChuyen;
use App\Models\GiaoDich;
use App\Models\LichSuDonHang;
use App\Models\NhanVien;
use App\Models\NhaSanXuat;
use App\Services\PinataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class GiaoDichController extends Controller
{
    public function checkPaid(Request $request)
    {
        $url = "https://script.googleusercontent.com/macros/echo?user_content_key=AehSKLhdaB5q-cMV_glq1bhF5RxaVDq-SWno4oRzkTvUOth51H16WtqweG8ghSNX0-UdktXSmADEpf5TO_zqYEhNDy-Vx4rHyIzoRVISEXkCQ94TvrdFoVzSZPFr9UmZrGtE1f3Xq0iYOTG9hVSfZrrHRIKHwrD7OOzec0Qcr5VuFR742I0EXQg5xHeVbVm1p2qNg-Wu_lEsq4AxsYzGF4Pau2aSzYhjXTwCPEOj0PTW9f_X9MsK4ZAXRmrbfGaGfApzr4C3NpEBWzFg9zkZ8E6MdEg9KC0MWxtp7L_8wMgX&lib=MbkYxf-q3Y3DolfYq64ZautuNnwycQPwH";
        $danhSachDonHang = DonHang::where('tinh_trang_thanh_toan', 0)->get();
        try {
            $response = Http::get($url);

            if ($response->successful()) {
                $data = $response->json();
                $matched = [];
                if (isset($data['data']) && count($data['data']) > 0) {
                    $giaoDich = $data['data'];
                    foreach ($danhSachDonHang as $dsDonHang) {
                        foreach ($giaoDich as $gd) {
                            $moTaKhongDau = str_replace('-', '', $gd["Mô tả"]);
                            $maDonHangKhongDau = str_replace('-', '', $dsDonHang->ma_don_hang);
                            $soSanhSoTien = floatval($gd["Giá trị"]) >= floatval($dsDonHang->tong_tien);
                            $soSanhMoTa = str_contains($moTaKhongDau, $maDonHangKhongDau);
                            if ($soSanhSoTien && $soSanhMoTa) {
                                $dsDonHang->tinh_trang_thanh_toan = 1;
                                $dsDonHang->save();

                                GiaoDich::create([
                                    'ma_giao_dich'             =>  $gd["Mã GD"],
                                    'ma_don_hang'              =>  $dsDonHang->ma_don_hang,
                                    'mo_ta'                    =>  $gd["Mô tả"],
                                    'gia_tri'                  =>  $gd["Giá trị"],
                                    'ngay_thuc_hien'           =>  $gd["Ngày diễn ra"],
                                    'so_tai_khoan'             =>  $gd["Số tài khoản"],
                                    'ma_tham_chieu'            =>  $gd["Mã tham chiếu"],
                                ]);

                                // $daiLy = DB::table('dai_lies')
                                //                 ->where('id', $dsDonHang->user_id)
                                //                 ->first();
                                // $tenDaiLy = $daiLy->ten_cong_ty ?? 'Không xác định';

                                // $metadata = [
                                //     'name' => 'Bằng chứng đại lý đã thanh toán đơn hàng',
                                //     'order_code' => $dsDonHang->ma_don_hang,
                                //     'time_of_execution' => $gd["Ngày diễn ra"],
                                //     'payer' => $tenDaiLy,
                                //     'status' => 'Đã thanh toán',
                                //     'description' => 'Thông tin thanh toán',
                                //     'attributes' => [
                                //         [
                                //             'trait_type' => 'Mã giao dịch',
                                //             'value' => $gd["Mã GD"],
                                //         ],
                                //         [
                                //             'trait_type' => 'Nội dung chuyển tiền',
                                //             'value' => $gd["Mô tả"],
                                //         ],
                                //         [
                                //             'trait_type' => 'Số tiền (VNĐ)',
                                //             'value' => $gd["Giá trị"],
                                //         ],
                                //         [
                                //             'trait_type' => 'Số tài khoản',
                                //             'value' => $gd["Mã GD"],
                                //         ],
                                //         [
                                //             'trait_type' => 'Mã tham chiếu',
                                //             'value' => $gd["Mã tham chiếu"],
                                //         ],
                                //     ]
                                // ];

                                // $pinataService = new PinataService();
                                // $metadataUri = $pinataService->uploadMetadata($metadata);

                                // $to_address = $request->dia_chi_vi;

                                // $address = $request->input('wallet_address', $to_address);

                                // $txHash = $pinataService->mintNFTtoApi($address, $metadataUri); // truyền từ frontend
                                // BlockChainForDonHang::create([
                                //     'id_don_hang'               =>  $dsDonHang->id_don_hang,
                                //     'action'                    =>  'Kiểm tra thanh toán',
                                //     'transaction_hash'          =>  $txHash['transactionHash'],
                                //     'metadata_uri'              =>  $metadataUri,
                                //     'token_id'                  =>  $txHash['tokenId'],
                                //     'id_user'                   =>  $daiLy->id,
                                //     'loai_tai_khoan'            =>  $daiLy->loai_tai_khoan,
                                // ]);

                                // cập nhật số dư tài khoản cho nhà sản xuất
                                $lichSuDonHangList = LichSuDonHang::where('id_don_hang', $dsDonHang->id)
                                    ->get();
                                $donHang = DonHang::find($dsDonHang->id);
                                $admin = $donHang && $donHang->id_nguoi_duyet ? NhanVien::find($donHang->id_nguoi_duyet) : null;
                                foreach ($lichSuDonHangList as $lichSuDH) {
                                    $nhaSanXuat = NhaSanXuat::find($lichSuDH->id_nha_san_xuat);
                                    $thanhTien = $lichSuDH->don_gia * $lichSuDH->so_luong;

                                    if ($nhaSanXuat) {
                                        $nhaSanXuat->so_du_tai_khoan += $thanhTien * 0.95;
                                        $nhaSanXuat->save();
                                    }
                                    $donHang = DonHang::find($lichSuDH->id_don_hang);
                                    if ($donHang && $donHang->id_nguoi_duyet) {
                                        $admin = NhanVien::find($donHang->id_nguoi_duyet);
                                        if ($admin) {
                                            $admin->so_du_tai_khoan += $thanhTien * 0.05; // 5%
                                            $admin->save();
                                        }
                                    }
                                }

                                // cập nhật số dư tài khoản đơn vị vậN chuyển
                                $donViVanChuyenGroup = $lichSuDonHangList->groupBy('id_don_vi_van_chuyen');

                                foreach ($donViVanChuyenGroup as $idDonViVanChuyen => $items) {
                                    $cuocVanChuyen = $items->first()->cuoc_van_chuyen;

                                    $donViVanChuyen = DonViVanChuyen::find($idDonViVanChuyen);

                                    if ($donViVanChuyen) {
                                        $donViVanChuyen->so_du_tai_khoan += $cuocVanChuyen * 0.95;
                                        $donViVanChuyen->save();
                                    }
                                    if ($admin) {
                                        $admin->so_du_tai_khoan += $cuocVanChuyen * 0.05;
                                        $admin->save();
                                    }
                                }
                                //done chia tiền cho admin




                                $matched[] = [
                                    'ma_don_hang' => $maDonHangKhongDau,
                                    'tong_tien' => $dsDonHang->tong_tien,
                                    'mo_ta_giao_dich' => $gd["Mô tả"],
                                    'gia_tri_giao_dich' => $gd["Giá trị"],
                                ];
                                break;
                            }
                        }
                    }
                    return response()->json([
                        'status' => 'true',
                        'message' => 'Đã cập nhật các hóa đơn mới khớp giao dịch.',
                        'matched_hoa_don' => $matched,
                        'total_updated' => count($matched),
                        'message_void' => 'Chưa có giao dịch mới được thanh toán.',
                        // 'transaction_hash' => $txHash['transactionHash'],
                        // 'metadata_uri' => $metadataUri,
                        // 'token_id' => $txHash['tokenId']
                    ]);
                } else {
                    return response()->json([
                        'status' => 'false',
                        'message' => 'Không có dữ liệu.'
                    ]);
                }
            } else {
                return response()->json([
                    'status' => 'false',
                    'message' => 'Lỗi kết nối API.'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'false',
                'message' => 'Exception: ' . $e->getMessage()
            ]);
        }
    }



    public function getDataChiTietHoaDonGiaoDich(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json([
                'message' => 'Bạn cần đăng nhập!',
                'status'  => false,
            ], 401);
        } else {
            $list_chi_tiet_giao_dich = GiaoDich::where('giao_dichs.ma_don_hang', $request->ma_don_hang)
                ->select(
                    'giao_dichs.*',
                )
                ->get();
            return response()->json([
                'status'    =>      true,
                'data'      =>      $list_chi_tiet_giao_dich,
            ]);
        }
    }
}
