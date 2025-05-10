<?php

namespace App\Http\Controllers;

use App\Models\DaiLy;
use App\Models\DonHang;
use App\Models\DonViVanChuyen;
use App\Models\GioHang;
use App\Models\LichSuDonHang;
use App\Models\NhaSanXuat;
use App\Models\SanPham;
use App\Services\PinataService;
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

    public function mintNFTtoApi($address, $metadataUri)
    {
        $client = new \GuzzleHttp\Client();
        $res    = $client->post("http://localhost:3000/api/mint-nft", [
            'json' => [
                'recipient' => $address,
                'tokenURI'  => $metadataUri
            ]
        ]);

        $data = json_decode($res->getBody(), true);
        return $data;
    }

    public function datHang(Request $request)
    {
        DB::beginTransaction();
        try {
            $ma_don_hang = Str::uuid();
            $ngay_dat = now();
            $ngay_giao = now()->addDays(4);
            // Tạo đơn hàng mới
            $donHang = DonHang::create([
                'ma_don_hang'           => $ma_don_hang,
                'user_id'               => $request->user_id,
                'id_nguoi_duyet'        => null,
                'ngay_dat'              => $ngay_dat,
                'ngay_giao'             => $ngay_giao,
                'tong_tien'             => $request->tong_tien,
                'cuoc_van_chuyen'       => $request->cuoc_van_chuyen ?? 0,
                'tinh_trang'            => 0,
                'tinh_trang_thanh_toan' => 0
            ]);

            $cuocVCMap = collect($request->chi_tiet_cuoc_vc)->keyBy('id_nha_san_xuat')->toArray();
            // Duyệt qua danh sách sản phẩm đã chọn từ request
            foreach ($request->san_pham as $sp) {
                // Lấy cuốc vận chuyển tương ứng theo index
                $idNSX = $sp['id_nha_san_xuat'];
                $cuocVC = $cuocVCMap[$idNSX]['cuoc_van_chuyen'] ?? 0;

                LichSuDonHang::create([
                    'user_id'           => $request->user_id,
                    'id_don_hang'       => $donHang->id,
                    'id_san_pham'       => $sp['id_san_pham'],
                    'id_nha_san_xuat'   => $sp['id_nha_san_xuat'],
                    'don_gia'           => $sp['don_gia'],
                    'so_luong'          => $sp['so_luong'],
                    'tinh_trang'        => 0,
                    'cuoc_van_chuyen'   => $cuocVC
                ]);

                // Kiểm tra và trừ số lượng sản phẩm trong kho
                $sanPham = SanPham::find($sp['id_san_pham']);
                if ($sanPham) {
                    if ($sanPham->so_luong_ton_kho >= $sp['so_luong']) {
                        $sanPham->so_luong_ton_kho -= $sp['so_luong'];
                        $sanPham->save();
                    } else {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => 'Sản phẩm ' . $sanPham->ten_san_pham . ' không đủ số lượng trong kho!'
                        ]);
                    }
                } else {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Không tìm thấy sản phẩm!'
                    ]);
                }
            }
            // Kiểm tra và cập nhật thông tin đơn vị vận chuyển cho từng sản phẩm
            if (!empty($request->don_vi_van_chuyen) && is_array($request->don_vi_van_chuyen)) {
                foreach ($request->don_vi_van_chuyen as $dvvc) {
                    // Tìm tất cả sản phẩm trong lichSuDonHang tương ứng với nhà sản xuất
                    $lichSuList = LichSuDonHang::where('id_don_hang', $donHang->id)
                        ->where('id_nha_san_xuat', $dvvc['id_nha_san_xuat'])
                        ->get(); // Sử dụng get() để lấy tất cả các sản phẩm có cùng nhà sản xuất
                    if ($lichSuList->isEmpty()) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => 'Không tìm thấy sản phẩm của nhà sản xuất có id: ' . $dvvc['id_nha_san_xuat']
                        ]);
                    }
                    // Cập nhật đơn vị vận chuyển cho tất cả sản phẩm của nhà sản xuất này
                    foreach ($lichSuList as $lichSu) {
                        $lichSu->id_don_vi_van_chuyen = $dvvc['id_don_vi_van_chuyen'];
                        $lichSu->save();
                    }
                }
            } else {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Không có thông tin đơn vị vận chuyển!'
                ]);
            }
            // Xóa các sản phẩm đã được chọn trong giỏ hàng của người dùng
            foreach ($request->san_pham as $sp) {
                GioHang::where('user_id', $request->user_id)
                    ->where('id_san_pham', $sp['id_san_pham'])
                    ->delete();
            }

            // Sao chép dữ liệu từ request ra biến riêng để xử lý
            $sanPhams = $request->san_pham;
            foreach ($sanPhams as &$sanPham) {
                $nsx = NhaSanXuat::find($sanPham['id_nha_san_xuat']);
                $sanPham['ten_nha_san_xuat']    = $nsx ? $nsx->ten_cong_ty : 'Không rõ';
                $sanPham['dia_chi']             = $nsx ? $nsx->dia_chi : 'Không rõ';
                $sanPham['email']               = $nsx ? $nsx->email : 'Không rõ';
                $sanPham['so_dien_thoai']       = $nsx ? $nsx->so_dien_thoai : 'Không rõ';

                $sp = SanPham::find($sanPham['id_san_pham']);
                $sanPham['ten_san_pham']    = $sp ? $sp->ten_san_pham : 'Không rõ';
                $sanPham['hinh_anh']        = $sp ? $sp->hinh_anh : 'Không rõ';
                $sanPham['mo_ta']           = $sp ? $sp->mo_ta : 'Không rõ';
                $sanPham['don_gia']         = $sp ? $sp->gia_ban : 'Không rõ';
                $sanPham['don_vi_tinh']     = $sp ? $sp->don_vi_tinh : 'Không rõ';

                unset($sanPham['id_nha_san_xuat'], $sanPham['id_san_pham']);
            }

            $donViVanChuyens = $request->don_vi_van_chuyen;
            foreach ($donViVanChuyens as &$don_vi_van_chuyen) {
                $nsx = NhaSanXuat::find($don_vi_van_chuyen['id_nha_san_xuat']);
                $don_vi_van_chuyen['ten_nha_san_xuat'] = $nsx ? $nsx->ten_cong_ty : 'Không rõ';
                $don_vi_van_chuyen['dia_chi_nsx'] = $nsx ? $nsx->dia_chi : 'Không rõ';
                $don_vi_van_chuyen['email_nsx'] = $nsx ? $nsx->email : 'Không rõ';
                $don_vi_van_chuyen['so_dien_thoai_nsx'] = $nsx ? $nsx->so_dien_thoai : 'Không rõ';

                $dvvchuyen = DonViVanChuyen::find($don_vi_van_chuyen['id_don_vi_van_chuyen']);
                $don_vi_van_chuyen['ten_dvvc'] = $dvvchuyen ? $dvvchuyen->ten_cong_ty : 'Không rõ';
                $don_vi_van_chuyen['dia_chi_dvvc'] = $dvvchuyen ? $dvvchuyen->dia_chi : 'Không rõ';
                $don_vi_van_chuyen['email_dvvc'] = $dvvchuyen ? $dvvchuyen->email : 'Không rõ';
                $don_vi_van_chuyen['so_dien_thoai_dvvc']= $dvvchuyen ? $dvvchuyen->so_dien_thoai : 'Không rõ';
                $don_vi_van_chuyen['cuoc_van_chuyen'] = $dvvchuyen ? $dvvchuyen->cuoc_van_chuyen : 'Không rõ';

                unset($don_vi_van_chuyen['id_nha_san_xuat'], $don_vi_van_chuyen['id_don_vi_van_chuyen']);
            }

            $cuocVCthanhphans = $request->chi_tiet_cuoc_vc;
            foreach ($cuocVCthanhphans as &$chi_tiet_cuoc_vc) {
                $dvvchuyen = DonViVanChuyen::find($chi_tiet_cuoc_vc['id_don_vi_van_chuyen']);
                $chi_tiet_cuoc_vc['ten_dvvc'] = $dvvchuyen ? $dvvchuyen->ten_cong_ty : 'Không rõ';
                $chi_tiet_cuoc_vc['cuoc_van_chuyen'] = $dvvchuyen ? $dvvchuyen->cuoc_van_chuyen : 'Không rõ';

                $nsx = NhaSanXuat::find($chi_tiet_cuoc_vc['id_nha_san_xuat']);
                $chi_tiet_cuoc_vc['ten_nha_san_xuat'] = $nsx ? $nsx->ten_cong_ty : 'Không rõ';

                unset($chi_tiet_cuoc_vc['id_nha_san_xuat'], $chi_tiet_cuoc_vc['id_don_vi_van_chuyen']);
            }

            // 🔐 Mint dữ liệu lên blockchain
            $metadata = [
                'name' => 'Đơn hàng #' . $ma_don_hang,
                'description' => 'Thông tin đơn hàng',
                'attributes' => [
                    ['trait_type' => 'Người nhận', 'value' => $request->ten_nguoi_nhan],
                    ['trait_type' => 'Ngày đặt', 'value' => $ngay_dat],
                    ['trait_type' => 'Ngày giao (dự kiến)', 'value' => $ngay_giao],
                    ['trait_type' => 'Số điện thoại', 'value' => $request->so_dien_thoai],
                    ['trait_type' => 'Tổng tiền', 'value' => $request->tong_tien],
                    ['trait_type' => 'Tổng cước vận chuyển', 'value' => $request->cuoc_van_chuyen],
                    [
                        'trait_type' => 'Cước vận chuyển thành phần',
                        'value' => $cuocVCthanhphans
                    ],
                    ['trait_type' => 'Mã đơn hàng', 'value' => $ma_don_hang],
                    [
                        'trait_type' => 'Sản phẩm',
                        'value' => $sanPhams
                    ],
                    [
                        'trait_type' => 'Thông tin ĐVVC chịu trách nhiệm vận chuyển hàng từ NSX',
                        'value' => $donViVanChuyens
                    ]
                ]
            ];

            $pinataService = new PinataService(); // Đảm bảo đã use đúng namespace
            $metadataUri = $pinataService->uploadMetadata($metadata);

            $to_address = $request->dia_chi_vi;

            $address = $request->input('wallet_address', $to_address);

            $txHash = $this->mintNFTtoApi($address, $metadataUri); // truyền từ frontend

            // Lưu vào đơn hàng
            $donHang->transaction_hash = $txHash['transactionHash'];
            $donHang->metadata_uri = $metadataUri;
            $donHang->token_id = $txHash['tokenId'];
            $donHang->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đặt hàng thành công!',
                'transaction_hash' => $txHash['transactionHash'],
                'metadata_uri' => $metadataUri,
                'token_id' => $txHash['tokenId']
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
