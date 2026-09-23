@props([
    'label' => 'Sibol',
    'showText' => true,
])

<span {{ $attributes->class('inline-flex items-center gap-3') }}>
    <span class="inline-flex size-11 items-center justify-center rounded-2xl bg-[#286446] shadow-sm" aria-hidden="true">
        <svg class="size-7" viewBox="0 0 48 48" fill="none" role="img">
            <circle cx="24" cy="32" r="7" fill="#F4C95D" />
            <path d="M24 31C24 20.2 18.3 12.9 9 10.8C8.7 20 14.5 27.2 24 31Z" fill="#E9F3EE" />
            <path d="M24 31C24.5 19.5 30.7 12.7 40 11.4C39.5 20.8 33.6 27.5 24 31Z" fill="#D7E5D8" />
            <path d="M24 32V39" stroke="#E9F3EE" stroke-width="3" stroke-linecap="round" />
        </svg>
    </span>

    @if ($showText)
        <span class="grid gap-0.5">
            <span class="text-lg font-semibold leading-none tracking-tight text-[#2E2A24]">{{ $label }}</span>
            <span class="text-xs font-medium leading-none text-[#6D665C]">School & family</span>
        </span>
    @endif
</span>
