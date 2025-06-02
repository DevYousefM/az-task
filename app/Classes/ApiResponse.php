<?php

namespace App\Classes;

class ApiResponse
{
    public static function success($data = null, $message = "Success", $code = 200)
    {
        return response()->json(
            [
                'status' => true,
                'message' => $message,
                'data' => $data,
            ],
            $code
        );
    }

    public static function error($data = null, $message = "Error", $code = 400)
    {
        return response()->json(
            [
                'status' => false,
                'message' => $message,
                'data' => $data,
            ],
            $code
        );
    }
}
