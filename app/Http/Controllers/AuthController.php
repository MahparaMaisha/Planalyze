<?php

namespace App\Http\Controllers;
use App\Models\Planner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'role_id' => 'required|exists:roles,id',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:4|confirmed',
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'role_id' => $validated['role_id'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            if($validated['role_id'] === 1){
                Planner::create([
                        'user_id' => $user->id,
                        'name' => $validated['name'],
                        'bio' => '',
                    ]);
            }

            return response()->json([
                'user' => $user,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
          'message' => 'Registration failed',
          'error' => $e->getMessage(),
            ], 400);
        }
    }

    // User login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = User::where('email', $request->email)->first();
        $token = $user->createToken('token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    // Get current user (uses Auth::user())
    public function dashboard(Request $request)
    {
        return response()->json([
            'user' => Auth::user(),
            'message' => 'Authenticated user data'
        ]);
    }

    // User logout
    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete();
            return response()->json(['message' => 'Logged out successfully']);
        } catch (\Exception $e) {
            return response()->json([
          'message' => 'Logout failed',
          'error' => $e->getMessage(),
            ], 400);
        }
    }
    public function editProfile(Request $request){
        $user = Auth::user();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'bio' => 'nullable|string|max:1000',
        ]);

        if($user->role_id === 1){
            $planner = $user->planner;
            $planner->bio = $validated['bio'];
            $planner->save();
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        $user->save();

        return response()->json(['message' => 'Profile updated successfully', 'user' => $user]);
    }
    public function deleteAccount(Request $request)
    {
        $user = Auth::user();
        $user->tokens()->delete();
        $user->delete();

        return response()->json(['message' => 'Profile deleted successfully']);
    }
}
