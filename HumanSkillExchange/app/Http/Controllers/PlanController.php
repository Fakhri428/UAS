<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Midtrans\Config as MidtransConfig;
use Midtrans\Notification as MidtransNotification;
use Midtrans\Snap;
use Midtrans\Transaction as MidtransTransaction;

class PlanController extends Controller
{
    /**
     * Halaman daftar plan + benefit.
     */
    public function index(Request $request): View
    {
        $plans = Plan::orderBy('price')->get();
        $currentPlan = $request->user()?->plan;

        return view('skill-exchange.plans.index', [
            'plans' => $plans,
            'currentPlan' => $currentPlan,
        ]);
    }

    /**
     * Halaman checkout. Plan gratis langsung aktif, selain itu lewat Midtrans Snap.
     */
    public function checkout(Request $request, Plan $plan): View|RedirectResponse
    {
        $user = $request->user();

        // Plan gratis: aktifkan langsung tanpa pembayaran.
        if ((int) $plan->price === 0) {
            $user->forceFill(['plan_id' => $plan->id])->save();

            return redirect()->route('dashboard')
                ->with('status', "Plan {$plan->name} berhasil diaktifkan.");
        }

        // Sudah memakai plan ini.
        if ((int) $user->plan_id === (int) $plan->id) {
            return redirect()->route('plans.index')
                ->with('status', "Kamu sudah berlangganan plan {$plan->name}.");
        }

        $transaction = Transaction::create([
            'user_id' => $user->id,
            'type' => 'plan',
            'reference_id' => $plan->id,
            'amount' => $plan->price,
            'status' => 'pending',
        ]);

        $this->configureMidtrans();

        $snapToken = Snap::getSnapToken([
            'transaction_details' => [
                'order_id' => "KOUKAN-PLAN-{$transaction->id}",
                'gross_amount' => (int) $plan->price,
            ],
            'item_details' => [[
                'id' => "plan-{$plan->id}",
                'price' => (int) $plan->price,
                'quantity' => 1,
                'name' => "Plan {$plan->name}",
            ]],
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
            ],
        ]);

        return view('skill-exchange.plans.checkout', [
            'plan' => $plan,
            'transaction' => $transaction,
            'snapToken' => $snapToken,
            'clientKey' => config('services.midtrans.client_key'),
        ]);
    }

    /**
     * Webhook notifikasi Midtrans (tanpa CSRF). Mengaktifkan plan saat pembayaran lunas.
     */
    public function callback(Request $request)
    {
        $this->configureMidtrans();

        try {
            $notif = new MidtransNotification();
        } catch (\Throwable $e) {
            Log::warning('Midtrans callback gagal diparse: '.$e->getMessage());

            return response('Invalid notification', 400);
        }

        $orderId = $notif->order_id ?? '';
        $transactionId = (int) str_replace('KOUKAN-PLAN-', '', $orderId);
        $transaction = Transaction::find($transactionId);

        if (! $transaction || $transaction->type !== 'plan') {
            return response('Transaction not found', 404);
        }

        $this->applyTransactionStatus($transaction, $notif->transaction_status, $notif->payment_type ?? null);

        return response('OK', 200);
    }

    /**
     * Halaman balik setelah pembayaran (onSuccess/onPending dari Snap).
     * Mengecek status langsung ke API Midtrans lalu mengaktifkan plan —
     * sehingga tidak bergantung pada webhook saat demo lokal.
     */
    public function finish(Request $request): RedirectResponse
    {
        $orderId = (string) $request->query('order_id');
        $transactionId = (int) str_replace('KOUKAN-PLAN-', '', $orderId);
        $transaction = Transaction::where('id', $transactionId)
            ->where('user_id', $request->user()->id)
            ->where('type', 'plan')
            ->first();

        if (! $transaction) {
            return redirect()->route('plans.index')->with('status', 'Transaksi tidak ditemukan.');
        }

        $this->configureMidtrans();

        try {
            $result = MidtransTransaction::status($orderId);
            $status = is_object($result) ? ($result->transaction_status ?? null) : ($result['transaction_status'] ?? null);
            $method = is_object($result) ? ($result->payment_type ?? null) : ($result['payment_type'] ?? null);
            $this->applyTransactionStatus($transaction, $status, $method);
        } catch (\Throwable $e) {
            Log::warning('Cek status Midtrans gagal: '.$e->getMessage());

            return redirect()->route('dashboard')->with('status', 'Pembayaran sedang diproses. Status akan diperbarui otomatis.');
        }

        $transaction->refresh();

        $message = match ($transaction->status) {
            'completed' => 'Pembayaran berhasil. Plan kamu sudah aktif!',
            'failed' => 'Pembayaran gagal atau dibatalkan.',
            default => 'Pembayaran sedang menunggu konfirmasi.',
        };

        return redirect()->route('dashboard')->with('status', $message);
    }

    /**
     * Terapkan status transaksi Midtrans ke record lokal + aktifkan plan bila lunas.
     */
    private function applyTransactionStatus(Transaction $transaction, ?string $status, ?string $paymentMethod): void
    {
        if (in_array($status, ['settlement', 'capture'], true)) {
            $transaction->update([
                'status' => 'completed',
                'payment_method' => $paymentMethod,
            ]);

            // Aktifkan plan untuk user.
            $transaction->user?->forceFill(['plan_id' => $transaction->reference_id])->save();
        } elseif (in_array($status, ['expire', 'cancel', 'deny'], true)) {
            $transaction->update([
                'status' => 'failed',
                'payment_method' => $paymentMethod,
            ]);
        } else {
            // pending / lainnya
            $transaction->update(['payment_method' => $paymentMethod]);
        }
    }

    private function configureMidtrans(): void
    {
        MidtransConfig::$serverKey = config('services.midtrans.server_key');
        MidtransConfig::$isProduction = (bool) config('services.midtrans.is_production');
        MidtransConfig::$isSanitized = true;
        MidtransConfig::$is3ds = true;
    }
}
