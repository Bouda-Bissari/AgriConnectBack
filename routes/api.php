<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CandidatureController;
use App\Http\Controllers\DetailController;
use App\Http\Controllers\DetailServiceController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SignController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Routes publiques
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::apiResource('/roles', RoleController::class);



Route::post('/send-otp', [SignController::class, 'sendOtp']);
Route::post('/verify-otp', [SignController::class, 'verifyOtp']);
Route::apiResource('/profile', ProfilController::class);
Route::apiResource('/candidature', CandidatureController::class);
//test
Route::get('/candidatures/user/{userId}', [CandidatureController::class, 'getCandidaturesByUser']);
Route::get('/candidatures/service/{serviceId}', [CandidatureController::class, 'getCandidaturesByService']);
//pire tests
Route::get('/candidatures/service-owner/{serviceOwnerId}', [CandidatureController::class, 'getCandidaturesByServiceOwner']);


Route::get('/detailservice/{id}', [DetailServiceController::class,'show']);
Route::delete('/profile/{userId}/image', [ProfilController::class, 'deleteImage']);
Route::apiResource('/services', ServiceController::class);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/update-phone-number', [UserController::class, 'updatePhoneNumber']);
    Route::get('/{userId}/services', [ServiceController::class, 'userServices']);

    Route::post('/update-user-role', [UserController::class, 'updateUserRole']);

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('/users', UserController::class);
    Route::apiResource('/details', DetailController::class);
 
    Route::middleware(['role:admin'])->group(function () {
        // Route::apiResource('/roles', RoleController::class);
    });

    
});
