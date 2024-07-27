<?php

namespace App\Http\Controllers;

// use App\Http\Controllers\Controller;
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\ResponseController;
use App\Models\Otp;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Mailjet\Client;
use Mailjet\Resources;

class OtpController extends Controller
{
    public function generateOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ], MessagesController::messages());

        if ($validator->fails()) {
            return ResponseController::getResponse(null, 422, $validator->errors()->first());
        }

        $user = User::where('email', $request->get('email'))
            ->first();

        if ($user === null) {
            return ResponseController::getResponse(null, 404, 'User Not Found');
        }

        try {
            $otp = random_int(100000, 999999);
        } catch (Exception $e) {
            return ResponseController::getResponse($e->getMessage(), 500, 'Internal Server Error');
        }
        /// INSERT DATA TO DB
        $data = Otp::create([
            'user_id' => $user->id,
            'otp' => $otp,
        ]);

        $firstname = strtok($user->name, " ");
        $mj = new Client(
            env('MAILJET_API_KEY'),
            env('MAILJET_SECRET_KEY'),
            true,
            ['version' => 'v3.1']
        );
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => '2172028@maranatha.ac.id',
                        'Name' => 'Surpay'
                    ],
                    'To' => [
                        [
                            'Email' => $user->email,
                            'Name' => $user->name
                        ]
                    ],
                    'TemplateID' => 5706872,
                    'TemplateLanguage' => true,
                    'Subject' => 'OTP Reset Password',
                    'Variables' => json_decode('{
                        "title": "OTP Reset Password",
                        "firstname": "' . $firstname . '",
                        "content": "Kode OTP untuk reset password anda adalah : ' . $otp . '"
                      }', true)
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);

        if (!$response->success()) {
            return ResponseController::getResponse($response->getData(), $response->getStatus(), $response->getReasonPhrase());
        }

        return ResponseController::getResponse($data, 200, 'Success');
    }

    public function verificationOtp(Request $request): JsonResponse
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'guid' => 'required|string|max:36',
            ], MessagesController::messages());

            if ($validator->fails()) {
                return ResponseController::getResponse(null, 422, $validator->errors()->first());
            }

            // Find the user by GUID
            $user = User::where('guid', $request->get('guid'))->first();

            if ($user === null) {
                return ResponseController::getResponse(null, 404, 'User Not Found');
            }

            // Check if OTP already exists for the user
            $otpObj = Otp::where('user_guid', $request->get('guid'))->first();
            if ($otpObj === null) {
                try {
                    $otp = random_int(100000, 999999);
                } catch (Exception $e) {
                    Log::error('Error generating OTP: ' . $e->getMessage());
                    return ResponseController::getResponse($e->getMessage(), 500, 'Internal Server Error');
                }

                // Insert new OTP into the database
                $data = Otp::create([
                    'user_guid' => $user->guid,
                    'otp' => $otp,
                ]);
            } else {
                $otp = $otpObj->otp;
                $data = $otpObj; // To return the existing OTP object for consistency
            }

            // Prepare the email
            $verificationUrl = 'http://127.0.0.1:8000/verify/' . $user->guid . '/' . $otp;
            $firstname = strtok($user->name, " ");
            $mj = new Client(
                env('MAILJET_API_KEY'),
                env('MAILJET_SECRET_KEY'),
                true,
                ['version' => 'v3.1']
            );
            $body = [
                'Messages' => [
                    [
                        'From' => [
                            'Email' => '2172028@maranatha.ac.id',
                            'Name' => 'Surpay'
                        ],
                        'To' => [
                            [
                                'Email' => $user->email,
                                'Name' => $user->name
                            ]
                        ],
                        'TemplateID' => 5964497,
                        'TemplateLanguage' => true,
                        'Subject' => 'Verify Email',
                        'Variables' => [
                            'title' => 'Email Verification',
                            'firstname' => $firstname,
                            'content' => 'Please click the link below to verify your email address:',
                            'verification_url' => $verificationUrl
                        ]
                    ]
                ]
            ];

            // Send the email
            $response = $mj->post(Resources::$Email, ['body' => $body]);

            if (!$response->success()) {
                Log::error('Error sending email: ' . $response->getReasonPhrase());
                return ResponseController::getResponse($response->getData(), $response->getStatus(), $response->getReasonPhrase());
            }

            return ResponseController::getResponse($data, 200, 'Success');
        } catch (Exception $e) {
            Log::error('An unexpected error occurred: ' . $e->getMessage());
            return ResponseController::getResponse($e->getMessage(), 500, 'An unexpected error occurred');
        }
    }


    public function validateOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|string|min:6',
        ], MessagesController::messages());

        if ($validator->fails()) {
            return ResponseController::getResponse(null, 422, $validator->errors()->first());
        }

        $user = User::where('email', $request->get('email'))
            ->first();

        if ($user === null) {
            return ResponseController::getResponse(null, 404, 'User Not Found');
        }

        $otp = Otp::where('user_guid',  $request->get('guid'))
            ->where('otp', $request->get('otp'))
            ->first();

        if ($otp === null) {
            return ResponseController::getResponse(null, 404, 'Wrong OTP');
        }

        return ResponseController::getResponse(null, 200, 'Success');
    }

    public function checkOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'guid' => 'required|string|max:36',
            'otp' => 'required|string|min:6',
        ], MessagesController::messages());

        if ($validator->fails()) {
            return ResponseController::getResponse(null, 422, $validator->errors()->first());
        }

        $user = User::where('guid', $request->get('guid'))
            ->first();

        if ($user === null) {
            return ResponseController::getResponse(null, 404, 'User Not Found');
        }

        $otp = Otp::where('user_guid',  $request->get('guid'))
            ->where('otp', $request->get('otp'))
            ->first();

        if ($otp === null) {
            return ResponseController::getResponse(null, 404, 'Wrong OTP');
        }
        $otp->delete();
        $user->email_verified_at = Carbon::now();
        $user->save();
        return ResponseController::getResponse(null, 200, 'Success');
    }
}
