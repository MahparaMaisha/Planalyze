<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientController extends Controller
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
        return inertia("Client/Dashboard");
    }
    public function events()
    {
        return inertia("Client/Events");
    }
    public function Account()
    {
        return inertia("Client/Account");
    }
}
