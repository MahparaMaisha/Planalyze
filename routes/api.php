<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// This is the api.php file, so you can use AuthController methods here for API authentication. You will see in AuthCrontroller it is different from DemoController. The return statement is a JSON response, not an Inertia response. Use postman to test these API routes.
Route::middleware("auth:sanctum")->group(function (){
  Route::get('/events',[EventController::class,'index']);
    Route::post('/events',[EventController::class,'store']);
    Route::get('/events/{id}',[EventController::class,'show']);
    Route::put('/events/{id}',[EventController::class,'update']);
    Route::delete('/events/{id}',[EventController::class,'destroy']);
    Route::post('/events/{id}/reviews', [EventController::class, 'storeReview']);
    Route::get('/events/{id}/reviews', [EventController::class, 'getReviews']);
    Route::put('/events/{id}/reviews/{reviewId}', [EventController::class, 'updateReview']);
    Route::delete('/events/{id}/reviews/{reviewId}', [EventController::class, 'deleteReview']);
});


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::put('/edit-profile', [AuthController::class, 'editProfile'])->middleware('auth:sanctum');
Route::delete('/delete-account', [AuthController::class, 'deleteAccount'])->middleware('auth:sanctum');
Route::post("/logout", [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/dashboard', [AuthController::class, 'dashboard'])->middleware('auth:sanctum');
Route::get('/planners', [ClientEventController::class, 'index'])->middleware('auth:sanctum');

//client routes
Route::get('/planner-search', [ClientEventController::class, 'plannerSearch'])->middleware('auth:sanctum');
Route::get('/planners/{id}', [ClientEventController::class, 'getPlanner'])->middleware('auth:sanctum');
Route::post('/leave-review', [ClientEventController::class, 'leaveReview'])->middleware('auth:sanctum');
Route::get('/get-top-rated-planners', [ClientEventController::class, 'getTopRatedAllPlanners'])->middleware('auth:sanctum');