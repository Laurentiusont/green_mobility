<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MessagesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function loginUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ], MessagesController::messages());

        if ($validator->fails()) {
            return ResponseController::getResponse(null, 422, $validator->errors()->first());
        }

        $user = User::where([
            ['email', '=', $request['email']],
        ])
            ->first();

        if (empty($user)) {
            return ResponseController::getResponse(null, 400, "User Not Found");
        }

        if (!Hash::check($request->get('password'), $user->password)) {
            return ResponseController::getResponse(null, 400, "Invalid Credentials");
        }

        $requiredChangePassword = false;
        if ($user->status == UserStatusEnum::Deleted) {
            return ResponseController::getResponse(null, 400, "Your account is deactivated, please contact your Admin");
        } else if ($user->status == UserStatusEnum::Pending) {
            $requiredChangePassword = true;
        }

        $payloadable = [
            'guid' => $user->guid,
            'name' => $user->name,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
        ];

        $token = JWTAuth::fromUser($user, $payloadable);

        $respone = [
            "access_token" => $token,
            "required_change_password" => $requiredChangePassword,
        ];

        return ResponseController::getResponse($respone, 200, 'Login Success');
    }

    public function loginAdmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ], MessagesController::messages());

        if ($validator->fails()) {
            return ResponseController::getResponse(null, 422, $validator->errors()->first());
        }

        // $user = User::select('users.guid', 'users.name', 'users.email', 'users.phone_number', 'users.password', 'users.role_guid', 'roles.role_name')
        //     ->join('roles', 'roles.guid', '=', 'users.role_guid')
        //     ->where([
        //         ['users.email', '=', $request['email']],
        //         ['users.status', '=', 'active'],
        //     ])
        //     ->first();

        $user = User::where([
            ['email', '=', $request['email']],
            ['status', '=', 'active'],
        ])
            ->first();

        if (empty($user)) {
            return ResponseController::getResponse(null, 400, "User Not Found");
        }

        if (!Hash::check($request->get('password'), $user->password)) {
            return ResponseController::getResponse(null, 400, "Invalid Credentials");
        }

        $requiredChangePassword = false;
        if ($user->status == UserStatusEnum::Deleted) {
            return ResponseController::getResponse(null, 400, "Your account is deactivated, please contact your Admin");
        } else if ($user->status == UserStatusEnum::Pending) {
            $requiredChangePassword = true;
        }

        $payloadable = [
            'guid' => $user->guid,
            'name' => $user->name,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'role_guid' => $user->role_guid,
            'role_name' => $user->role_name,
        ];

        $token = JWTAuth::fromUser($user, $payloadable);

        $respone = [
            "access_token" => $token,
            "required_change_password" => $requiredChangePassword,
        ];

        return ResponseController::getResponse($respone, 200, 'Login Success');
    }

    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    // public function register(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required|string|between:2,100',
    //         'email' => 'required|string|email|max:100|unique:users',
    //         'password' => 'required|string|confirmed|min:6',
    //     ]);
    //     if ($validator->fails()) {
    //         return response()->json($validator->errors()->toJson(), 400);
    //     }
    //     $user = User::create(array_merge(
    //         $validator->validated(),
    //         ['password' => bcrypt($request->password)]
    //     ));
    //     return response()->json([
    //         'message' => 'User successfully registered',
    //         'user' => $user
    //     ], 201);
    // }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function logout()
    {
        try {
            auth('api')->logout();
            return ResponseController::getResponse(null, 200, 'User successfully signed out');
        } catch (Exception $e) {
            return ResponseController::getResponse(null, 401, 'Unauthorized');
        }
        return ResponseController::getResponse(null, 401, 'Unauthorized');
    }
}
