<?php

namespace App\Helpers;


use Illuminate\Http\JsonResponse;

class Response {

public function success($data = null, string $message = 'Success', int $status = 200): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'data' => $data,
        ], $status);
    }

public function error($data = null, string $message = 'Error', int $status = 422): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'data' => $data,
        ], $status);
    }


}