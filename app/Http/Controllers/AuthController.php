<?php

namespace App\Http\Controllers;

use App\Helpers\Response;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $service,
        private readonly Response $response,
    ) {}

    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        return $this->response->success($this->service->register($data), 'Registered successfully.', 201);
    }

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        return $this->response->success($this->service->login($data['email'], $data['password']), 'Logged in successfully.');
    }

    public function logout(Request $request): JsonResponse
    {
        $this->service->logout($request->user());

        return $this->response->success(null, 'Logged out successfully.');
    }
}
