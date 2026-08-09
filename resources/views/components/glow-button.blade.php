@props(['href', 'variant' => 'primary'])

<a href="{{ $href }}" {{ $attributes->class(['gs-button gs-focus', 'gs-button-primary' => $variant === 'primary', 'gs-button-secondary' => $variant === 'secondary']) }}>
    {{ $slot }}
</a>
