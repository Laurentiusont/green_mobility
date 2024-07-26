<?php

namespace App\Http\Controllers;

use App\Models\Assistant;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\UserCourse;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $user = User::where('guid', auth('api')->user()->guid)
            ->first();

        return ResponseController::getResponse($user, 200, 'Get Profile User Success');
    }

    public function insertData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'guid' => 'required|string|max:40',
            'name' => 'required|string',
            'phone_number' => 'nullable|string',
            'email' => 'required|string',
            'role' => 'required|string',
        ], MessagesController::messages());

        if ($validator->fails()) {
            return ResponseController::getResponse(null, 422, $validator->errors()->first());
        }
        $data = User::create([
            'guid' => $request['guid'],
            'phone_number' => $request['phone_number'],
            'name' => $request['name'],
            'email' => $request['email'],
            'role' => $request['role'],
            'password' => Hash::make('asd123'),
        ]);

        return ResponseController::getResponse($data, 200, 'Success');
    }

    public function showData()
    {
        $data = User::all();
        if (!isset($data)) {
            return ResponseController::getResponse(null, 400, "Data not found");
        }
        $dataTable = DataTables::of($data)
            ->addIndexColumn()
            ->make(true);

        return $dataTable;
    }
    public function getData($guid)
    {
        /// GET DATA
        $data = User::where('guid', '=', $guid)
            ->first();

        if (!isset($data)) {
            return ResponseController::getResponse(null, 400, "Data not found");
        }

        return ResponseController::getResponse($data, 200, 'Success');
    }
    public function updateData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'guid' => 'required|string|max:40',
            'phone_number' => 'required|string',
            'name' => 'required|string',
            'email' => 'required|string',
            'google_id' => 'required|string',
            'role' => 'required|string',

        ], MessagesController::messages());

        if ($validator->fails()) {
            return ResponseController::getResponse(null, 422, $validator->errors()->first());
        }

        $data = User::where('guid', '=', $request['guid'])->first();

        if (!isset($data)) {
            return ResponseController::getResponse(null, 400, "Data not found");
        }
        /// UPDATE DATA
        $data->guid = $request['guid'];
        $data->name = $request['name'];
        $data->role = $request['role'];
        $data->phone_number = $request['phone_number'];
        if (isset($request['email'])) {
            $data->email = $request['email'];
        }
        if (isset($request['google_id'])) {
            $data->google_id = $request['google_id'];
        }
        $data->save();

        return ResponseController::getResponse($data, 200, 'Success');
    }
    public function deleteData($guid)
    {
        $data = User::where('guid', '=', $guid)->first();

        if (!isset($data)) {
            return ResponseController::getResponse(null, 400, "Data not found");
        }

        $data->delete();

        return ResponseController::getResponse(null, 200, 'Success');
    }
}
