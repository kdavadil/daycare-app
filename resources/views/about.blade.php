<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#286446">
    <title>About Sibol · School & family</title>
    <link rel="icon" href="{{ asset('sibol-icon.svg') }}" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#F8F7F2] font-sans antialiased">
    <main class="mx-auto max-w-lg px-5 py-10">
        <nav class="mb-6 flex items-center justify-between text-sm">
            <a href="{{ route('home') }}" class="rounded-2xl focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#286446]">
                <x-brand-mark />
            </a>
            <a href="{{ route('home') }}" class="rounded-xl border border-[#E6DFD2] bg-white px-4 py-2 font-semibold text-[#286446] underline-offset-4 hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#286446]">Back home</a>
        </nav>

        <article class="overflow-hidden rounded-[2rem] border border-[#E6DFD2] bg-white shadow-sm">
            <div class="bg-[#F8F7F2] p-8">
                <p class="text-sm font-semibold tracking-widest text-[#286446] uppercase">About Sibol</p>
                <h1 class="mt-5 text-3xl font-semibold tracking-tight text-[#2E2A24]">Built from everyday school moments.</h1>
                <p class="mt-4 text-base leading-relaxed text-[#6D665C]">Sibol started as a simple idea: make preschool communication feel warmer, clearer, and easier for families in the Philippines.</p>
            </div>

            <div class="space-y-5 px-8 py-7 text-sm leading-6 text-[#6D665C]">
                <p>For now, this is placeholder origin copy. The working story is that Sibol grew from conversations with parents, teachers, and school owners who wanted one calm place for attendance, daily updates, messages, pickup notes, and tuition reminders.</p>
                <p>The name means growth. Our early product direction is shaped around small schools, mobile-first family communication, and practical local workflows like verified pickups, peso invoices, and English or Filipino interface text.</p>
                <p>As the pilot takes shape, this page will be updated with the real founding story, team details, and the schools helping us build Sibol responsibly.</p>
            </div>

            <div class="mx-8 mb-8 rounded-2xl bg-[#E9F3EE] p-5">
                <h2 class="text-base font-semibold text-[#286446]">Preview note</h2>
                <p class="mt-2 text-sm leading-6 text-[#2E2A24]">This page is part of the development preview. Do not enter or publish real child, parent, staff, or payment information yet.</p>
            </div>
        </article>
    </main>
</body>
</html>
