<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;


class AuthController extends Controller
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
             Mail::raw("Your CalTrack verification code is: {$otp}\n\nThis code expires in 10 minutes.", function ($message) use ($request) {
                $message->to($request->email)
                       ->subject('CalTrack - Email Verification Code');
            });
               return response()->json([
                'message' => 'OTP sent successfully to your email.',
                'expires_in_minutes' => 10,
            ]);

        }catch(\Exception $e){
            return response()->json([
                'status'=>'error',
                'message'=>'OTP generated',
                'dev_otp'=>$otp,
                'expires_in_minutes'=>10,
            ]);
        }

    }

    public function verifyOtp(Request $request){
        $request->validate([
            'email'=>'required|email',
            'otp'=>'required|size:6'
            
        ]);

        $otpRecord= Otp::where('email',$request->email)->
        where('otp',$request->otp)->
        where('is_verified',false)->
        where('expires_at', '>', now())
        ->first();

        if(!$otpRecord){
            return response()->json([
                'message'=>'Invalid or expired OTP'
            ],422);
        }


        $otpRecord->update(['is_verified'=>true]);

        return response()->json([
            'message' => 'OTP verified successfully. You can now create your password.',
        ]);
        }

    public function completeRegistration(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string',
            'height' => 'nullable|numeric|min:0',
            'weight' => 'nullable|numeric|min:0',
            'age' => 'nullable|integer|min:0|max:150',
            'gender' => 'nullable|in:male,female,other',
            'activity_level' => 'nullable|in:sedentary,light,moderate,active,very_active',
            'goal' => 'nullable|in:lose_weight,maintain_weight,gain_weight',
        ]);

        $otpVerified= Otp::where('email',$request->email)->where('is_verified',true)->exists();
        if (!$otpVerified) {
            return response()->json([
                'message' => 'Email not verified. Please verify OTP first.',
            ], 422);
        }
                // Check if user already exists
        $existingUser = User::where('email', $request->email)->first();
        if ($existingUser) {
            return response()->json([
                'message' => 'Email already registered.',
            ], 422);
        }
        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone ?? null,
            'height' => $request->height ?? null,
            'weight' => $request->weight ?? null,
            'age' => $request->age ?? null,
            'gender' => $request->gender ?? null,
            'activity_level' => $request->activity_level ?? 'sedentary',
            'goal' => $request->goal ?? 'maintain_weight',
        ]);
        // Delete OTP after successful registration
        Otp::where('email', $request->email)->delete();

        $token = $user->createToken('caltrack-token')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful',
            'user' => $user,
            'token' => $token,
        ], 201);

    }
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string',
           'height_cm' => 'nullable|numeric|min:0',  // CHANGE: height to height_cm
        'weight_kg' => 'nullable|numeric|min:0',  // CHANGE: weight to weight_kg
        'age' => 'nullable|integer|min:0|max:150',
            'gender' => 'nullable|in:male,female,other',
            'activity_level' => 'nullable|in:sedentary,light,moderate,active,very_active',
            'goal' => 'nullable|in:lose_weight,maintain_weight,gain_weight',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
         'height_cm' => $request->height_cm ?? null,  // CHANGE
        'weight_kg' => $request->weight_kg ?? null,  // CHANGE
           'age' => $validated['age'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'activity_level' => $validated['activity_level'] ?? 'sedentary',
            'goal' => $validated['goal'] ?? 'maintain_weight',
        ]);

        $token = $user->createToken('caltrack-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('caltrack-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}