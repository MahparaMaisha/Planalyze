<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// This is the api.php file, so you can use AuthController methods here for API authentication. You will see in AuthCrontroller it is different from DemoController. The return statement is a JSON response, not an Inertia response. Use postman to test these API routes.

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/dashboard', [AuthController::class, 'dashboard'])->middleware('auth:sanctum');
   