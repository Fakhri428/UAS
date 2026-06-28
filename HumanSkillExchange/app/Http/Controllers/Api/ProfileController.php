<?php

namespace App\Http\Controllers\Api;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends BaseApiController
{
    public function show(Request $request): JsonResponse
    {
        $userId = $request->integer('user_id') ?: $request->user()->id;
        $user = User::with(['profile', 'plan'])->find($userId);

        if (! $user) {
            return $this->fail('Profil user tidak ditemukan', 404);
        }

        return $this->success('Profil berhasil diambil', [
            'user_id' => (int) $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'plan_name' => $user->plan?->name,
            'bio' => $user->profile?->bio,
            'location' => $user->profile?->location,
            'work_mode' => $user->profile?->work_mode,
            'available_time' => $user->profile?->available_time,
            'portfolio_url' => $user->profile?->portfolio_url,
            'social_url' => $user->profile?->social_url,
        ]);
    }

    public function publicProfile(int $id): JsonResponse
    {
        $user = User::with(['profile', 'plan'])->find($id);

        if (! $user) {
            return $this->fail('Profil user tidak ditemukan', 404);
        }

        // Profil publik: tanpa email demi privasi.
        return $this->success('Profil publik berhasil diambil', [
            'user_id' => (int) $user->id,
            'name' => $user->name,
            'role' => $user->role,
            'is_verified' => (bool) $user->is_verified,
            'plan_name' => $user->plan?->name,
            'bio' => $user->profile?->bio,
            'location' => $user->profile?->location,
            'work_mode' => $user->profile?->work_mode,
            'available_time' => $user->profile?->available_time,
            'portfolio_url' => $user->profile?->portfolio_url,
            'social_url' => $user->profile?->social_url,
        ]);
    }

    public function save(Request $request): JsonResponse
    {
        $data = $this->validated($request, [
            'bio' => ['required', 'string'],
            'location' => ['required', 'string', 'max:120'],
            'work_mode' => ['required', 'in:online,offline,hybrid'],
            'available_time' => ['required', 'string', 'max:120'],
            'portfolio_url' => ['nullable', 'url', 'max:255'],
            'social_url' => ['nullable', 'url', 'max:255'],
        ]);

        if ($data instanceof JsonResponse) {
            return $data;
        }

        $profile = Profile::updateOrCreate(
            ['user_id' => $request->user()->id],
            $data
        );

        return $this->success('Profil berhasil disimpan', $profile);
    }
}
