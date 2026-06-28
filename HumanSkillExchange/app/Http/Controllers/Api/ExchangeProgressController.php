<?php

namespace App\Http\Controllers\Api;

use App\Models\ExchangeProgress;
use App\Models\ExchangeRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExchangeProgressController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $exchangeRequestId = $request->integer('exchange_request_id');

        if (! $exchangeRequestId) {
            return $this->fail('Parameter exchange_request_id wajib dikirim', 400);
        }

        if (! $this->isParticipant($exchangeRequestId, $request->user()->id)) {
            return $this->fail('Exchange request tidak ditemukan atau bukan milik Anda', 404);
        }

        $progress = ExchangeProgress::query()
            ->select('exchange_progress.*', 'users.name as user_name')
            ->join('users', 'users.id', '=', 'exchange_progress.user_id')
            ->where('exchange_request_id', $exchangeRequestId)
            ->latest('exchange_progress.id')
            ->get();

        return $this->success('Progress exchange berhasil diambil', $progress);
    }

    public function store(Request $request): JsonResponse
    {
        $exchangeRequestId = $request->integer('exchange_request_id');

        if (! $exchangeRequestId) {
            return $this->fail('Parameter exchange_request_id wajib dikirim', 400);
        }

        if (! $this->isParticipant($exchangeRequestId, $request->user()->id)) {
            return $this->fail('Exchange request tidak ditemukan atau bukan milik Anda', 404);
        }

        $data = $this->validated($request, [
            'progress_note' => ['required', 'string'],
            'file_url' => ['nullable', 'url', 'max:255'],
        ]);

        if ($data instanceof JsonResponse) {
            return $data;
        }

        $progress = ExchangeProgress::create($data + [
            'exchange_request_id' => $exchangeRequestId,
            'user_id' => $request->user()->id,
        ]);

        return $this->success('Progress exchange berhasil ditambahkan', $progress, 201);
    }

    public function indexForRequest(Request $request, int $exchangeRequestId): JsonResponse
    {
        $request->merge(['exchange_request_id' => $exchangeRequestId]);

        return $this->index($request);
    }

    public function storeForRequest(Request $request, int $exchangeRequestId): JsonResponse
    {
        $request->merge(['exchange_request_id' => $exchangeRequestId]);

        return $this->store($request);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $progress = ExchangeProgress::where('id', $id)->where('user_id', $request->user()->id)->first();

        if (! $progress) {
            return $this->fail('Progress tidak ditemukan atau bukan milik Anda', 404);
        }

        $data = $this->validated($request, [
            'progress_note' => ['required', 'string'],
            'file_url' => ['nullable', 'url', 'max:255'],
        ]);

        if ($data instanceof JsonResponse) {
            return $data;
        }

        $progress->update($data);

        return $this->success('Progress exchange berhasil diperbarui', $progress);
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
        $progress = ExchangeProgress::where('id', $id)->where('user_id', $request->user()->id)->first();

        if (! $progress) {
            return $this->fail('Progress tidak ditemukan atau bukan milik Anda', 404);
        }

        $progress->delete();

        return $this->success('Progress exchange berhasil dihapus', ['id' => $id]);
    }

    public function destroyFromQuery(Request $request): JsonResponse
    {
        $id = $request->integer('id');

        return $id
            ? $this->destroy($request, $id)
            : $this->fail('Parameter id wajib dikirim', 400);
    }

    private function isParticipant(int $exchangeRequestId, int $userId): bool
    {
        return ExchangeRequest::where('id', $exchangeRequestId)
            ->where(fn ($query) => $query->where('from_user_id', $userId)->orWhere('to_user_id', $userId))
            ->exists();
    }
}
