<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

abstract class BaseApiController extends Controller
{
    protected function success(string $message, mixed $data = null, int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    protected function fail(string $message, int $statusCode = 400, mixed $data = null): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    protected function validated(Request $request, array $rules): array|JsonResponse
    {
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->fail('Validasi gagal', 422, $validator->errors());
        }

        return $validator->validated();
    }

    protected function enforcePlanLimit($user, string $modelClass, string $limitColumn, string $featureName): ?JsonResponse
    {
        $plan = $user->plan;

        if (! $plan || $plan->{$limitColumn} === null) {
            return null;
        }

        $total = $modelClass::where('user_id', $user->id)->count();

        if ($total >= (int) $plan->{$limitColumn}) {
            return $this->fail("Batas {$featureName} untuk paket Anda sudah tercapai", 403);
        }

        return null;
    }
}
