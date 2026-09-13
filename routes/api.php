<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\ComplaintController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\AIJobController;
use App\Http\Controllers\Api\FieldOfficerController;
use App\Http\Controllers\API\AuthController;
use App\Http\Middleware\AIWorkerAuth;
use App\Http\Controllers\Api\ResolutionProofController;
use App\Http\Controllers\Api\ResolutionAIJobController;
use App\Http\Controllers\Api\AdminDashboardController;
use App\Http\Controllers\Api\CitizenRewardController;

// ---------------------------------------------------------
// Citizen APIs
// ---------------------------------------------------------

Route::post('/complaints', [
    ComplaintController::class,
    'store',
]);

Route::get('/complaints/{complaintNumber}', [
    ComplaintController::class,
    'show',
]);

Route::post('/location/detect-ward', [
    LocationController::class,
    'detectWard',
]);


// ---------------------------------------------------------
// Local AI Worker APIs
// ---------------------------------------------------------

Route::middleware(AIWorkerAuth::class)->group(function () {

    // Existing complaint AI
    Route::get('/ai/jobs/pending', [
        AIJobController::class,
        'pending',
    ]);

    Route::get('/ai/jobs/{analysisId}/media/{mediaId}', [
        AIJobController::class,
        'media',
    ]);

    Route::post('/ai/jobs/{analysisId}/result', [
        AIJobController::class,
        'result',
    ]);

    // Resolution AI
    Route::get('/ai/resolution-jobs/pending', [
        ResolutionAIJobController::class,
        'pending',
    ]);

    Route::get('/ai/resolution-jobs/{proofId}/media', [
        ResolutionAIJobController::class,
        'media',
    ]);

    Route::post('/ai/resolution-jobs/{proofId}/result', [
        ResolutionAIJobController::class,
        'result',
    ]);
});


// ---------------------------------------------------------
// Authentication APIs
// ---------------------------------------------------------

Route::post('/auth/register', [
    AuthController::class,
    'register',
]);

Route::post('/auth/login', [
    AuthController::class,
    'login',
]);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/auth/me', [
        AuthController::class,
        'me',
    ]);

    Route::post('/auth/logout', [
        AuthController::class,
        'logout',
    ]);

    // Citizen complaint tracking
    Route::get('/citizen/complaints', [
        \App\Http\Controllers\Api\CitizenComplaintController::class,
        'index',
    ]);

    // Citizen resolution verification
    Route::post('/citizen/complaints/{complaintNumber}/verify-resolved', [
        \App\Http\Controllers\Api\CitizenComplaintController::class,
        'verifyResolved',
    ]);

    // Citizen reopen
    Route::post('/citizen/complaints/{complaintNumber}/reopen', [
        \App\Http\Controllers\Api\CitizenComplaintController::class,
        'reopen',
    ]);

    Route::get('/citizen/rewards', [
        CitizenRewardController::class,
        'rewards',
    ]);

    Route::get('/citizen/certificates', [
        CitizenRewardController::class,
        'certificates',
    ]);
});


// ---------------------------------------------------------
// Admin Command Center APIs
// ---------------------------------------------------------

// Route::middleware([
//     'auth:sanctum',
//     'role:admin',
// ])->prefix('admin')->group(function () {

//     Route::get('/dashboard', [
//         AdminDashboardController::class,
//         'dashboard',
//     ]);

// });
// ---------------------------------------------------------
// Admin Command Center APIs
// ---------------------------------------------------------

Route::middleware([
    'auth:sanctum',
    'role:admin',
])->prefix('admin')->group(function () {

    Route::get('/dashboard', [
        AdminDashboardController::class,
        'dashboard',
    ]);

    Route::get('/incidents', [
        AdminDashboardController::class,
        'incidents',
    ]);

    Route::get('/incidents/{incident}', [
        AdminDashboardController::class,
        'incidentDetail',
    ]);

    Route::get('/sla', [
        AdminDashboardController::class,
        'sla',
    ]);

    Route::get('/map', [
        AdminDashboardController::class,
        'map',
    ]);

});


// ---------------------------------------------------------
// Field Officer APIs
// ---------------------------------------------------------

Route::middleware([
    'auth:sanctum',
    'role:ward_officer',
])->group(function () {

    Route::get('/field/dashboard', [
        FieldOfficerController::class,
        'dashboard',
    ]);

    Route::post('/field/complaints/{complaintId}/status', [
        FieldOfficerController::class,
        'updateStatus',
    ]);

    Route::post('/field/complaints/{complaintId}/resolution-proof', [
        ResolutionProofController::class,
        'store',
    ]);

});