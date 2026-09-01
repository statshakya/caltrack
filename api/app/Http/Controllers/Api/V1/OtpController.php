<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;




class OtpController extends Controller
{
    public function sendOtp(Request $request){
        $request->validate([
            'email'=>'required|email',
        ]);

        $existing = User::where('email',$request->email)->first();

        if($existing){
            return response()->json([
                'message'=>'Email already registered .Please login'
            ],422);
        }

        $otp = str_pad(random_int(0,999999),6,'0',STR_PAD_LEFT);

        OTP::where('email',$request->email)->delete();

        $otpRecord = Otp::create([
            'email'=>$request->email,
            'otp'=>$otp,
            'expires_at'=>now()->addMinutes(10),
        ]);


        try{
            mail::raw("");

        }catch(\Exception $e){
            return response()->json([
                'status'=>'error',
                'message'=>'OTP generated',
                'dev_otp'=>$otp,
                'expires_in_minutes'=>10,
            ]);
        }

    }
}
