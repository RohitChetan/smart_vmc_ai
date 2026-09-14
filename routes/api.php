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
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\AdminMasterController;

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



/*
|--------------------------------------------------------------------------
| Admin Master Management
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'role:admin'])
    ->prefix('admin/master')
    ->group(function () {

        // Overview
        Route::get('/overview', [
            AdminMasterController::class,
            'overview'
        ]);

        // Departments
        Route::get('/departments', [
            AdminMasterController::class,
            'departments'
        ]);

        Route::post('/departments', [
            AdminMasterController::class,
            'storeDepartment'
        ]);

        Route::put('/departments/{department}', [
            AdminMasterController::class,
            'updateDepartment'
        ]);

        Route::delete('/departments/{department}', [
            AdminMasterController::class,
            'destroyDepartment'
        ]);

        // Categories
        Route::get('/categories', [
            AdminMasterController::class,
            'categories'
        ]);

        Route::post('/categories', [
            AdminMasterController::class,
            'storeCategory'
        ]);

        Route::put('/categories/{category}', [
            AdminMasterController::class,
            'updateCategory'
        ]);

        Route::delete('/categories/{category}', [
            AdminMasterController::class,
            'destroyCategory'
        ]);

        // Wards
        Route::get('/wards', [
            AdminMasterController::class,
            'wards'
        ]);

        Route::post('/wards', [
            AdminMasterController::class,
            'storeWard'
        ]);

        Route::put('/wards/{ward}', [
            AdminMasterController::class,
            'updateWard'
        ]);

        Route::delete('/wards/{ward}', [
            AdminMasterController::class,
            'destroyWard'
        ]);

        // Users
        Route::get('/users', [
            AdminMasterController::class,
            'users'
        ]);

        Route::post('/users', [
            AdminMasterController::class,
            'storeUser'
        ]);

        Route::put('/users/{user}', [
            AdminMasterController::class,
            'updateUser'
        ]);

        Route::delete('/users/{user}', [
            AdminMasterController::class,
            'destroyUser'
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

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'read']);
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll']);
});