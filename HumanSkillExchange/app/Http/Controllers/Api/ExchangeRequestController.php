<?php

namespace App\Http\Controllers\Api;

use App\Models\ExchangeRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExchangeRequestController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        if ($request->filled('id')) {
            return $this->show($request, (int) $request->query('id'));
        }

        $userId = $request->user()->id;
        $requests = ExchangeRequest::query()
            ->select([
                'exchange_requests.*',
                'from_user.name as from_user_name',
                'to_user.name as to_user_name',
                'offers.title as offer_title',
                'needs.title as need_title',
            ])
            ->join('users as from_user', 'from_user.id', '=', 'exchange_requests.from_user_id')
            ->join('users as to_user', 'to_user.id', '=', 'exchange_requests.to_user_id')
            ->leftJoin('offers', 'offers.id', '=', 'exchange_requests.offer_id')
            ->leftJoin('needs', 'needs.id', '=', 'exchange_requests.need_id')
            ->where(fn ($query) => $query->where('from_user_id', $userId)->orWhere('to_user_id', $userId))
            ->latest('exchange_requests.id')
            ->get();

        return $this->success('Data exchange request berhasil diambil', $requests);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $exchange = $this->findForUser($id, $request->user()->id);

        return $exchange
            ? $this->success('Detail exchange request berhasil diambil', $exchange)
            : $this->fail('Exchange request tidak ditemukan atau bukan milik Anda', 404);
    }

    public function store(Request $request): JsonResponse
    {
        $limit = $this->enforceMonthlyExchangeLimit($request->user()->load('plan'));

        if ($limit) {
            return $limit;
        }

        $data = $this->validated($request, [
            'to_user_id' => ['required', 'integer', 'exists:users,id'],
            'offer_id' => ['required', 'integer', 'exists:offers,id'],
            'need_id' => ['required', 'integer', 'exists:needs,id'],
            'message' => ['required', 'string'],
        ]);

        if ($data instanceof JsonResponse) {
            return $data;
        }

        if ((int) $data['to_user_id'] === $request->user()->id) {
            return $this->fail('Tidak bisa mengirim exchange request ke diri sendiri', 422);
        }

        $exchange = ExchangeRequest::create($data + [
            'from_user_id' => $request->user()->id,
            'status' => 'pending',
        ]);

        return $this->success('Exchange request berhasil dikirim', $exchange, 201);
    }

    public function patch(Request $request, int $id): JsonResponse
    {
        return $request->query('action') === 'complete'
            ? $this->complete($request, $id)
            : $this->updateStatus($request, $id);
    }

    public function patchFromQuery(Request $request): JsonResponse
    {
        $id = $request->integer('id');

        return $id
            ? $this->patch($request, $id)
            : $this->fail('Parameter id wajib dikirim', 400);
    }

    public function status(Request $request, int $id): JsonResponse
    {
        return $this->updateStatus($request, $id);
    }

    public function markComplete(Request $request, int $id): JsonResponse
    {
        return $this->complete($request, $id);
    }

    private function updateStatus(Request $request, int $id): JsonResponse
    {
        $exchange = $this->findForUser($id, $request->user()->id);

        if (! $exchange) {
            return $this->fail('Exchange request tidak ditemukan atau bukan milik Anda', 404);
        }

        $data = $this->validated($request, [
            'status' => ['required', 'in:accepted,rejected,in_progress,cancelled'],
        ]);

        if ($data instanceof JsonResponse) {
            return $data;
        }

        $newStatus = $data['status'];

        if (in_array($newStatus, ['accepted', 'rejected'], true) && (int) $exchange->to_user_id !== $request->user()->id) {
            return $this->fail('Hanya penerima request yang boleh accept atau reject', 403);
        }

        if ($exchange->status !== 'pending' && in_array($newStatus, ['accepted', 'rejected'], true)) {
            return $this->fail('Request yang sudah diproses tidak bisa accept atau reject ulang', 422);
        }

        if ($newStatus === 'in_progress' && ! in_array($exchange->status, ['accepted', 'in_progress'], true)) {
            return $this->fail('Exchange harus accepted sebelum menjadi in_progress', 422);
        }

        $exchange->update(['status' => $newStatus]);

        return $this->success('Status exchange request berhasil diperbarui', [
            'id' => $exchange->id,
            'status' => $newStatus,
        ]);
    }

    private function complete(Request $request, int $id): JsonResponse
    {
        $exchange = $this->findForUser($id, $request->user()->id);

        if (! $exchange) {
            return $this->fail('Exchange request tidak ditemukan atau bukan milik Anda', 404);
        }

        if (! in_array($exchange->status, ['accepted', 'in_progress', 'completed'], true)) {
            return $this->fail('Exchange belum bisa dikonfirmasi selesai', 422);
        }

        $fromDone = $exchange->completed_by_from_user;
        $toDone = $exchange->completed_by_to_user;

        if ((int) $exchange->from_user_id === $request->user()->id) {
            $fromDone = true;
        }

        if ((int) $exchange->to_user_id === $request->user()->id) {
            $toDone = true;
        }

        $status = $fromDone && $toDone ? 'completed' : 'in_progress';
        $exchange->update([
            'completed_by_from_user' => $fromDone,
            'completed_by_to_user' => $toDone,
            'status' => $status,
        ]);

        return $this->success($status === 'completed' ? 'Exchange selesai' : 'Menunggu konfirmasi dari user lain', [
            'id' => $exchange->id,
            'completed_by_me' => true,
            'completed_by_partner' => (int) $exchange->from_user_id === $request->user()->id ? $toDone : $fromDone,
            'status' => $status,
        ]);
    }

    private function findForUser(int $id, int $userId): ?ExchangeRequest
    {
        return ExchangeRequest::where('id', $id)
            ->where(fn ($query) => $query->where('from_user_id', $userId)->orWhere('to_user_id', $userId))
            ->first();
    }

    private function enforceMonthlyExchangeLimit(User $user): ?JsonResponse
    {
        $plan = $user->plan;

        if (! $plan || $plan->max_exchange_requests === null) {
            return null;
        }

        $total = ExchangeRequest::where('from_user_id', $user->id)
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();

        if ($total >= (int) $plan->max_exchange_requests) {
            return $this->fail('Batas exchange request bulanan untuk paket Anda sudah tercapai', 403);
        }

        return null;
    }
}
