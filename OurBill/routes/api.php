<?php

use App\Http\Controllers\api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/send-otp', [AuthController::class, 'sendOtp'])->name('send-otp');
Route::post('/check-username', [AuthController::class, 'checkUsername'])->name('username.check');