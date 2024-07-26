<?php

namespace App\Http\Controllers;

use App\Http\Controllers\MessagesController;
use App\Models\MerchantMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class MerchantMasterController extends Controller
{
    public function index(Request $request)
    {

        $data = MerchantMaster::all();

        return ResponseController::getResponse($data, 200, 'Success');
    }

    public function getData($guid)
    {
        /// GET DATA
        $data = MerchantMaster::where('guid', '=', $guid)
            ->first();

        if (!isset($data)) {
            return ResponseController::getResponse(null, 400, "Data not found");
        }

        return ResponseController::getResponse($data, 200, 'Success');
    }

    public function getAllDataTable()
    {
        $data = MerchantMaster::orderBy('created_at', 'desc')
            ->get();

        $dataTable = DataTables::of($data)
            ->addIndexColumn()
            ->make(true);

        return $dataTable;
    }

    public function insertData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
        ], MessagesController::messages());

        if ($validator->fails()) {
            return ResponseController::getResponse(null, 422, $validator->errors()->first());
        }

        $data = MerchantMaster::create([
            'name' => $request['name'],
        ]);

        return ResponseController::getResponse($data, 200, 'Success');
    }

    public function updateData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'guid' => 'required|string|max:36',
            'name' => 'required|string',
        ], MessagesController::messages());

        if ($validator->fails()) {
            return ResponseController::getResponse(null, 422, $validator->errors()->first());
        }

        /// GET DATA
        $data = MerchantMaster::where('guid', '=', $request['guid'])->first();

        if (!isset($data)) {
            return ResponseController::getResponse(null, 400, "Data not found");
        }

        /// UPDATE DATA
        $data->name = $request['name'];
        $data->save();

        return ResponseController::getResponse($data, 200, 'Success');
    }

    public function deleteData($guid)
    {
        /// GET DATA
        $data = MerchantMaster::where('guid', '=', $guid)->first();

        if (!isset($data)) {
            return ResponseController::getResponse(null, 400, "Data not found");
        }

        $data->delete();

        return ResponseController::getResponse(null, 200, 'Success');
    }
}
