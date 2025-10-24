<?php

use App\Http\Controllers\Admin\CriteriaController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\RopotiController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Executive\DashboardController;
use App\Http\Controllers\Executive\EvaluationController;
use App\Services\Admin\RopotiService;
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
    Route::post('/execute-a/{id}', [CriteriaController::class, 'ExecuteConfigA']);
    Route::post('delete-a/{id}', [CriteriaController::class, 'deleteA']);

    //CRITERIA B
    Route::post('/add-b', [CriteriaController::class, 'storeB']);
    Route::post('/update-b/{id}', [CriteriaController::class, 'editB']);
    Route::get('/get-all-b', [CriteriaController::class, 'getAllB']);
    Route::get('/get-criteria-b/{id}', [CriteriaController::class, 'getBCriteriaId']);
    Route::post('/tags-b/{id}', [CriteriaController::class, 'TagsB']);
    Route::post('/execute-b/{id}', [CriteriaController::class, 'ExecuteConfigB']);
    Route::post('delete-b/{id}', [CriteriaController::class, 'deleteB']);
    

    //CRITERIA C
    Route::post('/add-c', [CriteriaController::class, 'storeC']);
    Route::post('/update-c/{id}', [CriteriaController::class, 'editC']);
    Route::get('/get-all-c', [CriteriaController::class, 'getAllC']);
    Route::get('/get-criteria-c/{id}', [CriteriaController::class, 'getCCriteriaId']);
    Route::post('/tags-c/{id}', [CriteriaController::class, 'TagsC']);
    Route::post('/execute-c/{id}', [CriteriaController::class, 'ExecuteConfigC']);
    Route::post('delete-c/{id}', [CriteriaController::class, 'deleteC']);

    //CRITERIA D
    Route::post('/add-d', [CriteriaController::class, 'storeD']);
    Route::post('/update-d/{id}', [CriteriaController::class, 'editD']);
    Route::get('/get-all-d', [CriteriaController::class, 'getAllD']);
    Route::get('/get-criteria-d/{id}', [CriteriaController::class, 'getDCriteriaId']);
    Route::post('/tags-d/{id}', [CriteriaController::class, 'TagsD']);
    Route::post('/execute-d/{id}', [CriteriaController::class, 'ExecuteConfigD']);
    Route::post('delete-d/{id}', [CriteriaController::class, 'deleteD']);

    //CRITERIA E
    Route::post('/add-e', [CriteriaController::class, 'storeE']);
    Route::post('/update-e/{id}', [CriteriaController::class, 'editE']);
    Route::get('/get-all-e', [CriteriaController::class, 'getAllE']);
    Route::get('/get-criteria-e/{id}', [CriteriaController::class, 'getECriteriaId']);
    Route::post('/tags-e/{id}', [CriteriaController::class, 'TagsE']);
    Route::post('/execute-e/{id}', [CriteriaController::class, 'ExecuteConfigE']);
    Route::post('delete-e/{id}', [CriteriaController::class, 'deleteE']);

    //Regions
    Route::get('/regions', [RopotiController::class, 'getRO']);
    Route::post('/regions', [RopotiController::class, 'addRO']);
    Route::post('/regions/{id}', [RopotiController::class, 'editRO']);
    Route::delete('/regions/{id}', [RopotiController::class, 'deleteRO']);

    //Province
    Route::get('/provinces', [RopotiController::class, 'getPO']);
    Route::post('/provinces', [RopotiController::class, 'addPO']);
    Route::post('/provinces/{id}', [RopotiController::class, 'editPO']);
    Route::delete('/provinces/{id}', [RopotiController::class, 'deletePO']);

    // Institutions
    Route::get('/institutions', [RopotiController::class, 'getTI']);
    Route::post('/institutions', [RopotiController::class, 'addTI']);
    Route::post('/institutions/{id}', [RopotiController::class, 'editTI']);
    Route::delete('/institutions/{id}', [RopotiController::class, 'deleteTI']);

    // Users
    Route::get('/users', [UserController::class, 'get']);
    Route::post('/users', [UserController::class, 'add']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::post('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'delete']);

    //ADMIN DASHBOARD
    Route::get('/dashboard/nominees', [AdminDashboardController::class, 'getCounts']);
    Route::get('/dashboard/users', [AdminDashboardController::class, 'getUsers']);
    Route::get('/users/{id}/ratings', [AdminDashboardController::class, 'getRatings']);
    Route::get('/all/criteria/a', [AdminDashboardController::class, 'getBroCriteriaA']);
    Route::get('/all/criteria/b', [AdminDashboardController::class, 'getBroCriteriaB']);
    Route::get('/all/criteria/c', [AdminDashboardController::class, 'getBroCriteriaC']);
    Route::get('/all/criteria/d', [AdminDashboardController::class, 'getBroCriteriaD']);
    Route::get('/all/criteria/e', [AdminDashboardController::class, 'getBroCriteriaE']);
    Route::get('/scores/a/{nomineeId}', [AdminDashboardController::class, 'getScoresA']);
    Route::get('/scores/b/{nomineeId}', [AdminDashboardController::class, 'getScoresB']);
    Route::get('/scores/c/{nomineeId}', [AdminDashboardController::class, 'getScoresC']);
    Route::get('/scores/d/{nomineeId}', [AdminDashboardController::class, 'getScoresD']);
    Route::get('/scores/e/{nomineeId}', [AdminDashboardController::class, 'getScoresE']);
    Route::get('/bro-summaries', [AdminDashboardController::class, 'get']);
    Route::post('/bro-summaries/endorse/{nomineeId}', [AdminDashboardController::class, 'endorseExternals']);
    //Evaluation for Executive
    Route::get('/criterias', [EvaluationController::class, 'index']);
    Route::get('/criteria/a', [EvaluationController::class, 'getACriteria']);
    Route::get('/criteria/b', [EvaluationController::class, 'getBCriteria']);
    Route::get('/criteria/c', [EvaluationController::class, 'getCCriteria']);
    Route::get('/criteria/d', [EvaluationController::class, 'getDCriteria']);
    Route::get('/criteria/e', [EvaluationController::class, 'getECriteria']);
    Route::get('/nominee/{id}', [EvaluationController::class, 'get']);
    Route::post('/score', [EvaluationController::class, 'store']);
    Route::get('/score/{id}', [EvaluationController::class, 'showScore']);
    Route::get('/scores/nominee/{id}', [EvaluationController::class, 'getScores']);
    Route::post('/scores-done/nominee/{id}', [EvaluationController::class, 'markAsDone']);
    Route::get('/scores-done/nominee/{id}', [EvaluationController::class, 'getStatus']);
    Route::post('/scores/aggregate', [EvaluationController::class, 'completionRate']);
    //Dashboard for Executive
    Route::get('/dashboard/bro-nominees', [DashboardController::class, 'getBroNominees']);
    Route::get('/dashboard/score-rating', [DashboardController::class, 'getScoreRating']);
});