<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TestController extends Controller
{
    public function testDb(): JsonResponse
    {
        try {
            \DB::connection()->getPdo();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Database connected successfully!',
                'database' => \DB::connection()->getDatabaseName(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Could not connect to the database.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
        public function health(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => 'API is working!',
            'timestamp' => now(),
        ]);
    }

    public function createTestUser(Request $request): JsonResponse
    {
        try{
            $user = User::create([
                'name'=> $request->input('name','test user'),
                'email' => $request->input('email', 'test' . time() . '@example.com'),
                'password' => Hash::make($request->input('password', 'password123')),
                'role' => 'user',
                'is_active' => true,

            ]);
            return response()->json([
                'status'=> 'success',
                'message'=> 'user created sucessfully',
                'user' => $user 
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' =>'error',
                'message' => 'Failed to create user',
                'error' => $e->getMessage()
            ],500);

        }
    }

    public function getUsers(): JsonResponse
    {
        try{
            $users= User::all();

            return response()->json([
                'status'=> 'success',
                'message'=>'user found',
                'users' =>$users
            ]);

        }catch(\Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => 'user not found',
                'error' => $e->getMessage()
            ],500);
        }
    }
}
