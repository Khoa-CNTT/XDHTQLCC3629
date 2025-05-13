<?php

namespace App\Http\Controllers;

use App\Models\DonHang;
use App\Models\LichSuVanChuyen;
use App\Services\PinataService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlockChainController extends Controller
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
            $ma_van_don = Str::uuid();
            $routes = $request->input('routes'); // lấy mảng hành trình

            if (!is_array($routes) || count($routes) === 0) {
                return response()->json([
                    'success' => false,
                    'error' => 'Không có dữ liệu hành trình'
                ], 400);
            }
            // Tùy ý lấy thông tin từ phần tử đầu để hiển thị trên NFT
            // $first = $routes[0];
            $id_don_hang = $routes[0]['id_don_hang'] ?? null;

            if (!$id_don_hang) {
                return response()->json([
                    'success' => false,
                    'error' => 'Không tìm thấy id đơn hàng trong hành trình'
                ], 400);
            }

            // Lấy thông tin mã đơn hàng từ bảng don_hangs
            $donHang = DonHang::where('id', $id_don_hang)->first();

            if (!$donHang) {
                return response()->json([
                    'success' => false,
                    'error' => 'Không tìm thấy đơn hàng tương ứng'
                ], 404);
            }

            $ma_don_hang = $donHang->ma_don_hang;
            $ngay_dat = $donHang->ngay_dat;
            $ngay_giao = $donHang->ngay_giao;
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
                    'chang' => $index + 1,
                    'tuyen_so' => $route['tuyen_so'] ?? null,
                    'mo_ta' => $route['mo_ta'] ?? 'Không có mô tả',
                    'dia_chi' => $dia_chi,
                    'thoi_gian_den' => $route['thoi_gian_den'] ?? 'N/A',
                    'thoi_gian_di' => $route['thoi_gian_di'] ?? 'Đã đến kho, chờ đại lý nhận hàng',
                    'vi_tri_tiep_theo' =>  $route['vi_tri_tiep_theo'] ?? null,
                ];
            }
            $thoiGianCapNhat = Carbon::now('Asia/Ho_Chi_Minh');
            $metadata = [
                'name' => 'Bằng chứng đơn vị vận chuyển giao đơn hàng',
                'order_code' => $ma_don_hang,
                'delivery_code' => $ma_van_don,
                'time_of_execution' => $thoiGianCapNhat,
                'user_execution' => $request->input('orderData.nguoi_thuc_hien'),
                'order_date' => $ngay_dat,
                'delivery_date(expected)' => $ngay_giao,
                'description' => 'Thông tin vận đơn',
                'attributes' => $attributes
            ];

            // Upload metadata lên IPFS
            $metadataUri = $this->pinataService->uploadMetadata($metadata);

            // Lấy địa chỉ ví (có thể là đại lý hoặc NSX hoặc cố định)
            $to_address = $request->input('orderData.dia_chi_vi');

            $address = $request->input('wallet_address', $to_address);

            // Gọi API mint
            $txHash = $this->mintNFTtoApi($address, $metadataUri);

            // Kiểm tra xem mảng có phần tử nào không
            if (empty($routes) || !isset($routes[0])) {
                return response()->json([
                    'error' => 'Không có dữ liệu hợp lệ'
                ], 400);
            }

            $firstItem = $routes[0];

            $idDonHang = $firstItem['id_don_hang'] ?? null;
            $tuyenSo   = $firstItem['tuyen_so'] ?? null;

            LichSuVanChuyen::updateOrCreate(
                [
                    'id_don_hang' => $idDonHang,
                    'tuyen_so' => $tuyenSo
                ],
                [
                    'transaction_hash' => $txHash['transactionHash'],
                    'metadata_uri' => $metadataUri,
                    'token_id' => $txHash['tokenId'],
                    'ma_van_don' => $ma_van_don,
                ]
            );

            return response()->json([
                'success' => true,
                'transaction_hash' => $txHash['transactionHash'],
                'metadata_uri' => $metadataUri,
                'tokenId' => $txHash['tokenId'],
                'id_don_hang' => $idDonHang,
                'tuyen_so' => $tuyenSo,
                'ma_van_don' => $ma_van_don,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
