<?php

namespace App\Services;

use GuzzleHttp\Client;

class PinataService
{
    protected $client;
    protected $apiKey;
    protected $secretApiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = env('PINATA_API_KEY');
        $this->secretApiKey = env('PINATA_SECRET_API_KEY');
    }

    public function uploadMetadata($metadata)
    {
        $response = $this->client->post('https://api.pinata.cloud/pinning/pinJSONToIPFS', [
            'headers' => [
                'pinata_api_key' => $this->apiKey,
                'pinata_secret_api_key' => $this->secretApiKey,
            ],
            'json' => $metadata,
        ]);

        $result = json_decode($response->getBody(), true);
        return "https://gateway.pinata.cloud/ipfs/{$result['IpfsHash']}";
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
}
