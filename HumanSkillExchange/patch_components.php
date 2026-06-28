<?php
// button.blade.php
$f = 'resources/views/components/button.blade.php';
$c = file_get_contents($f);
$c = preg_replace('/class="inline-flex items-center[^"]*"/', 'class="nb-btn nb-btn-primary {{ $attributes->get(\'class\') }}"', $c);
file_put_contents($f, $c);

// input.blade.php
$f = 'resources/views/components/input.blade.php';
$c = file_get_contents($f);
$c = preg_replace('/class="border-gray-300[^"]*"/', 'class="nb-input {{ $attributes->get(\'class\') }}"', $c);
file_put_contents($f, $c);

// label.blade.php
$f = 'resources/views/components/label.blade.php';
$c = file_get_contents($f);
$c = str_replace('class="block font-medium text-sm text-gray-700"', 'class="block font-bold text-sm text-ink"', $c);
file_put_contents($f, $c);

// authentication-card.blade.php
$f = 'resources/views/components/authentication-card.blade.php';
$c = file_get_contents($f);
$c = str_replace('bg-gray-100', 'bg-paper', $c);
$c = str_replace('w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg', 'nb-card w-full sm:max-w-md mt-6 p-6', $c);
file_put_contents($f, $c);

echo "COMPONENTS PATCHED\n";
