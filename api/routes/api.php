<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\FoodEntryController;
use App\Http\Controllers\Api\V1\NutritionController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\TestController;
use App\Http\Controllers\Api\V1\UserController;




Route::prefix('v1')->group(function () {


    Route::get('/test/health', [TestController::class, 'health']);
    Route::get('/test/db', [TestController::class, 'testDb']);
    Route::post('/test/create-user', [TestController::class, 'createTestUser']);
    Route::get('/test/users', [TestController::class, 'getUsers']);


    // Public routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/register/send-otp', [AuthController::class, 'sendOtp']);
    Route::post('/register/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('/register/complete', [AuthController::class, 'completeRegistration']);
    Route::post('/login', [AuthController::class, 'login']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [UserController::class, 'profile']);
        Route::put('/user', [UserController::class, 'updateProfile']);

        Route::get('/profile', [ProfileController::class, 'show']);
        Route::put('/profile', [ProfileController::class, 'update']);
        Route::patch('/profile', [ProfileController::class, 'update']);

        Route::get('/food-entries',[FoodEntryController::class,'index']);
        Route::post('/food-entries',[FoodEntryController::class,'store']);
        Route::delete('/food-entries/{id}',[FoodEntryController::class,'destroy']);

Route::get('/nutrition/search', [NutritionController::class, 'searchFood']);
    });
});