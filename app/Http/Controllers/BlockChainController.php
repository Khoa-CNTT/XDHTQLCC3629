<?php

namespace App\Http\Controllers;

use App\Models\LichSuVanChuyen;
use App\Services\PinataService;
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
            $ngay_giao = now()->addDays(4);
            $ma_van_don = Str::uuid();
            $routes = $request->input('routes'); // lấy mảng hành trình

            if (!is_array($routes) || count($routes) === 0) {
                return response()->json([
                    'success' => false,
                    'error' => 'Không có dữ liệu hành trình'
                ], 400);
            }
            // Tùy ý lấy thông tin từ phần tử đầu để hiển thị trên NFT
            $first = $routes[0];
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
                'description' => 'Thông tin vận đơn',
                'ma_hoa_don' => 'DH' . str_pad($first['id_don_hang'], 3, '0', STR_PAD_LEFT),
                'ma_van_don' => $ma_van_don,
                'ngay_giao_du_kien' => $ngay_giao,
                'attributes' => $attributes
            ];

            // Upload metadata lên IPFS
            $metadataUri = $this->pinataService->uploadMetadata($metadata);

            // Lấy địa chỉ ví (có thể là đại lý hoặc NSX hoặc cố định)
            $to_address = $request->dia_chi_vi;

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
                    'token_id' => $txHash['tokenId']
                ]
            );

            return response()->json([
                'success' => true,
                'transaction_hash' => $txHash['transactionHash'],
                'metadata_uri' => $metadataUri,
                'tokenId' => $txHash['tokenId'],
                'id_don_hang' => $idDonHang,
                'tuyen_so' => $tuyenSo,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
