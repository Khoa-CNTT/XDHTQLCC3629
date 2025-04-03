<?php

namespace App\Http\Controllers;

use App\Models\DaiLy;
use App\Models\DonHang;
use App\Models\GioHang;
use App\Models\LichSuDonHang;
use App\Models\SanPham;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GioHangController extends Controller
{
    public function themVaoGioHang(Request $request)
    {
        // Kiểm tra đầu vào
        $request->validate([
            'id_san_pham' => 'required|exists:san_phams,id',
            'so_luong' => 'required|integer|min:1'
        ]);
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;
        // Kiểm tra sản phẩm có tồn tại không
        $sanPham = SanPham::find($request->id_san_pham);
        if (!$sanPham) {
            return response()->json(['status' => false, 'message' => 'Sản phẩm không tồn tại!'], 404);
        }
        // Kiểm tra sản phẩm đã có trong giỏ hàng chưa
        $gioHang = GioHang::where('user_id', $user_id)
            ->where('id_san_pham', $request->id_san_pham)
            ->first();
        // Lấy tổng số lượng sản phẩm đã có trong giỏ hàng
        $tongSoLuongTrongGio = GioHang::where('user_id', $user_id)
            ->where('id_san_pham', $request->id_san_pham)
            ->sum('so_luong');
        // Kiểm tra nếu tổng số lượng vượt quá số lượng tồn kho
        if (($tongSoLuongTrongGio + $request->so_luong) > $sanPham->so_luong_ton_kho) {
            return response()->json([
                'status' => false,
                'message' => 'Không thể thêm vào giỏ hàng! Số lượng sản phẩm vượt quá tồn kho.'
            ], 400);
        }
        if ($gioHang) {
            $gioHang->so_luong += $request->so_luong;
        } else {
            $gioHang = new GioHang();
            $gioHang->user_id = $user_id;
            $gioHang->id_san_pham = $request->id_san_pham;
            $gioHang->so_luong = $request->so_luong;
            $gioHang->don_gia = $request->don_gia;
        }
        $gioHang->save();
        return response()->json(['status' => true, 'message' => 'Đã thêm vào giỏ hàng!']);
    }

    public function getData(){
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json([
                'message' => 'Bạn cần đăng nhập!',
                'status'  => false,
            ], 401);
        } elseif($user && $user instanceof DaiLy) {
            $id_dai_ly = $user->id;
            $san_pham = GioHang::join('san_phams', 'san_phams.id','gio_hangs.id_san_pham')
            ->join('san_pham_n_s_x_e_s', 'san_phams.id','san_pham_n_s_x_e_s.id_san_pham')
            ->join('nha_san_xuats', 'nha_san_xuats.id', 'san_pham_n_s_x_e_s.id_nha_san_xuat')
            ->where('gio_hangs.user_id', $id_dai_ly)
            ->select('san_phams.id as id_san_pham',
                    'san_phams.ten_san_pham',
                    'nha_san_xuats.ten_cong_ty',
                    'san_phams.hinh_anh',
                    'gio_hangs.don_gia',
                    'gio_hangs.so_luong',
                    'san_phams.so_luong_ton_kho',
                    'gio_hangs.id',
                    'nha_san_xuats.id as id_nha_san_xuat') //get để nhóm ở groupby
            ->get();
            // $check = 2;
            return response()->json([
                'status'    =>      true,
                'data'      =>      $san_pham,
                // 'check'     =>      $check,
            ]);
        }
    }

    public function capNhatSoLuong(Request $request)
    {
        // Validate dữ liệu đầu vào
        $request->validate([
            'id' => 'required|exists:gio_hangs,id',
            'so_luong' => 'required|integer|min:1'
        ]);
        $cartItem = GioHang::find($request->id);
        if (!$cartItem) {
            return response()->json(['status' => false, 'message' => 'Sản phẩm không tồn tại trong giỏ hàng'], 404);
        }
        $sanPham = SanPham::find($cartItem->id_san_pham);
        if (!$sanPham) {
            return response()->json(['status' => false, 'message' => 'Sản phẩm không tồn tại'], 404);
        }
        if ($request->so_luong > $sanPham->so_luong_ton_kho) {
            return response()->json([
                'status' => false,
                'message' => 'Số lượng vượt quá số lượng tồn kho'
            ], 400);
        }
        $cartItem->so_luong = $request->so_luong;
        $cartItem->save();
        return response()->json([
            'status' => true,
            'message' => 'Cập nhật số lượng thành công',
            'data' => $cartItem
        ]);
    }

    public function xoaSanPham(Request $request)
    {
        // Tìm sản phẩm trong giỏ hàng
        $sanPham = GioHang::where('id', $request->id)->first();

        if (!$sanPham) {
            return response()->json([
                'status' => false,
                'message' => 'Sản phẩm không tồn tại trong giỏ hàng'
            ], 404);
        }

        $sanPham->delete();

        return response()->json([
            'status' => true,
            'message' => 'Xóa sản phẩm thành công'
        ]);
    }

    public function datHang(Request $request)
    {
        DB::beginTransaction();
        try {
            // Tạo đơn hàng mới
            $donHang = DonHang::create([
                'ma_don_hang'           => Str::uuid(),
                'user_id'               => $request->user_id,
                'id_nguoi_duyet'        => null,
                'id_van_chuyen'         => null,
                'ngay_dat'              => now(),
                'ngay_giao'             => now()->addDays(4),
                'tong_tien'             => 0,
                'tinh_trang'            => 0,
                'tinh_trang_thanh_toan' => 0
            ]);
            $tongTien = 0;
            // Duyệt qua danh sách sản phẩm đã chọn từ request
            foreach ($request->san_pham as $sp) {
                LichSuDonHang::create([
                    'user_id'           => $request->user_id,
                    'id_don_hang'       => $donHang->id,
                    'id_san_pham'       => $sp['id_san_pham'],
                    'id_nha_san_xuat'   => $sp['id_nha_san_xuat'],
                    'don_gia'           => $sp['don_gia'],
                    'so_luong'          => $sp['so_luong'],
                    'tinh_trang'        => 1
                ]);
                $tongTien += $sp['so_luong'] * $sp['don_gia'];
                // Trừ số lượng sản phẩm trong kho
                $sanPham = SanPham::find($sp['id_san_pham']);
                if ($sanPham) {
                    // Kiểm tra xem số lượng tồn kho có đủ không
                    if ($sanPham->so_luong_ton_kho >= $sp['so_luong']) {
                        $sanPham->so_luong_ton_kho -= $sp['so_luong'];
                        $sanPham->save();
                    } else {
                        // Nếu không đủ số lượng, trả lỗi
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => 'Sản phẩm ' . $sanPham->ten_san_pham . ' không đủ số lượng trong kho!'
                        ]);
                    }
                } else {
                    // Nếu không tìm thấy sản phẩm
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Không tìm thấy sản phẩm!'
                    ]);
                }
            }
            // Cập nhật tổng tiền cho đơn hàng
            $donHang->update(['tong_tien' => $tongTien]);
            // Xóa các sản phẩm đã được chọn trong giỏ hàng của người dùng
            foreach ($request->san_pham as $sp) {
                GioHang::where('user_id', $request->user_id)
                    ->where('id_san_pham', $sp['id_san_pham']) // Chỉ xóa sản phẩm đã được chọn
                    ->delete();
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Đặt hàng thành công!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Lỗi đặt hàng: ' . $e->getMessage()
            ]);
        }
    }
}
