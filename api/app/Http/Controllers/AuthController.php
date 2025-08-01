<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (!Auth::attempt($credentials))
            return response()->json(['message' => 'Invalid credentials'], 401);

        $user = auth()->user();

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'data' => [
                'user' => $user,
                'access_token' => $token,
            ],
        ]);
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }
    public function check(Request $request)
    {
        return response()->json(['status' => $request->user()->isAuthenticated()]);
    }
}
