<?php

namespace App\Http\Middleware;

use App\Classes\ApiResponse;
use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class JwtMiddleware
{
    public function handle($request, Closure $next)
    {
        try {
            if (!$token = JWTAuth::parseToken()->getToken()) {
                return ApiResponse::error(null, 'Token not provided', Response::HTTP_UNAUTHORIZED);
            }

            $user = JWTAuth::authenticate($token);

            if (!$user) {
                return ApiResponse::error(null, 'User not found', Response::HTTP_UNAUTHORIZED);
            }
        } catch (TokenExpiredException $e) {
            return ApiResponse::error(null, 'Token expired', Response::HTTP_UNAUTHORIZED);
        } catch (TokenInvalidException $e) {
            return ApiResponse::error(null, 'Token invalid', Response::HTTP_UNAUTHORIZED);
        } catch (JWTException $e) {
            return ApiResponse::error(null, 'Token error: ' . $e->getMessage(), Response::HTTP_UNAUTHORIZED);
        } catch (\Exception $e) {
            return ApiResponse::error(null, 'Authorization error', Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
