<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

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

Route::post('/login', [AuthController::class, 'login']);

// IP Info endpoint (public, but can be rate limited)
Route::get('/ip-info', [App\Http\Controllers\Api\IpInfoController::class, 'getIpInfo']);
Route::get('/ip-info/{ip}', [App\Http\Controllers\Api\IpInfoController::class, 'getIpInfo']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::get('/ip-history', [App\Http\Controllers\Api\IpHistoryController::class, 'index']);
    Route::post('/ip-history', [App\Http\Controllers\Api\IpHistoryController::class, 'store']);
    Route::delete('/ip-history', [App\Http\Controllers\Api\IpHistoryController::class, 'destroy']);
    
    Route::post('/logout', function (Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    });
});
