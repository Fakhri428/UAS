<?php

namespace App\Http\Controllers\Api;

use App\Models\Portfolio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PortfolioController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        if ($request->filled('id')) {
            return $this->show((int) $request->query('id'));
        }

        $userId = $request->integer('user_id') ?: $request->user()->id;
        $portfolios = Portfolio::query()
            ->where('user_id', $userId)
            ->latest('id')
            ->get();

        return $this->success('Data portfolio berhasil diambil', $portfolios);
    }

    public function forUser(int $userId): JsonResponse
    {
        $portfolios = Portfolio::query()
            ->where('user_id', $userId)
            ->latest('id')
            ->get();

        return $this->success('Portfolio user berhasil diambil', $portfolios);
    }

    public function show(int $id): JsonResponse
    {
        $portfolio = Portfolio::find($id);

        return $portfolio
            ? $this->success('Detail portfolio berhasil diambil', $portfolio)
            : $this->fail('Portfolio tidak ditemukan', 404);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validated($request, [
            'title' => ['required', 'string', 'max:160'],
            'description' => ['required', 'string'],
            'file_url' => ['nullable', 'url', 'max:255'],
            'project_url' => ['nullable', 'url', 'max:255'],
        ]);

        if ($data instanceof JsonResponse) {
            return $data;
        }

        $portfolio = Portfolio::create($data + ['user_id' => $request->user()->id]);

        return $this->success('Portfolio berhasil ditambahkan', $portfolio, 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $portfolio = Portfolio::where('id', $id)->where('user_id', $request->user()->id)->first();

        if (! $portfolio) {
            return $this->fail('Portfolio tidak ditemukan atau bukan milik Anda', 404);
        }

        $data = $this->validated($request, [
            'title' => ['required', 'string', 'max:160'],
            'description' => ['required', 'string'],
            'file_url' => ['nullable', 'url', 'max:255'],
            'project_url' => ['nullable', 'url', 'max:255'],
        ]);

        if ($data instanceof JsonResponse) {
            return $data;
        }

        $portfolio->update($data);

        return $this->success('Portfolio berhasil diperbarui', $portfolio);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $portfolio = Portfolio::where('id', $id)->where('user_id', $request->user()->id)->first();

        if (! $portfolio) {
            return $this->fail('Portfolio tidak ditemukan atau bukan milik Anda', 404);
        }

        $portfolio->delete();

        return $this->success('Portfolio berhasil dihapus', ['id' => $id]);
    }
}