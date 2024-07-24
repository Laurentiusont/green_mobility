<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class ResponseController extends Controller
{
    public static function getResponse($data, $code, $message){
        return response()->json([
            'data' => $data,
            'code' => $code,
            'message' => $message,
        ], $code);
    }
}
