<?php

namespace App\Http\Controllers\Auth;

use App\Classes\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    /**
     * Authenticate user and return JWT token
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return ApiResponse::error([], 'Invalid credentials', 401);
            }

            return ApiResponse::success([
                'token' => $token,
                'user' => JWTAuth::user()
            ], 'Login successful');
        } catch (JWTException $e) {
            return ApiResponse::error([], 'Could not create token', 500);
        }
    }

    /**
     * Logout user and invalidate token
     */
    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return ApiResponse::success([], 'Logout successful');
        } catch (JWTException $e) {
            return ApiResponse::error([], 'Could not logout', 500);
        }
    }
}
