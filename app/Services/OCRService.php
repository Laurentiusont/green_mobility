<?php

namespace App\Services;

use thiagoalessio\TesseractOCR\TesseractOCR;

class OCRService
{
    public function recognizeText($imagePath)
    {
        $ocr = new TesseractOCR($imagePath);
        $text = $ocr->run();

        return ['text' => $text];
    }
}
