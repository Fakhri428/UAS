<?php

namespace App\Http\Controllers\Api;

use App\Models\Need;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NeedController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        if ($request->filled('id')) {
            return $this->show((int) $request->query('id'));
        }

        $userId = $request->integer('user_id') ?: $request->user()->id;
        $needs = Need::where('user_id', $userId)
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = '%'.$request->query('search').'%';
                $query->where(fn ($builder) => $builder
                    ->where('title', 'like', $search)
                    ->orWhere('category', 'like', $search)
                    ->orWhere('description', 'like', $search));
            })
            ->latest('id')
            ->get();

        return $this->success('Data need berhasil diambil', $needs);
    }

    public function show(int $id): JsonResponse
    {
        $need = Need::find($id);

        return $need
            ? $this->success('Detail need berhasil diambil', $need)
            : $this->fail('Need tidak ditemukan', 404);
    }

    public function store(Request $request): JsonResponse
    {
        $limit = $this->enforcePlanLimit($request->user()->load('plan'), Need::class, 'max_needs', 'need');

        if ($limit) {
            return $limit;
        }

        $data = $this->validated($request, [
            'title' => ['required', 'string', 'max:180'],
            'category' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string'],
            'exchange_offer' => ['required', 'string'],
        ]);

        if ($data instanceof JsonResponse) {
            return $data;
        }

        $need = Need::create($data + ['user_id' => $request->user()->id]);

        return $this->success('Need berhasil ditambahkan', $need, 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $need = Need::where('id', $id)->where('user_id', $request->user()->id)->first();

        if (! $need) {
            return $this->fail('Need tidak ditemukan atau bukan milik Anda', 404);
        }

        $data = $this->validated($request, [
            'title' => ['required', 'string', 'max:180'],
            'category' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string'],
            'exchange_offer' => ['required', 'string'],
        ]);

        if ($data instanceof JsonResponse) {
            return $data;
        }

        $need->update($data);

        return $this->success('Need berhasil diperbarui', $need);
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
        $need = Need::where('id', $id)->where('user_id', $request->user()->id)->first();

        if (! $need) {
            return $this->fail('Need tidak ditemukan atau bukan milik Anda', 404);
        }

        $need->delete();

        return $this->success('Need berhasil dihapus', ['id' => $id]);
    }

    public function destroyFromQuery(Request $request): JsonResponse
    {
        $id = $request->integer('id');

        return $id
            ? $this->destroy($request, $id)
            : $this->fail('Parameter id wajib dikirim', 400);
    }
}
