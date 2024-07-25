<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OCRService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Twilio\Rest\Client;
use App\Models\UserState;

class WhatsAppController extends Controller
{
    protected $ocrService;

    public function __construct(OCRService $ocrService)
    {
        $this->ocrService = $ocrService;
    }

    public function handleWebhook(Request $request)
    {
        $from = $request->input('From');
        $body = trim($request->input('Body'));
        $mediaUrl = $request->input('MediaUrl0');
        $latitude = $request->input('Latitude');
        $longitude = $request->input('Longitude');

        Log::info('Received message', ['from' => $from, 'body' => $body, 'mediaUrl' => $mediaUrl, 'latitude' => $latitude, 'longitude' => $longitude]);

        $state = $this->getUserState($from);

        if (strtolower($body) === 'menu') {
            $this->sendMenu($from);
            $this->setUserState($from, null);
            return response()->json(['status' => 'success']);
        }

        switch ($state) {
            case 'awaiting_image':
                if ($mediaUrl) {
                    $this->processOCR($from, $mediaUrl);
                } else {
                    $this->sendMessage($from, 'Silakan unggah gambar tanda terima untuk diproses oleh OCR.');
                }
                break;
            case 'awaiting_location':
                if ($latitude && $longitude) {
                    $this->processParkingLocation($from, $latitude, $longitude);
                } else {
                    $this->sendMessage($from, 'Silakan bagikan lokasi Anda untuk mencari lahan parkir.');
                }
                break;
            case 'carbon_calculator':
                $this->handleCarbonCalculator($from, $body);
                break;
            default:
                $this->handleMenuSelection($from, $body);
                break;
        }

        return response()->json(['status' => 'success']);
    }

    private function handleMenuSelection($from, $body)
    {
        switch ($body) {
            case '1':
                $this->sendMessage($from, 'Anda memilih Carbon Emission Calculator. Ketik jenis kendaraan Anda (mobil, motor, bus) dan jarak tempuh dalam km. Contoh: mobil 15');
                $this->setUserState($from, 'carbon_calculator');
                break;
            case '2':
                $this->sendMessage($from, 'Silakan unggah gambar tanda terima untuk diproses oleh OCR.');
                $this->setUserState($from, 'awaiting_image');
                break;
            case '3':
                $this->sendMessage($from, 'Silakan bagikan lokasi Anda untuk mencari lahan parkir.');
                $this->setUserState($from, 'awaiting_location');
                break;
            default:
                $this->sendMessage($from, 'Pilihan tidak valid. Ketik "menu" untuk melihat pilihan yang tersedia.');
                break;
        }
    }

    private function handleCarbonCalculator($from, $body)
    {
        $parts = explode(' ', $body);
        if (count($parts) == 2) {
            $vehicleType = strtolower($parts[0]);
            $distance = floatval($parts[1]);

            $carbonEmission = $this->calculateCarbonEmission($vehicleType, $distance);

            if ($carbonEmission !== null) {
                $this->sendMessage($from, "Estimasi emisi karbon untuk $vehicleType dengan jarak tempuh $distance km adalah $carbonEmission kg CO2.");
            } else {
                $this->sendMessage($from, 'Jenis kendaraan tidak valid. Ketik mobil, motor, atau bus diikuti dengan jarak tempuh dalam km. Contoh: mobil 15');
            }
        } else {
            $this->sendMessage($from, 'Format tidak valid. Ketik jenis kendaraan dan jarak tempuh dalam km. Contoh: mobil 15');
        }
        $this->setUserState($from, null);
    }

    private function calculateCarbonEmission($vehicleType, $distance)
    {
        $emissionRates = [
            'mobil' => 0.21, // kg CO2 per km
            'motor' => 0.1,  // kg CO2 per km
            'bus' => 0.27    // kg CO2 per km
        ];

        if (array_key_exists($vehicleType, $emissionRates)) {
            return $emissionRates[$vehicleType] * $distance;
        } else {
            return null;
        }
    }

    private function sendMenu($to)
    {
        $menuMessage = "Silakan pilih salah satu opsi berikut:\n1. Carbon Emission Calculator\n2. OCR Upload Receipt\n3. Mencari Lahan Parkir berdasarkan share lokasi";
        $this->sendMessage($to, $menuMessage);
    }

    private function sendMessage($to, $message)
    {
        $twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
        $twilio->messages->create($to, [
            'from' => 'whatsapp:' . env('TWILIO_WHATSAPP_NUMBER'),
            'body' => $message
        ]);
    }

    private function processOCR($from, $mediaUrl)
    {
        $imageContent = $this->downloadMedia($mediaUrl);
        if ($imageContent === false) {
            $this->sendMessage($from, 'Failed to download media');
            $this->setUserState($from, null);
            return;
        }

        $imagePath = 'uploads/' . uniqid() . '.jpg';
        Storage::disk('public')->put($imagePath, $imageContent);
        $fullImagePath = storage_path("app/public/{$imagePath}");

        $ocrResult = $this->ocrService->recognizeText($fullImagePath);

        if (!empty($ocrResult['responses'][0]['fullTextAnnotation']['text'])) {
            $detectedText = $ocrResult['responses'][0]['fullTextAnnotation']['text'];
            $responseMessage = "Teks terdeteksi pada gambar:\n" . $detectedText;
        } else {
            $responseMessage = "Tidak ada teks yang terdeteksi pada gambar.";
        }

        $this->sendMessage($from, $responseMessage);
        $this->setUserState($from, null);
    }

    private function processParkingLocation($from, $latitude, $longitude)
    {
        $responseMessage = "Koordinat lokasi yang Anda kirim adalah:\nLatitude: $latitude\nLongitude: $longitude\n(Implementasikan fungsi pencarian lahan parkir di sini)";
        $this->sendMessage($from, $responseMessage);
        $this->setUserState($from, null);
    }

    private function downloadMedia($mediaUrl)
    {
        try {
            $response = Http::withBasicAuth(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'))->timeout(30)->get($mediaUrl);

            if ($response->successful() && $response->header('Content-Type') !== 'application/xml') {
                return $response->body();
            } else {
                throw new \Exception('Invalid media content type or request failed');
            }
        } catch (\Exception $e) {
            Log::error('Error in downloadMedia', ['error' => $e->getMessage()]);
            return false;
        }
    }

    private function getUserState($from)
    {
        $userState = UserState::where('user_id', $from)->first();
        return $userState ? $userState->state : null;
    }

    private function setUserState($from, $state)
    {
        $userState = UserState::firstOrNew(['user_id' => $from]);
        $userState->state = $state;
        $userState->save();
    }
}
