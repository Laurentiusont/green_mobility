<?php

namespace App\Http\Controllers;

use App\Models\ParkingLot;
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
                    $responseMessage = $this->processOCR($from, $mediaUrl);
                    $this->sendMessage($from, $responseMessage);
                } else {
                    $this->sendMessage($from, 'Silakan unggah gambar tanda terima untuk diproses oleh OCR.');
                }
                break;
            case 'awaiting_location':
                $latitude = $request->input('Latitude');
                $longitude = $request->input('Longitude');
                if ($latitude && $longitude) {
                    $fromClean = str_replace('whatsapp:', '', $from);
                    $formattedPhoneNumber = $this->formatPhoneNumberToLocal($fromClean);
                    $user = User::where('phone_number', $fromClean)->orWhere('phone_number', $formattedPhoneNumber)->first();
                    $user->latitude = $latitude;
                    $user->longitude = $longitude;
                    $user->status = 'awaiting_radius';
                    $user->save();
                    $this->sendMessage($from, 'Silakan masukkan radius pencarian dalam kilometer:');
                } else {
                    $this->sendMessage($from, 'Silakan bagikan lokasi Anda untuk mencari lahan parkir.');
                }
                break;
            case 'awaiting_radius':
                if (is_numeric($body)) {
                    $radius = (float)$body;
                    $fromClean = str_replace('whatsapp:', '', $from);
                    $formattedPhoneNumber = $this->formatPhoneNumberToLocal($fromClean);
                    $user = User::where('phone_number', $fromClean)->orWhere('phone_number', $formattedPhoneNumber)->first();
                    $latitude = $user->latitude;
                    $longitude = $user->longitude;
                    $parkingLots = $this->findParkingLotsWithinRadius($latitude, $longitude, $radius);

                    if ($parkingLots->isNotEmpty()) {
                        $responseMessage = "Lahan parkir terdekat dalam radius {$radius} km:\n";

                        foreach ($parkingLots as $lot) {
                            $mapsLink = "https://www.google.com/maps/search/?api=1&query={$lot->latitude},{$lot->longitude}";
                            $responseMessage .= "\nNama: {$lot->name}\n" .
                                "Lokasi: {$lot->city}, {$lot->country}\n" .
                                "Jumlah tempat tersedia: {$lot->available_spots}\n" .
                                "Telepon: {$lot->phone_number}\n" .
                                "Google Maps: {$mapsLink}\n";
                        }
                    } else {
                        $responseMessage = "Tidak ada lahan parkir yang ditemukan dalam radius {$radius} km dari lokasi Anda.";
                    }

                    $this->sendMessage($from, $responseMessage);
                    $user->status = null;
                    $user->latitude = null;
                    $user->longitude = null;
                    $user->save();
                } else {
                    $this->sendMessage($from, 'Silakan masukkan radius yang valid dalam kilometer:');
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
        $user = User::where('phone_number', str_replace('whatsapp:', '', $from))->first();
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
        if (empty($message)) {
            Log::error('Failed to send message: Message body is empty');
            return;
        }

        $twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
        $twilio->messages->create($to, [
            'from' => 'whatsapp:' . env('TWILIO_WHATSAPP_NUMBER'),
            'body' => $message
        ]);
    }

    private function isUserRegistered($phoneNumber)
    {
        $phoneNumber = str_replace('whatsapp:', '', $phoneNumber);

        $formattedPhoneNumber = $this->formatPhoneNumberToLocal($phoneNumber);
        return User::where('phone_number', $phoneNumber)->orWhere('phone_number', $formattedPhoneNumber)->exists();
    }

    private function formatPhoneNumberToLocal($phoneNumber)
    {
        $phoneUtil = PhoneNumberUtil::getInstance();
        try {
            $numberProto = $phoneUtil->parse($phoneNumber);

            $countryCode = $phoneUtil->getRegionCodeForNumber($numberProto);

            $formattedPhoneNumber = $phoneUtil->format($numberProto, PhoneNumberFormat::NATIONAL);

            $formattedPhoneNumber = preg_replace('/[^0-9]/', '', $formattedPhoneNumber);

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
        $formattedPhoneNumber = $this->formatPhoneNumberToLocal($from);
        $user = User::where('phone_number', $from)->orWhere('phone_number', $formattedPhoneNumber)->first();
        return $user ? $user->status : null;
    }

    private function setUserState($from, $state)
    {
        $from = str_replace('whatsapp:', '', $from);
        $formattedPhoneNumber = $this->formatPhoneNumberToLocal($from);
        $user = User::where('phone_number', $from)->orWhere('phone_number', $formattedPhoneNumber)->first();
        if ($user) {
            $user->status = $state;
            $user->save();
        }
    }

    public function processOCR($from, $mediaUrl)
    {
        $imageContent = $this->downloadMedia($mediaUrl);
        if ($imageContent === false) {
            $this->sendMessage($from, 'Failed to download media');
            $this->setUserState($from, null);
            return "Failed to download media.";
        }

        $imagePath = 'uploads/' . uniqid() . '.jpg';
        Storage::disk('public')->put($imagePath, $imageContent);
        $fullImagePath = storage_path("app/public/{$imagePath}");

        $ocrResult = $this->ocrService->recognizeText($fullImagePath);

        if (!empty($ocrResult['text'])) {
            $detectedText = $ocrResult['text'];
            $responseMessage = "OCR Text: \n" . $detectedText . "\n\n";

            if (stripos($detectedText, 'alfamart') !== false) {
                $responseMessage = $this->processAlfamartOCR($detectedText, $from, $imagePath);
            } else if (stripos($detectedText, 'strava') !== false) {
                $responseMessage = $this->processStravaOCR($detectedText, $from, $imagePath);
            } else {
                $responseMessage = "No relevant keywords detected.";
            }
        } else {
            $responseMessage = "No text detected in the image.";
        }

        $this->setUserState($from, null);
        return $responseMessage;
    }

    // Method to process Alfamart OCR and store point history
    private function processAlfamartOCR($text, $from, $imagePath)
    {
        $pattern = '/^(?!.*(?:Subtotal|Total Diskon|A-Poin)).*Total\s+([\d,.]+)/im';
        if (preg_match($pattern, $text, $matches)) {
            $numberWithCommas = $matches[1];
            $numberWithCommas = str_replace(',', '', $numberWithCommas);
            $total = floatval($numberWithCommas);

            $responseMessage = "Total detected: Rp " . number_format($total, 0, ',', '.') . "\nTotal points earned: " . $this->calculatePoints($total);
            $this->storePointHistory($from, $total, $imagePath);
        } else {
            $responseMessage = "No matching total found.";
        }
        return $responseMessage;
    }

    // Method to process Strava OCR and store point history
    // private function processStravaOCR($text, $from, $imagePath)
    // {
    //     $responseMessage = "";
    //     $ridePattern = '/Ride\s+Elev Gain\s+Time\s+([\d.,]+)\s*km/i';
    //     $distancePattern = '/Ride\s+Steps\s+Time\s+([\d.,]+)\s*km/i';
    //     $rideDistance = null;
    //     $actualDistance = null;

    //     if (preg_match($ridePattern, $text, $matches)) {
    //         $rideDistance = str_replace(',', '.', $matches[1]);
    //         $rideDistance = floatval($rideDistance);
    //         $responseMessage .= "Total ride detected: " . number_format($rideDistance, 2, '.', '') . " km\n";
    //     }

    //     if (preg_match($distancePattern, $text, $matches)) {
    //         $actualDistance = str_replace(',', '.', $matches[1]);
    //         $actualDistance = floatval($actualDistance);
    //         $responseMessage .= "Total distance detected: " . number_format($actualDistance, 2, '.', '') . " km\n";
    //     }

    //     if ($rideDistance || $actualDistance) {
    //         $this->storeStravaPointHistory($from, $rideDistance, $actualDistance, $imagePath);
    //     } else {
    //         $responseMessage .= "No ride or distance information found.\n";
    //     }
    //     return $responseMessage;
    // }

    // // Method to store point history for Strava
    // private function storeStravaPointHistory($from, $rideDistance, $actualDistance, $file_url)
    // {
    //     $fromClean = str_replace('whatsapp:', '', $from);
    //     $user = User::where('phone_number', $fromClean)->first();

    //     if ($user) {
    //         $total = $rideDistance ? $rideDistance : $actualDistance;
    //         $point = $rideDistance ? ($total / 2) : ($total / 1);

    //         $pointHistory = new \App\Models\PointHistory([
    //             'total' => $total,
    //             'point' => floor($point),
    //             'file_url' => $file_url,
    //             'user_guid' => $user->guid
    //         ]);
    //         $pointHistory->save();
    //     }
    // }

    private function processStravaOCR($text, $from, $imagePath)
    {
        $responseMessage = "";
        $ridePattern = '/Ride\s+Elev Gain\s+Time\s+([\d.,]+)\s*km/i';
        $distancePattern = '/Distance\s+Time\s+Elev Gain\s+([\d.,]+)\s*km/i';
        $runPattern =  '/Run\s+Pace\s+Time\s+([\d.,]+)\s*km/i';
        $rideDistance = null;
        $actualDistance = null;
        $runs = null;

        Log::info('Processing OCR text for Strava', ['text' => $text]);

        if (preg_match($ridePattern, $text, $matches)) {
            $rideDistance = str_replace(',', '.', $matches[1]);
            $rideDistance = floatval($rideDistance);
            $responseMessage .= "Total ride detected: " . number_format($rideDistance, 2, '.', '') . " km\n";
        }

        if (preg_match($distancePattern, $text, $matches)) {
            $actualDistance = str_replace(',', '.', $matches[1]);
            $actualDistance = floatval($actualDistance);
            $responseMessage .= "Total distance detected: " . number_format($actualDistance, 0, '.', '') . " km\n";
        }

        if (preg_match($runPattern, $text, $matches)) {
            $runs = intval(str_replace(',', '', $matches[1]));
            $runs = floatval($runs);
            $responseMessage .= "Total run distance detected: " . number_format($runs) . "\n";
        }

        if ($rideDistance || $actualDistance || $runs) {
            $this->storeStravaPointHistory($from, $rideDistance, $actualDistance, $runs, $imagePath);
        } else {
            $responseMessage .= "No ride, distance, or steps information found.\n";
        }
        return $responseMessage;
    }

    private function storeStravaPointHistory($from, $rideDistance, $actualDistance, $runs, $file_url)
    {
        $fromClean = str_replace('whatsapp:', '', $from);
        $user = User::where('phone_number', $fromClean)->first();

        if ($user) {
            $total = $rideDistance ? $rideDistance : ($runs ? $runs : 0);
            $point = $rideDistance ? ($total / 2) : $total;

            $pointHistory = new \App\Models\PointHistory([
                'total' => $total,
                'point' => floor($point),
                'file_url' => $file_url,
                'user_guid' => $user->guid
            ]);
            $pointHistory->save();
        }
    }

    // Method to store point history for Alfamart
    private function storePointHistory($from, $total, $file_url)
    {
        $fromClean = str_replace('whatsapp:', '', $from);
        $user = User::where('phone_number', $fromClean)->first();

        if ($user) {
            $pointHistory = new \App\Models\PointHistory([
                'total' => $total,
                'point' => $this->calculatePoints($total),
                'file_url' => $file_url,
                'user_guid' => $user->guid
            ]);
            $pointHistory->save();
        }
    }

    private function calculatePoints($total)
    {
        return floor($total / 10000);
    }

    private function findParkingLotsWithinRadius($latitude, $longitude, $radius)
    {
        $distanceFormula = "
            (6371 * acos(
                cos(radians(?)) *
                cos(radians(latitude)) *
                cos(radians(longitude) - radians(?)) +
                sin(radians(?)) *
                sin(radians(latitude))
            ))
        ";

        return ParkingLot::select('*')
            ->selectRaw("{$distanceFormula} AS distance", [$latitude, $longitude, $latitude])
            ->having('distance', '<', $radius)
            ->orderBy('distance')
            ->get();
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
