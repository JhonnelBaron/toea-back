<?php

use App\Http\Controllers\Admin\CriteriaController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');


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
    //CRITERIA A
    Route::post('/add', [CriteriaController::class, 'store']);
    Route::post('/update/{id}', [CriteriaController::class, 'edit']);
    Route::get('/get-all', [CriteriaController::class, 'getAll']);
    Route::get('/get-criteria/{id}', [CriteriaController::class, 'getCriteriaId']);
    Route::post('/tags-a/{id}', [CriteriaController::class, 'TagsA']);
    Route::post('delete-a/{id}', [CriteriaController::class, 'deleteA']);

    //CRITERIA B
    Route::post('/add-b', [CriteriaController::class, 'storeB']);
    Route::post('/update-b/{id}', [CriteriaController::class, 'editB']);
    Route::get('/get-all-b', [CriteriaController::class, 'getAllB']);
    Route::get('/get-criteria-b/{id}', [CriteriaController::class, 'getBCriteriaId']);
    Route::post('/tags-b/{id}', [CriteriaController::class, 'TagsB']);
    Route::post('delete-b/{id}', [CriteriaController::class, 'deleteB']);
    

    //CRITERIA C
    Route::post('/add-c', [CriteriaController::class, 'storeC']);
    Route::post('/update-c/{id}', [CriteriaController::class, 'editC']);
    Route::get('/get-all-c', [CriteriaController::class, 'getAllC']);
    Route::get('/get-criteria-c/{id}', [CriteriaController::class, 'getCCriteriaId']);
    Route::post('/tags-c/{id}', [CriteriaController::class, 'TagsC']);
    Route::post('delete-c/{id}', [CriteriaController::class, 'deleteC']);

    //CRITERIA D
    Route::post('/add-d', [CriteriaController::class, 'storeD']);
    Route::post('/update-d/{id}', [CriteriaController::class, 'editD']);
    Route::get('/get-all-d', [CriteriaController::class, 'getAllD']);
    Route::get('/get-criteria-d/{id}', [CriteriaController::class, 'getDCriteriaId']);
    Route::post('/tags-d/{id}', [CriteriaController::class, 'TagsD']);
    Route::post('delete-d/{id}', [CriteriaController::class, 'deleteD']);

    //CRITERIA E
    Route::post('/add-e', [CriteriaController::class, 'storeE']);
    Route::post('/update-e/{id}', [CriteriaController::class, 'editE']);
    Route::get('/get-all-e', [CriteriaController::class, 'getAllE']);
    Route::get('/get-criteria-e/{id}', [CriteriaController::class, 'getECriteriaId']);
    Route::post('/tags-e/{id}', [CriteriaController::class, 'TagsE']);
    Route::post('delete-e/{id}', [CriteriaController::class, 'deleteE']);
});