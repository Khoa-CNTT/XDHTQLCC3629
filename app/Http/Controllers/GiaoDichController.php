<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DonHang;
use App\Models\DonViVanChuyen;
use App\Models\GiaoDich;
use App\Models\LichSuDonHang;
use App\Models\NhanVien;
use App\Models\NhaSanXuat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class GiaoDichController extends Controller
{
    public function checkPaid()
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

                                // cập nhật số dư tài khoản cho nhà sản xuất
                                $lichSuDonHangList = LichSuDonHang::where('id_don_hang', $dsDonHang->id)->get();

                                foreach ($lichSuDonHangList as $lichSuDH) {
                                    $nhaSanXuat = NhaSanXuat::find($lichSuDH->id_nha_san_xuat);

                                    if ($nhaSanXuat) {
                                        $nhaSanXuat->so_du_tai_khoan = $nhaSanXuat->so_du_tai_khoan + ($lichSuDH->don_gia * $lichSuDH->so_luong); // Cộng dồn đơn giá vào số dư
                                        $nhaSanXuat->save();
                                    }
                                }

                                // cập nhật số dư tài khoản đơn vị vậN chuyển
                                $donViVanChuyenGroup = $lichSuDonHangList->groupBy('id_don_vi_van_chuyen');

                                foreach ($donViVanChuyenGroup as $idDonViVanChuyen => $items) {
                                    $cuocVanChuyen = $items->first()->cuoc_van_chuyen;

                                    $donViVanChuyen = DonViVanChuyen::find($idDonViVanChuyen);

                                    if ($donViVanChuyen) {
                                        $donViVanChuyen->so_du_tai_khoan += $cuocVanChuyen;
                                        $donViVanChuyen->save();
                                    }
                                }

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
                        'message' => 'Đã cập nhật các hóa đơn khớp giao dịch.',
                        'matched_hoa_don' => $matched,
                        'total_updated' => count($matched)
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
            return response()->json(['status' => 'false', 'message' => 'Exception: ' . $e->getMessage()]);
        }
    }



    public function getDataChiTietHoaDonGiaoDichAdmin(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json([
                'message' => 'Bạn cần đăng nhập!',
                'status'  => false,
            ], 401);
        } elseif ($user && $user instanceof NhanVien) {
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
