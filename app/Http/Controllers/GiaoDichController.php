<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DonHang;
use App\Models\GiaoDich;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GiaoDichController extends Controller
{
    public function checkPaid()
    {
        $url = "https://script.googleusercontent.com/macros/echo?user_content_key=AehSKLhdaB5q-cMV_glq1bhF5RxaVDq-SWno4oRzkTvUOth51H16WtqweG8ghSNX0-UdktXSmADEpf5TO_zqYEhNDy-Vx4rHyIzoRVISEXkCQ94TvrdFoVzSZPFr9UmZrGtE1f3Xq0iYOTG9hVSfZrrHRIKHwrD7OOzec0Qcr5VuFR742I0EXQg5xHeVbVm1p2qNg-Wu_lEsq4AxsYzGF4Pau2aSzYhjXTwCPEOj0PTW9f_X9MsK4ZAXRmrbfGaGfApzr4C3NpEBWzFg9zkZ8E6MdEg9KC0MWxtp7L_8wMgX&lib=MbkYxf-q3Y3DolfYq64ZautuNnwycQPwH"; // URL gốc bạn dùng
        $danhSachDonHang = DonHang::where('tinh_trang_thanh_toan', 0)->get();
        try {
            $response = Http::get($url);

            if ($response->successful()) {
                $data = $response->json();
                $matched = [];
                if (isset($data['data']) && count($data['data']) > 0) {
                    $giaoDich = $data['data'];
                    foreach ($danhSachDonHang as $hoaDon) {
                        foreach ($giaoDich as $gd) {
                            $maDonHangKhongDau = str_replace('-', '', $hoaDon->ma_don_hang);
                            $soSanhSoTien = floatval($gd["Giá trị"]) >= floatval($hoaDon->tong_tien);
                            $soSanhMoTa = str_contains($gd["Mô tả"], $maDonHangKhongDau);
                            if ($soSanhSoTien && $soSanhMoTa) {
                                $hoaDon->tinh_trang_thanh_toan = 1;
                                $hoaDon->save();

                                GiaoDich::create([
                                    'ma_giao_dich'             =>  $gd["Mã GD"],
                                    'ma_don_hang'              =>  $hoaDon->ma_don_hang,
                                    'mo_ta'                    =>  $gd["Mô tả"],
                                    'gia_tri'                  =>  $gd["Giá trị"],
                                    'ngay_thuc_hien'           =>  $gd["Ngày diễn ra"],
                                    'so_tai_khoan'             =>  $gd["Số tài khoản"],
                                    'ma_tham_chieu'            =>  $gd["Mã tham chiếu"],
                                ]);

                                $matched[] = [
                                    'ma_don_hang' => $maDonHangKhongDau,
                                    'tong_tien' => $hoaDon->tong_tien,
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
                    return response()->json(['status' => 'false', 'message' => 'Không có dữ liệu.']);
                }
            } else {
                return response()->json(['status' => 'false', 'message' => 'Lỗi kết nối API.']);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'false', 'message' => 'Exception: ' . $e->getMessage()]);
        }
    }
}
