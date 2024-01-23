<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PremiumController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/test', function() {
    return [
        'foo' => 'test',
        'year' => 2024,
    ];
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('/auth')->group(function() {
    Route::post('/register', [RegisterController::class, 'registerUser']);
    Route::post('/login', [LoginController::class, 'loginUser']);
    Route::middleware('auth:sanctum')->get('/logout', [LogoutController::class, 'logoutUser']);
});

Route::middleware('auth:sanctum','ability:premium')->get('/premium-access', [PremiumController::class, 'access']);
Route::middleware('auth:sanctum')->get('/set-premium', [PremiumController::class, 'setPremium']);