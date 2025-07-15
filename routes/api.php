<?php

use App\Http\Controllers\Admin\CriteriaController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('logout', [AuthController::class, 'logout']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
Route::post('/password/email', [AuthController::class, 'sendResetLinkEmail']);
route::post('/password/reset', [AuthController::class, 'resetPassword']);
Route::get('/password/reset', [AuthController::class, 'showResetForm']);
Route::get('/password/reset/{token}', function ($token) {
    // Redirect to frontend route with the token and email as query parameters
    $frontendUrl = config('app.frontend_url') . '/update-password?token=' . $token . '&email=' . request('email');
    return redirect($frontendUrl);
})->name('password.reset');


Route::middleware(['auth:api'])->group(function () {
    Route::post('/add', [CriteriaController::class, 'store']);
    Route::post('/update/{id}', [CriteriaController::class, 'edit']);
    Route::post('/create-requirement-a', [CriteriaController::class, 'createRequirementA']);
    Route::get('/get-all', [CriteriaController::class, 'getAll']);
    
});