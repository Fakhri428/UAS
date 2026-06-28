@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-black text-sm text-ink']) }}>
    {{ $value ?? $slot }}
</label>
