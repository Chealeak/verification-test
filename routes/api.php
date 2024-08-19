<?php

use App\Http\Controllers\Auth\SanctumAuthController;
use App\Http\Controllers\VerificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register',[SanctumAuthController::class, 'register']);
Route::post('login',[SanctumAuthController::class, 'login']);
Route::post('logout',[SanctumAuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('verification', [VerificationController::class, 'store']);
});
