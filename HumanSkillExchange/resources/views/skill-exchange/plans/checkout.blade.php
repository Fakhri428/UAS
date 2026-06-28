@php
    $limit = fn ($v) => is_null($v) ? 'Tanpa batas' : $v;
    $snapUrl = config('services.midtrans.is_production')
        ? 'https://app.midtrans.com/snap/snap.js'
        : 'https://app.sandbox.midtrans.com/snap/snap.js';
@endphp

@component('layouts.public', ['title' => 'Pembayaran '.$plan->name.' - Koukan'])
    <main class="mx-auto max-w-2xl px-4 py-10 sm:px-6 lg:px-8">
        <a href="{{ route('plans.index') }}" class="text-sm font-bold text-ink/60 hover:text-ink">&larr; Kembali ke daftar plan</a>

        <section class="nb-card mt-4 p-6">
            <span class="nb-badge bg-brand-purple text-white">Checkout</span>
            <h1 class="mt-4 text-3xl font-black text-ink">Plan {{ $plan->name }}</h1>

            <div class="mt-6 rounded-lg border-2 border-ink bg-brand-sky/30 p-5">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-ink">Total pembayaran</span>
                    <span class="text-2xl font-black text-ink">Rp{{ number_format($plan->price, 0, ',', '.') }}</span>
                </div>
                <p class="mt-1 text-xs font-semibold text-ink/60">Order ID: KOUKAN-PLAN-{{ $transaction->id }}</p>
            </div>

            <ul class="mt-6 space-y-2 text-sm font-semibold text-ink">
                <li class="flex items-center gap-2"><span class="font-black">✓</span> {{ $limit($plan->max_skills) }} skill</li>
                <li class="flex items-center gap-2"><span class="font-black">✓</span> {{ $limit($plan->max_needs) }} Koukan Need</li>
                <li class="flex items-center gap-2"><span class="font-black">✓</span> {{ $limit($plan->max_offers) }} Koukan Offer</li>
                <li class="flex items-center gap-2"><span class="font-black">✓</span> {{ $limit($plan->max_exchange_requests) }} request pertukaran / bulan</li>
            </ul>

            <button id="pay-button" type="button" class="nb-btn nb-btn-primary mt-8 w-full">Bayar Sekarang</button>
            <p class="mt-3 text-center text-xs font-semibold text-ink/50">Pembayaran diproses aman oleh Midtrans.</p>
        </section>
    </main>

    <script src="{{ $snapUrl }}" data-client-key="{{ $clientKey }}"></script>
    <script>
        document.getElementById('pay-button').addEventListener('click', function () {
            var finishUrl = '{{ route('plans.finish') }}';
            window.snap.pay('{{ $snapToken }}', {
                onSuccess: function (result) {
                    window.location.href = finishUrl + '?order_id=' + encodeURIComponent(result.order_id);
                },
                onPending: function (result) {
                    window.location.href = finishUrl + '?order_id=' + encodeURIComponent(result.order_id);
                },
                onError: function () {
                    alert('Pembayaran gagal. Silakan coba lagi.');
                },
                onClose: function () {
                    // pengguna menutup popup tanpa menyelesaikan pembayaran
                },
            });
        });
    </script>
@endcomponent
