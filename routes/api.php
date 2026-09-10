<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\ComplaintController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\AIJobController;
use App\Http\Controllers\Api\FieldOfficerController;
use App\Http\Controllers\API\AuthController;
use App\Http\Middleware\AIWorkerAuth;


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

});


// ---------------------------------------------------------
// Authentication APIs
// ---------------------------------------------------------

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

});