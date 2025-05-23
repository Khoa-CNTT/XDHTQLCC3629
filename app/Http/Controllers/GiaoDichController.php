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

        $urlMB = "https://script.googleusercontent.com/macros/echo?user_content_key=AehSKLhfOc3Ea5r9drFS3wYDhOwHYKGZEUjqzbOuKDP9a2sJCDyDgx3_bIGECDg9PTgj_KtEBK-Updlt_oQmgSt4QgCEmKW2HF3yjcOe3MpEqHt7UP1PV65a-Sd-6r_Qch87231b09XLHGJnrXAsdjb5ET4vIkPruqcWs-I3M9Fgu20wyGtDh-4PEaJC0Ffk1mpvrRCHL6dwAT7SE_T97L26p-M4zpUUAMZ6w1BEZb1xNzNlVwk0YsCwVFh_hqibYBEs7uN7coNVcD-rUYdCxnihkv80lkDopk0eG-S4k_1x&lib=MbkYxf-q3Y3DolfYq64ZautuNnwycQPwH";
        //vietinbank $url = "https://script.googleusercontent.com/macros/echo?user_content_key=AehSKLh0u-HD36Gpd6GWpnXi4WLw3Iy_wqJtS0lTDmC_ITnbbkPtl1m3O9Ulf835DYtLyACTqPOOH0sChPch0X-GRaEsHKBqqkMcS5yba_Gi2BZ8hSZx0h_elycaTONfteHBsVl2VxpV1bsSHI2hl5JM-Gj0Hw8zlx6Gyz557EKf37jdvgxHGOGYPHqX_H8YAaVlV8wVq5YcWnO4I2LOiWEP6_aLdiL5ZA3yQEzKZjobDvDpIn8hAJlBSgXgBkAplTwWR4GDE3eKEuQHEU90c0cHjduUussk1w&lib=MOUtsjnPOVSGPAxAONMEt_j_jpFl-Glvw";
        $danhSachDonHang = DonHang::where('tinh_trang_thanh_toan', 0)->get();
        try {
            $response = Http::get($urlMB);

            if ($response->successful()) {
                $data = $response->json();
                $matched = [];
                if (isset($data['data']) && count($data['data']) > 0) {
                    $giaoDich = $data['data'];
                    $nftResults = [];
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
                                    'so_tai_khoan_doi_ung'     =>  $gd["Số tài khoản đối ứng"],
                                ]);

                                $daiLy = DB::table('dai_lies')
                                    ->where('id', $dsDonHang->user_id)
                                    ->first();
                                $tenDaiLy = $daiLy->ten_cong_ty ?? 'Không xác định';

                                $metadata = [
                                    'name' => 'Bằng chứng đại lý đã thanh toán đơn hàng',
                                    'order_code' => $dsDonHang->ma_don_hang,
                                    'time_of_execution' => $gd["Ngày diễn ra"],
                                    'payer' => $tenDaiLy,
                                    'status' => 'Đã thanh toán',
                                    'description' => 'Thông tin thanh toán',
                                    'attributes' => [
                                        [
                                            'trait_type' => 'Mã giao dịch',
                                            'value' => $gd["Mã GD"],
                                        ],
                                        [
                                            'trait_type' => 'Nội dung chuyển tiền',
                                            'value' => $gd["Mô tả"],
                                        ],
                                        [
                                            'trait_type' => 'Số tiền (VNĐ)',
                                            'value' => $gd["Giá trị"],
                                        ],
                                        [
                                            'trait_type' => 'Số tài khoản',
                                            'value' => $gd["Mã GD"],
                                        ],
                                        [
                                            'trait_type' => 'Mã tham chiếu',
                                            'value' => $gd["Mã tham chiếu"],
                                        ],
                                    ]
                                ];

                                $pinataService = new PinataService();
                                $metadataUri = $pinataService->uploadMetadata($metadata);

                                $to_address = $request->dia_chi_vi;

                                $address = $request->input('wallet_address', $to_address);

                                $txHash = $pinataService->mintNFTtoApi($address, $metadataUri);
                                BlockChainForDonHang::create([
                                    'id_don_hang'               =>  $dsDonHang->id,
                                    'action'                    =>  'Kiểm tra thanh toán',
                                    'transaction_hash'          =>  $txHash['transactionHash'],
                                    'metadata_uri'              =>  $metadataUri,
                                    'token_id'                  =>  $txHash['tokenId'],
                                    'id_user'                   =>  $daiLy->id,
                                    'loai_tai_khoan'            =>  $daiLy->loai_tai_khoan,
                                ]);

                                $nftResults[] = [
                                    'transaction_hash' => $txHash['transactionHash'],
                                    'metadata_uri' => $metadataUri,
                                    'token_id' => $txHash['tokenId'],
                                    'ma_don_hang' => $dsDonHang->ma_don_hang,
                                    'dia_chi_vi' => $request->dia_chi_vi
                                ];


                                // cập nhật số dư tài khoản
                                $lichSuDonHangList = LichSuDonHang::where('id_don_hang', $dsDonHang->id)
                                    ->where('tinh_trang', '!=', 4) // chỉ lấy lịch sử đơn hàng chưa bị hủy
                                    ->get();

                                $soDuNhaSanXuat = [];
                                $daXuLyVanChuyen = [];
                                $tongTienAdmin = 0;

                                $donHang = DonHang::find($dsDonHang->id);
                                $admin = $donHang && $donHang->id_nguoi_duyet ? NhanVien::find($donHang->id_nguoi_duyet) : null;

                                foreach ($lichSuDonHangList as $lichSuDH) {
                                    $thanhTien = $lichSuDH->don_gia * $lichSuDH->so_luong;
                                    $cuocVanChuyen = $lichSuDH->cuoc_van_chuyen;

                                    // Cộng cho nhà sản xuất (vẫn cộng từng dòng)
                                    if (!isset($soDuNhaSanXuat[$lichSuDH->id_nha_san_xuat])) {
                                        $soDuNhaSanXuat[$lichSuDH->id_nha_san_xuat] = 0;
                                    }
                                    $soDuNhaSanXuat[$lichSuDH->id_nha_san_xuat] += $thanhTien * 0.95;

                                    // Mỗi id_nha_san_xuat chỉ xử lý cước vận chuyển 1 lần
                                    if (!in_array($lichSuDH->id_nha_san_xuat, $daXuLyVanChuyen)) {
                                        $donViVanChuyen = DonViVanChuyen::find($lichSuDH->id_don_vi_van_chuyen);
                                        if ($donViVanChuyen) {
                                            $donViVanChuyen->so_du_tai_khoan += $cuocVanChuyen * 0.95;
                                            $donViVanChuyen->save();
                                        }
                                        // Admin cũng chỉ cộng tương ứng 1 lần
                                        if ($admin) {
                                            $tongTienAdmin += $cuocVanChuyen * 0.05;
                                        }
                                        $daXuLyVanChuyen[] = $lichSuDH->id_nha_san_xuat;
                                    }
                                    // Admin nhận tiền từ sản phẩm (vẫn cộng từng dòng)
                                    if ($admin) {
                                        $tongTienAdmin += $thanhTien * 0.05;
                                    }
                                }

                                // Cập nhật số dư nhà sản xuất
                                foreach ($soDuNhaSanXuat as $id => $tien) {
                                    $nhaSanXuat = NhaSanXuat::find($id);
                                    if ($nhaSanXuat) {
                                        $nhaSanXuat->so_du_tai_khoan += $tien;
                                        $nhaSanXuat->save();
                                    }
                                }

                                // Cập nhật số dư admin
                                if ($admin && $tongTienAdmin > 0) {
                                    $admin->so_du_tai_khoan += $tongTienAdmin;
                                    $admin->save();
                                }


                                // cập nhật số dư tài khoản cho nhà sản xuất
                                // $lichSuDonHangList = LichSuDonHang::where('id_don_hang', $dsDonHang->id)
                                //     ->where('tinh_trang', '!=', 4) // chỉ lấy lịch sử đơn hàng chưa bị hủy
                                //     ->get();

                                // $donHang = DonHang::find($dsDonHang->id);
                                // $admin = $donHang && $donHang->id_nguoi_duyet ? NhanVien::find($donHang->id_nguoi_duyet) : null;

                                // foreach ($lichSuDonHangList as $lichSuDH) {
                                //     $nhaSanXuat = NhaSanXuat::find($lichSuDH->id_nha_san_xuat);
                                //     $thanhTien = $lichSuDH->don_gia * $lichSuDH->so_luong;

                                //     if ($nhaSanXuat) {
                                //         $nhaSanXuat->so_du_tai_khoan += $thanhTien * 0.95;
                                //         $nhaSanXuat->save();
                                //     }

                                //     if ($donHang && $donHang->id_nguoi_duyet) {
                                //         if ($admin) {
                                //             $admin->so_du_tai_khoan += $thanhTien * 0.05; // 5%
                                //             $admin->save();
                                //         }
                                //     }
                                // }

                                // // cập nhật số dư tài khoản đơn vị vậN chuyển
                                // $soDuVanChuyen = [];

                                // foreach ($lichSuDonHangList as $lichSuDH) {
                                //     $cuocVanChuyen = $lichSuDH->cuoc_van_chuyen;
                                //     if (!isset($soDuVanChuyen[$lichSuDH->id_don_vi_van_chuyen])) {
                                //         $soDuVanChuyen[$lichSuDH->id_don_vi_van_chuyen] = 0;
                                //     }
                                //     $soDuVanChuyen[$lichSuDH->id_don_vi_van_chuyen] += $cuocVanChuyen * 0.95;

                                //     if ($admin) {
                                //         $admin->so_du_tai_khoan += $cuocVanChuyen * 0.05;
                                //     }
                                // }

                                // foreach ($soDuVanChuyen as $id => $tien) {
                                //     $donViVanChuyen = DonViVanChuyen::find($id);
                                //     if ($donViVanChuyen) {
                                //         $donViVanChuyen->so_du_tai_khoan += $tien;
                                //         $donViVanChuyen->save();
                                //     }
                                // }


                                // foreach ($lichSuDonHangList as $lichSuDH) {
                                //     $cuocVanChuyen = $lichSuDH->cuoc_van_chuyen;
                                //     $donViVanChuyen = DonViVanChuyen::find($lichSuDH->id_don_vi_van_chuyen);

                                //     if ($donViVanChuyen) {
                                //         $donViVanChuyen->so_du_tai_khoan += $cuocVanChuyen * 0.95;
                                //         $donViVanChuyen->save();
                                //     }

                                //     if ($admin) {
                                //         $admin->so_du_tai_khoan += $cuocVanChuyen * 0.05;
                                //         $admin->save();
                                //     }
                                // }
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
                        'nft_results' => $nftResults,
                        'message_void' => 'Chưa có giao dịch mới được thanh toán.',
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
