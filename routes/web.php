<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlannerController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


//use DemoController Which you have created. Make sure when you use in that controller you only use methods that return Inertia responses or views.
Route::get('/', [PlannerController::class, 'slashView']);
Route::get("/login", [PlannerController::class, 'login']);
Route::get("/register", [PlannerController::class, 'register']);
Route::get("/planner/dashboard", [PlannerController::class, 'dashboard']);
Route::get("/planner/events", [PlannerController::class, 'events']);
Route::get("/planner/account", [PlannerController::class, 'account']);

// Never make this mistake!!! You are in web.php, not api.php. So do not use AuthController methods here. AuthController is typically used in api.php for API authentication. This was the first mistake here. 
//For web routes, you typically use controllers that return views or Inertia responses, not API controllers like AuthController which doesn't return any type of view.
// Route::post('/register', [AuthController::class, 'register']);
// Route::post('/login', [AuthController::class, 'login']);
// Route::get('/dashboard', [AuthController::class, 'dashboard']);

