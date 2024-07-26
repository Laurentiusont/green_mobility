<?php

namespace App\Http\Controllers;

use App\Http\Controllers\MessagesController;
use App\Models\MerchantLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;

class MerchantLocationController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Log the incoming request data
            Log::info('Index method called with request:', $request->all());
    
            // Fetch the data from the database
            $data = MerchantLocation::with('merchant_masters')
                ->orderBy('created_at', 'desc')
                ->get();
    
            // Log the fetched data
            Log::info('Fetched data:', $data->toArray());
    
            // Return the response
            return ResponseController::getResponse($data, 200, 'Success');
        } catch (\Exception $e) {
            // Log the exception message and stack trace
            Log::error('Error in index method:', [
                'message' => $e->getMessage(),
                'stack' => $e->getTraceAsString(),
            ]);
    
            // Return an error response
            return ResponseController::getResponse(null, 500, 'Internal Server Error');
        }
    }

    public function getData($guid)
    {
        /// GET DATA
        $data = MerchantLocation::where('guid', '=', $guid)
            ->first();

        if (!isset($data)) {
            return ResponseController::getResponse(null, 400, "Data not found");
        }

        return ResponseController::getResponse($data, 200, 'Success');
    }

    public function getAllDataTable()
    {
        $data = MerchantLocation::with('merchant_masters')->get();

        $dataTable = DataTables::of($data)
            ->addIndexColumn()
            ->make(true);

        return $dataTable;
    }

    public function insertData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'country' => 'required|string', 
            'city' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'phone_number' => 'nullable|string',
            'merchant_master_guid' => 'required|uuid|exists:merchant_masters,guid',
        ], MessagesController::messages());
    
        if ($validator->fails()) {
            return ResponseController::getResponse(null, 422, $validator->errors()->first());
        }
    
        $data = MerchantLocation::create([
            'name' => $request->name,
            'country' => $request->country,
            'city' => $request->city,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'phone_number' => $request->phone_number,
            'merchant_master_guid' => $request->merchant_master_guid,
        ]);
    
        return ResponseController::getResponse($data, 200, 'Success');
    }

    public function updateData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'guid' => 'required|string|max:36',
            'name' => 'required|string',
            'country' => 'required|string', 
            'city' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'phone_number' => 'nullable|string',
            'merchant_master_guid' => 'required|uuid|exists:merchant_masters,guid',
        ], MessagesController::messages());
    
        if ($validator->fails()) {
            return ResponseController::getResponse(null, 422, $validator->errors()->first());
        }
    
        // Get data
        $data = MerchantLocation::where('guid', '=', $request->guid)->first();
    
        if (!$data) {
            return ResponseController::getResponse(null, 400, "Data not found");
        }
    
        // Update data
        $data->name = $request->name;
        $data->country = $request->country;
        $data->city = $request->city;
        $data->latitude = $request->latitude;
        $data->longitude = $request->longitude;
        $data->phone_number = $request->phone_number;
        $data->merchant_master_guid = $request->merchant_master_guid;
        $data->save();
    
        return ResponseController::getResponse($data, 200, 'Success');
    }

    public function deleteData($guid)
    {
        /// GET DATA
        $data = MerchantLocation::where('guid', '=', $guid)->first();

        if (!isset($data)) {
            return ResponseController::getResponse(null, 400, "Data not found");
        }

        $data->delete();

        return ResponseController::getResponse(null, 200, 'Success');
    }
}
