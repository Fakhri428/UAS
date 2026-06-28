<button {{ $attributes->merge(['type' => 'button', 'class' => 'nb-btn bg-brand-pink text-ink']) }}>
    {{ $slot }}
</button>
