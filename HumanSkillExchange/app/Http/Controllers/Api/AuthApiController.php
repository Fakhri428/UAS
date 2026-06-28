<?php

namespace App\Http\Controllers\Api;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthApiController extends BaseApiController
{
    public function register(Request $request): JsonResponse
    {
        $data = $this->validated($request, [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:120', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($data instanceof JsonResponse) {
            return $data;
        }

        $freePlan = Plan::where('name', 'Gratis')->first();

        $user = User::create([
            'name' => $data['name'],
            'email' => strtolower($data['email']),
            'password' => Hash::make($data['password']),
            'role' => 'user',
            'plan_id' => $freePlan?->id,
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        return $this->success('Register berhasil', [
            'user' => $this->userPayload($user->load('plan')),
            'token_type' => 'Bearer',
            'access_token' => $token,
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $data = $this->validated($request, [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if ($data instanceof JsonResponse) {
            return $data;
        }

        $user = User::with('plan')->where('email', strtolower($data['email']))->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return $this->fail('Email atau password salah', 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return $this->success('Login berhasil', [
            'user' => $this->userPayload($user),
            'token_type' => 'Bearer',
            'access_token' => $token,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return $this->success('Logout berhasil');
    }

    public function me(Request $request): JsonResponse
    {
        return $this->success('Data user login berhasil diambil', $this->userPayload($request->user()->load('plan')));
    }

    private function userPayload(User $user): array
    {
        return [
            'id' => (int) $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'plan' => $user->plan?->name,
        ];
    }
}
