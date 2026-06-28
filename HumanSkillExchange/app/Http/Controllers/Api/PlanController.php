<?php

namespace App\Http\Controllers\Api;

use App\Models\Plan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlanController extends BaseApiController
{
    public function index(): JsonResponse
    {
        return $this->success('Data paket langganan berhasil diambil', Plan::orderBy('price')->get());
    }

    public function current(Request $request): JsonResponse
    {
        return $this->success('Paket aktif berhasil diambil', $request->user()->load('plan')->plan);
    }

    public function subscribe(Request $request): JsonResponse
    {
        $data = $this->validated($request, [
            'plan_id' => ['required', 'integer', 'exists:plans,id'],
        ]);

        if ($data instanceof JsonResponse) {
            return $data;
        }

        $plan = Plan::find($data['plan_id']);
        $request->user()->forceFill(['plan_id' => $plan->id])->save();

        return $this->success('Paket berhasil diaktifkan', $plan);
    }

    public function cancel(Request $request): JsonResponse
    {
        $plan = Plan::where('name', 'Gratis')->first();

        if (! $plan) {
            return $this->fail('Paket Gratis tidak ditemukan', 404);
        }

        $request->user()->forceFill(['plan_id' => $plan->id])->save();

        return $this->success('Subscription dibatalkan. Paket kembali ke Gratis', $plan);
    }
}
