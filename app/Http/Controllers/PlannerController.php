<?php

namespace App\Http\Controllers;

use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PlannerController extends Controller
{
    public function slashView()
    {
        return Inertia::render('Home');
    }
    public function login()
    {
        return inertia("Auth/Login");
    }
    public function register()
    {
        return inertia("Auth/Register");
    }
    public function dashboard()
    {
        return inertia("Planner/Dashboard");
    }
    public function events()
    {
        return inertia("Planner/Events");
    }
    public function Account()
    {
        return inertia("Planner/Account");
    }
}
