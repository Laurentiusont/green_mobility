<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OCRService;
use Illuminate\Http\Request;

class OCRController extends Controller
{
    protected $ocrService;

    public function __construct(OCRService $ocrService)
    {
        $this->ocrService = $ocrService;
    }

    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $path = $request->file('image')->store('uploads', 'public');
        $fullPath = storage_path("app/public/{$path}");

        $result = $this->ocrService->recognizeText($fullPath);

        return response()->json($result);
    }
}
