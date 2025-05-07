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

    public function mint()
    {
        try {
            // Tạo metadata
            $metadata = [
                'name' => "Bằng Chứng Đơn hàng cho Lê Thanh Trườnga",
                'ma_hoa_don' => "DH00001",
                'attributes' => [
                    ['name' => 'id', 'value' => 1],
                    ['name' => 'Tên Người Nhận', 'value'    => "Lê Thanh Trường"],
                    ['name' => 'Địa Chỉ', 'value'           => "32 Xuân Diệu"],
                    ['name' => 'Số Điện Thoại', 'value'     => "012345678"],
                    ['name' => 'Tổng Tiền', 'value'         => "3.000.000.000"],
                ]
            ];

            // Tải metadata lên IPFS
            $metadataUri = $this->pinataService->uploadMetadata($metadata);
            // Mint NFT
            $address = "TA9yodjyC7YpDJSxLZF9mntnZGMaG8Eitb"; // 1. Nếu như khách hàng có địa chỉ ví TRONLINK -> lấy của hắn, 2: Nếu không thì lấy địa chỉ nhà sản xuất
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
}
