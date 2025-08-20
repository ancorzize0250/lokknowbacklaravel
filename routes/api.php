<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BusinessController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\TestController;

Route::post('/register/client', [AuthController::class, 'registerClient']);
Route::post('/register/business', [AuthController::class, 'registerBusiness']);
Route::post('/information/business', [BusinessController::class, 'editBusiness']);
Route::post('/login', [AuthController::class, 'login']);

Route::match(['get', 'post'], '/test', [TestController::class, 'handleTestRequest']);
Route::post('/register_question', [TestController::class, 'registerQuestions']);