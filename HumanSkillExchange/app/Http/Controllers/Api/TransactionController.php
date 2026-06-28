<?php

namespace App\Http\Controllers\Api;

use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = Transaction::with('user:id,name,email')->latest('id');

        // Admin melihat semua transaksi, user biasa hanya miliknya sendiri.
        if ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        }

        return $this->success('Data transaksi berhasil diambil', $query->get());
    }

    public function show(Request $request, Transaction $transaction): JsonResponse
    {
        if ($request->user()->role !== 'admin' && (int) $transaction->user_id !== $request->user()->id) {
            return $this->fail('Transaksi tidak ditemukan atau bukan milik Anda', 404);
        }

        return $this->success('Detail transaksi berhasil diambil', $transaction->load('user:id,name,email'));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validated($request, [
            'type' => ['required', 'string', 'max:30'],
            'reference_id' => ['nullable', 'integer'],
            'amount' => ['required', 'integer', 'min:0'],
            'platform_fee' => ['nullable', 'integer', 'min:0'],
            'payment_method' => ['nullable', 'string', 'max:80'],
        ]);

        if ($data instanceof JsonResponse) {
            return $data;
        }

        $transaction = Transaction::create($data + [
            'user_id' => $request->user()->id,
            'platform_fee' => $data['platform_fee'] ?? 0,
            'status' => 'pending',
        ]);

        return $this->success('Transaksi berhasil dibuat', $transaction, 201);
    }

    public function confirm(Request $request, Transaction $transaction): JsonResponse
    {
        // Hanya pemilik transaksi atau admin yang boleh mengonfirmasi pembayaran.
        if ($request->user()->role !== 'admin' && (int) $transaction->user_id !== $request->user()->id) {
            return $this->fail('Transaksi tidak ditemukan atau bukan milik Anda', 404);
        }

        if ($transaction->status === 'completed') {
            return $this->fail('Transaksi sudah dikonfirmasi sebelumnya', 422);
        }

        $transaction->update(['status' => 'completed']);

        return $this->success('Transaksi berhasil dikonfirmasi', $transaction);
    }
}
