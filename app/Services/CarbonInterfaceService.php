<?php

namespace App\Services;

use GuzzleHttp\Client;

class CarbonInterfaceService
{
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = env('CARBON_INTERFACE_API_KEY');
    }

    public function getCarbonFootprint($parameters)
    {
        $response = $this->client->post('https://www.carboninterface.com/api/v1/estimates', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => $parameters,
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }
}
