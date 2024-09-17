<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{

    public function index(Request $request)
    {
        return $request->user;
    }

    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation errors as JSON
            return response()->json([
                'errors' => $e->errors(),
            ], 422); // 422 Unprocessable Entity
        }
    
        if (!auth()->attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
        
        $user = auth()->user();
        $token = $user->createToken($user->email)->plainTextToken;
    
        return response()->json(['token' => $token]);
    }

    public function register(Request $request)
    {
        try {
            // Perform validation
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation errors as JSON
            return response()->json([
                'errors' => $e->errors(),
            ], 422); // 422 Unprocessable Entity
        }

        // Create the user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Return a success response with the generated token
        return response()->json([
            'user' => $user
        ], 201);
    }
}
