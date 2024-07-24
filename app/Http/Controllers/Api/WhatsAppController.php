<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OCRService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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
        try {
            $from = $request->input('From');
            $body = $request->input('Body');
            $mediaUrl = $request->input('MediaUrl0'); // URL media jika ada gambar

            // Log input data
            Log::info('Received message', ['from' => $from, 'body' => $body, 'mediaUrl' => $mediaUrl]);

            $responseMessage = 'Data Anda telah diterima.';

            // Jika ada gambar, lakukan OCR
            if ($mediaUrl) {
                $imageContent = $this->downloadMedia($mediaUrl);
                if ($imageContent === false) {
                    throw new \Exception('Failed to download media');
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
            }

            // Kirim respon ke pengguna
            $twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
            $twilio->messages->create($from, [
                'from' => 'whatsapp:' . env('TWILIO_WHATSAPP_NUMBER'),
                'body' => $responseMessage
            ]);

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error in handleWebhook', ['error' => $e->getMessage()]);

            return response()->json(['status' => 'error', 'message' => 'An error occurred'], 500);
        }
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
