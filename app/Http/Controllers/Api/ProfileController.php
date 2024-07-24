<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MessagesController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{

    public function getUser()
    {
        $user = User::where('guid', auth('api')->user()->guid)
            ->with('roles.permissions')
            ->with('position')
            ->with('department')
            ->with('division')
            ->first();

        return ResponseController::getResponse($user, 200, 'Get Profile User Success');
    }

    public function updateUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'phone_number' => 'required|numeric|min:9',
        ], MessagesController::messages());

        if ($validator->fails()) {
            return ResponseController::getResponse(null, 422, $validator->errors()->first());
        }

        $user = User::where('guid', auth('api')->user()->guid)
            ->with('role')
            ->with('position')
            ->with('department')
            ->with('division')
            ->first();

        $user->name = $request['name'];
        $user->phone_number = $request['phone_number'];
        $user->save();

        return ResponseController::getResponse($user, 200, 'Update Profile User Success');
    }
}
