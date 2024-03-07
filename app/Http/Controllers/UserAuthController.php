<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserAuthController extends Controller
{
    // REGISTER USER METHOD
    public function register(Request $request)
    {
        // VALIDATES THE DATA SENT BY THE REQUEST
        $registerUserData = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|min:8'
        ]);

        // CREATES A NEW USER AND GENERATES
        $user = User::create([
            'name' => $registerUserData['name'],
            'email' => $registerUserData['email'],
            'password' => Hash::make($registerUserData['password']),
        ]);

        return response()->json([
            'message' => 'User Created ',
            'user' => $user
        ]);
    }

    // LOGIN USER METHOD
    public function login(Request $request)
    {
        $loginUserData = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|min:8'
        ]);

        // CHECKS IF USER EXISTS
        // LOOKS FOR THE USER WITH THE SAME email AS THE loginUserData IN THE DATABASE
        $user = User::where('email', $loginUserData['email'])->first();

        // IS THE USER DOES NOT EXISTS OR PASSWORD IS WRONG, SEND ERROR MESSAGE
        if (!$user || !Hash::check($loginUserData['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid Credentials'
            ], 401);
        }

        //  CREATES A TOKEN AND LOGIN THE USER
        $token = $user->createToken($user->name . '-AuthToken')->plainTextToken;
        return response()->json([
            'access_token' => $token,
        ]);
    }

    // LOGOUT USER METHOD
    public function logout()
    {
        // DELETES THE TOKENS FOR THE CURRENTLY AUTHENTICATED USER
        auth()->user()->tokens()->delete();

        return response()->json([
            "message" => "logged out"
        ]);
    }
}
