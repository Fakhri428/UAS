<?php

namespace App\Http\Controllers\Api;

use App\Models\Offer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OfferController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        if ($request->filled('id')) {
            return $this->show((int) $request->query('id'));
        }

        $userId = $request->integer('user_id') ?: $request->user()->id;
        $offers = Offer::where('user_id', $userId)
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = '%'.$request->query('search').'%';
                $query->where(fn ($builder) => $builder
                    ->where('title', 'like', $search)
                    ->orWhere('category', 'like', $search)
                    ->orWhere('description', 'like', $search)
                    ->orWhere('exchange_expectation', 'like', $search));
            })
            ->latest('id')
            ->get();

        return $this->success('Data offer berhasil diambil', $offers);
    }

    public function show(int $id): JsonResponse
    {
        $offer = Offer::find($id);

        return $offer
            ? $this->success('Detail offer berhasil diambil', $offer)
            : $this->fail('Offer tidak ditemukan', 404);
    }

    public function store(Request $request): JsonResponse
    {
        $limit = $this->enforcePlanLimit($request->user()->load('plan'), Offer::class, 'max_offers', 'offer');

        if ($limit) {
            return $limit;
        }

        $data = $this->validated($request, $this->rules());

        if ($data instanceof JsonResponse) {
            return $data;
        }

        $offer = Offer::create($data + ['user_id' => $request->user()->id]);

        return $this->success('Offer berhasil dibuat', $offer, 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $offer = Offer::where('id', $id)->where('user_id', $request->user()->id)->first();

        if (! $offer) {
            return $this->fail('Offer tidak ditemukan atau bukan milik Anda', 404);
        }

        $data = $this->validated($request, $this->rules());

        if ($data instanceof JsonResponse) {
            return $data;
        }

        $offer->update($data);

        return $this->success('Offer berhasil diperbarui', $offer);
    }

    public function updateFromQuery(Request $request): JsonResponse
    {
        $id = $request->integer('id');

        return $id
            ? $this->update($request, $id)
            : $this->fail('Parameter id wajib dikirim', 400);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $offer = Offer::where('id', $id)->where('user_id', $request->user()->id)->first();

        if (! $offer) {
            return $this->fail('Offer tidak ditemukan atau bukan milik Anda', 404);
        }

        $offer->delete();

        return $this->success('Offer berhasil dihapus', ['id' => $id]);
    }

    public function destroyFromQuery(Request $request): JsonResponse
    {
        $id = $request->integer('id');

        return $id
            ? $this->destroy($request, $id)
            : $this->fail('Parameter id wajib dikirim', 400);
    }

    private function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:180'],
            'type' => ['required', 'in:skill,time,experience,mentoring,project,collaboration'],
            'category' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string'],
            'exchange_expectation' => ['required', 'string'],
            'available_duration' => ['nullable', 'string', 'max:120'],
        ];
    }
}
