<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordMail;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{

    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'username' => 'required|string',
        ]);

        $code = rand(100000, 999999);
        $user = User::where('email', $request->email)->where('username', $request->username)->first();
        if($user){
            $otp = Otp::create([
                'user_id' => $user->id,
                'otp' => $code,
                'expires_at' => now()->addMinutes(5),
            ]);
            Mail::to($user->email)->send(new ResetPasswordMail($code));
            return response()->json([
                'message' => 'OTP sent successfully!',
                'success' => true,
            ]);
        }
        else{
            return response()->json([
                'message' => 'User not found!',
                'success' => false,
            ], 404);
        }
    }

    public function checkUsername(Request $request){
        $request->validate([
            'username' => 'required|string|alpha_num|max:255',
        ]);

        $isAvailable = true;

        if(User::where('username', '=', $request->username)->exists()){
            $isAvailable = false;
        }

        return response()->json([
            'success' => $isAvailable,
        ]);
    }
}