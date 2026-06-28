<button {{ $attributes->merge(['type' => 'submit', 'class' => 'nb-btn nb-btn-primary']) }}>
    {{ $slot }}
</button>
