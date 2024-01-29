<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PremiumController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Project\ProjectStoreController;
use App\Http\Controllers\Interval\IntervalOpenController;
use App\Http\Controllers\Interval\IntervalShowController;
use App\Http\Controllers\Customer\CustomerStoreController;
use App\Http\Controllers\Customer\CustomerUpdateController;
use App\Http\Controllers\Interval\UpdateIntervalCategoryController;

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

Route::middleware('auth:sanctum')->get('/user', [UserController::class, 'getUser']);

Route::prefix('/auth')->group(function() {
    Route::post('/register', [RegisterController::class, 'registerUser']);
    Route::post('/login', [LoginController::class, 'loginUser']);
    Route::middleware('auth:sanctum')->get('/logout', [LogoutController::class, 'logoutUser']);
});

Route::middleware('auth:sanctum','ability:premium')->get('/premium-access', [PremiumController::class, 'access']);
Route::middleware('auth:sanctum')->get('/set-premium', [PremiumController::class, 'setPremium']);

Route::middleware('auth:sanctum')->apiResource('/categories', CategoryController::class);

Route::middleware('auth:sanctum')->prefix('/customers')->group(function() {
    Route::post('/', CustomerStoreController::class);
    Route::put('/{id}', CustomerUpdateController::class);
});

Route::middleware('auth:sanctum')->prefix('/projects')->group(function() {
    Route::post('/', ProjectStoreController::class);
});

Route::middleware('auth:sanctum')->prefix('/intervals')->group(function() {
    Route::post('/', IntervalOpenController::class);
    Route::post('/{id}/attach-category', [UpdateIntervalCategoryController::class, 'attachCategory']);
    Route::get('/{id}', [IntervalShowController::class, 'show']);
});