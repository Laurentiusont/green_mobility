<?php

namespace App\Services;

use GuzzleHttp\Client;

class OCRService
{
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = env('GOOGLE_CLOUD_VISION_API_KEY'); // Pastikan Anda menambahkan API Key di .env
    }

    public function recognizeText($imagePath)
    {
        $url = "https://vision.googleapis.com/v1/images:annotate?key={$this->apiKey}";

        $imageData = base64_encode(file_get_contents($imagePath));

        $body = [
            'requests' => [
                [
                    'image' => [
                        'content' => $imageData,
                    ],
                    'features' => [
                        [
                            'type' => 'TEXT_DETECTION',
                            'maxResults' => 1,
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->client->post($url, [
            'json' => $body,
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }
}
