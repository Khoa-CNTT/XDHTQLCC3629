<?php

namespace App\Http\Controllers;

use App\Services\PinataService;
use Illuminate\Http\Request;

class TestController extends Controller
{
    protected $pinataService;

    public function __construct(PinataService $pinataService)
    {
        $this->pinataService = $pinataService;
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

    public function mint(Request $request)
    {
        try {
            $routes = $request->input('routes'); // lấy mảng hành trình

            if (!is_array($routes) || count($routes) === 0) {
                return response()->json([
                    'success' => false,
                    'error' => 'Không có dữ liệu hành trình'
                ], 400);
            }
            // Tùy ý lấy thông tin từ phần tử đầu để hiển thị trên NFT
            $first = $routes[0];
            // $last = end($routes);
            // Tạo metadata từ toàn bộ tuyến
            $attributes = [];
            foreach ($routes as $index => $route) {
                if ($route['mo_ta'] === 'Vị trí nhà sản xuất') {
                    $dia_chi = $route['dia_chi_nsx'] ?? 'Không có địa chỉ nhà sản xuất';
                } elseif ($route['mo_ta'] === 'Vị trí đại lý') {
                    $dia_chi = $route['dia_chi_dai_ly'] ?? 'Không có địa chỉ đại lý';
                } else {
                    $dia_chi = $route['dia_chi_kho'] ?? 'Không có địa chỉ kho';
                }
                $attributes[] = [
                    'Chặng' => $index + 1,
                    'Tuyến số' => $route['tuyen_so'] ?? null,
                    'Mô tả' => $route['mo_ta'] ?? 'Không có mô tả',
                    'Địa chỉ' => $dia_chi,
                    'Thời gian đến' => $route['thoi_gian_den'] ?? 'N/A',
                    'Thời gian đi' => $route['thoi_gian_di'] ?? 'Đã đến kho, chờ đại lý nhận hàng',
                    'Vị trí tiếp theo' =>  $route['vi_tri_tiep_theo'] ?? null,
                ];
            }

            $metadata = [
                'name' => 'Bằng Chứng Vận Chuyển Đơn Hàng #' . $first['id_don_hang'],
                'ma_hoa_don' => 'DH' . str_pad($first['id_don_hang'], 3, '0', STR_PAD_LEFT),
                'attributes' => $attributes
            ];

            // Upload metadata lên IPFS
            $metadataUri = $this->pinataService->uploadMetadata($metadata);

            // Lấy địa chỉ ví (có thể là đại lý hoặc NSX hoặc cố định)
            $address = $request->input('wallet_address', 'TDyWikx2s9DpdLVi5jc1MLYrwiyihzcDRj');

            // Gọi API mint
            $txHash = $this->mintNFTtoApi($address, $metadataUri);

            return response()->json([
                'success' => true,
                'transaction_hash' => $txHash['transactionHash'],
                'metadata_uri' => $metadataUri,
                'tokenId' => $txHash['tokenId']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
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
                $id_nguoi_thuc_hien = $user->id;
                if ($request->input('v.tinh_trang') == 1 || $$request->input('v.tinh_trang') == 0) {
                    $tinh_trang_moi = 4;
                }

                DonHang::where('id', $request->input('v.id') )->update([
                    'tinh_trang'    =>  $tinh_trang_moi
                ]);

                LichSuDonHang::where('id_don_hang', $request->input('v.id') )->update([
                    'tinh_trang'    =>  $tinh_trang_moi
                ]);

                $thoiGianCapNhat = Carbon::now('Asia/Ho_Chi_Minh');
                $metadata = [
                    'name' => 'Đơn hàng #' . $request->input('v.ma_don_hang'),
                    'description' => 'Đại lý hủy đơn hàng',
                    'time' => $thoiGianCapNhat,
                    'attributes' => [
                        ['trait_type' => 'Người thực hiện', 'value' => $request->input('orderData.nguoi_thuc_hien')],
                        ['trait_type' => 'Tổng tiền', 'value' => $request->input('v.tong_tien')],
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
                    'action'                    =>  'Cancel order',
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
}
