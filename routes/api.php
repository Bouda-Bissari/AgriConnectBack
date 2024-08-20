<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CandidatureController;
use App\Http\Controllers\DetailController;
use App\Http\Controllers\DetailServiceController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\ReportController;
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

// Route pour changer le statut d'une candidature
Route::put('/candidatures/{id}/status', [CandidatureController::class, 'changeStatus']);

// Route pour compter le nombre de candidatures pour un service spécifique
Route::get('/candidatures/count/{serviceId}', [CandidatureController::class, 'countCandidaturesByService']);

// Route pour récupérer toutes les candidatures avec un statut "pending"
Route::get('/candidatures/pending', [CandidatureController::class, 'getPendingCandidatures']);

// Route pour filtrer les candidatures selon différents critères
Route::get('/candidatures/filter', [CandidatureController::class, 'filterCandidatures']);


//pire tests
Route::get('/candidatures/service-owner/{serviceOwnerId}', [CandidatureController::class, 'getCandidaturesByServiceOwner']);

Route::apiResource('/services', ServiceController::class);

// Route pour lister tous les services (index)
Route::get('/services', [ServiceController::class, 'index']);

// Route pour afficher un service spécifique (show)
Route::get('/services/{service}', [ServiceController::class, 'show']);


Route::get('/detailservice/{id}', [DetailServiceController::class,'show']);
Route::delete('/profile/{userId}/image', [ProfilController::class, 'deleteImage']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });


//services
// Route pour créer un nouveau service (store)
Route::post('/services', [ServiceController::class, 'store']);


// Route pour mettre à jour un service spécifique (update)
Route::put('/services/{service}', [ServiceController::class, 'update']);

    // Route pour obtenir le nombre de candidatures associées à un service
Route::get('/services/{service}/count-applications', [ServiceController::class, 'countApplications']);


// Route pour obtenir les candidatures associées à un service
Route::get('/services/{service}/get-applications', [ServiceController::class, 'getApplications']);

    Route::apiResource('/reports', ReportController::class);


    Route::post('/update-phone-number', [UserController::class, 'updatePhoneNumber']);
    Route::get('/{userId}/services', [ServiceController::class, 'userServices']);

    Route::apiResource('/candidature', CandidatureController::class);


    Route::post('/update-user-role', [UserController::class, 'updateUserRole']);

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('/users', UserController::class);
    Route::apiResource('/details', DetailController::class);

    Route::middleware(['role:admin'])->group(function () {
        // Route::apiResource('/roles', RoleController::class);
    });


});
