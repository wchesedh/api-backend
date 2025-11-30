<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            // Create Sanctum token for API authentication
            $token = $user->createToken('api-token')->plainTextToken;
            
            return response()->json([
                'success' => true, 
                'user' => $user,
                'token' => $token
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Invalid credentials'], 401);
    }
}
