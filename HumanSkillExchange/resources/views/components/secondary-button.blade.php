<button {{ $attributes->merge(['type' => 'button', 'class' => 'nb-btn nb-btn-white text-ink']) }}>
    {{ $slot }}
</button>
