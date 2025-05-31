<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/register/client', [AuthController::class, 'registerClient']);
Route::post('/register/business', [AuthController::class, 'registerBusiness']);
Route::post('/login', [AuthController::class, 'login']);