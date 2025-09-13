<?php

namespace App\Http\Controllers;

use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PlannerController extends Controller
{
    public function slashView()
    {
        return inertia('Home',props: [
            'message' => 'Welcome to Event Planner Platform'
        ]);
    }
    public function login()
    {
        return inertia("Auth/Login");
    }
    public function register()
    {
        return inertia("Auth/Register");
    }
    public function events()
    {
        return inertia("Planner/Events");
    }
    public function Account()
    {
        return inertia("Planner/Account");
    }
    public function BookingRequest(){
        return inertia("Planner/BookingRequest");
    }
}
