<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\OCRService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\NumberParseException;
use Twilio\Rest\Client;

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

        Log::info('Received message', ['from' => $from, 'body' => $body]);

        if (!$this->isUserRegistered($from)) {
            $this->sendMessage($from, 'Nomor Anda tidak terdaftar. Silakan daftar terlebih dahulu.');
            return response()->json(['status' => 'user_not_registered']);
        }

        if (strtolower($body) === 'menu') {
            $this->sendMenu($from);
            $this->setUserState($from, 'menu');
            return response()->json(['status' => 'success']);
        }

        $state = $this->getUserState($from);
        switch ($state) {
            case 'awaiting_image':
                $mediaUrl = $request->input('MediaUrl0');
                if ($mediaUrl) {
                    $this->processOCR($from, $mediaUrl);
                } else {
                    $this->sendMessage($from, 'Silakan unggah gambar tanda terima untuk diproses oleh OCR.');
                }
                break;
            case 'awaiting_location':
                $latitude = $request->input('Latitude');
                $longitude = $request->input('Longitude');
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
            case 'Carbon Calculator':
                $this->sendMessage($from, 'Anda memilih Carbon Emission Calculator. Ketik jenis kendaraan Anda (mobil, motor, bus) dan jarak tempuh dalam km. Contoh: mobil 15');
                $this->setUserState($from, 'carbon_calculator');
                break;
            case 'OCR Upload Receipt':
                $this->sendMessage($from, 'Silakan unggah gambar tanda terima untuk diproses oleh OCR.');
                $this->setUserState($from, 'awaiting_image');
                break;
            case 'Mencari Lahan Parkir':
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
            'mobil' => 0.21,
            'motor' => 0.1,
            'bus' => 0.27
        ];

        if (array_key_exists($vehicleType, $emissionRates)) {
            return $emissionRates[$vehicleType] * $distance;
        } else {
            return null;
        }
    }

    private function sendMenu($to)
    {
        $twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
        $twilio->messages->create(
            $to,
            [
                "contentSid" => "HXdb8be527cb8afbc187a8b241a7348ee5",
                "from" => "whatsapp:" . env('TWILIO_WHATSAPP_NUMBER'),
                "messagingServiceSid" => env('TWILIO_MESSAGING_SERVICE_SID'), // optional, jika menggunakan messaging service
            ]
        );
    }

    private function sendMessage($to, $message)
    {
        $twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
        $twilio->messages->create($to, [
            'from' => 'whatsapp:' . env('TWILIO_WHATSAPP_NUMBER'),
            'body' => $message
        ]);
    }

    private function isUserRegistered($phoneNumber)
    {
        // Menghapus awalan 'whatsapp:'
        $phoneNumber = str_replace('whatsapp:', '', $phoneNumber);

        $formattedPhoneNumber = $this->formatPhoneNumberToLocal($phoneNumber);
        // Cari pengguna dengan nomor telepon yang telah dinormalisasi
        return User::where('phone_number', $phoneNumber)->orWhere('phone_number', $formattedPhoneNumber)->exists();
    }

    private function formatPhoneNumberToLocal($phoneNumber)
    {
        $phoneUtil = PhoneNumberUtil::getInstance();
        try {
            $numberProto = $phoneUtil->parse($phoneNumber);

            $countryCode = $phoneUtil->getRegionCodeForNumber($numberProto);

            $formattedPhoneNumber = $phoneUtil->format($numberProto, PhoneNumberFormat::NATIONAL);

            $formattedPhoneNumber = '0' . ltrim($formattedPhoneNumber, '0');

            Log::info('Phone number formatted', [
                'original' => $phoneNumber,
                'formatted' => $formattedPhoneNumber,
                'country_code' => $countryCode
            ]);

            return $formattedPhoneNumber;
        } catch (NumberParseException $e) {
            Log::error('Error parsing phone number', ['error' => $e->getMessage()]);
            return false;
        }
    }

    private function getUserState($from)
    {
        $from = str_replace('whatsapp:', '', $from);
        $user = User::where('phone_number', $from)->first();
        return $user ? $user->status : null;
    }

    private function setUserState($from, $state)
    {
        $from = str_replace('whatsapp:', '', $from);
        $user = User::where('phone_number', $from)->first();
        if ($user) {
            $user->status = $state;
            $user->save();
        }
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
}
