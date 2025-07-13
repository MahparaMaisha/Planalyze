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
});


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get("/logout", [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/dashboard', [AuthController::class, 'dashboard'])->middleware('auth:sanctum');
   