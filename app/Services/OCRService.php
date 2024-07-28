<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OCRService
{
    public function recognizeText($imagePath)
    {
        $apiKey = env('OCR_SPACE_API_KEY');
        $response = Http::attach(
            'file',
            file_get_contents($imagePath),
            basename($imagePath)
        )->post('https://api.ocr.space/parse/image', [
            'apikey' => $apiKey,
            'language' => 'eng',
            'isTable' => true, // Menambahkan parameter isTable
            'OCREngine' => 2,  // Menambahkan parameter OCREngine
        ]);

        $data = $response->json();
        if ($data['IsErroredOnProcessing']) {
            throw new \Exception($data['ErrorMessage'][0]);
        }

        return ['text' => $data['ParsedResults'][0]['ParsedText']];
    }
}
