<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CarbonInterfaceService;
use Illuminate\Http\Request;

class CarbonController extends Controller
{
    protected $carbonService;

    public function __construct(CarbonInterfaceService $carbonService)
    {
        $this->carbonService = $carbonService;
    }

    public function calculateCarbonFootprint(Request $request)
    {
        $parameters = [
            'type' => 'flight', // Misalnya untuk perhitungan penerbangan
            'passengers' => $request->input('passengers'),
            'legs' => [
                [
                    'departure_airport' => $request->input('departure_airport'),
                    'destination_airport' => $request->input('destination_airport'),
                ],
            ],
        ];

        $result = $this->carbonService->getCarbonFootprint($parameters);

        return response()->json($result);
    }
}
