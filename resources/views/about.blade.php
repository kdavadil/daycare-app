<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#286446">
    <title>About Sibol · School & family</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-stone-50 font-sans antialiased">
    <main class="mx-auto max-w-lg px-5 py-10">
        <nav class="mb-6 flex items-center justify-between text-sm">
            <a href="{{ route('home') }}" class="font-semibold text-emerald-800 underline-offset-4 hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-emerald-700">Sibol</a>
            <a href="{{ route('home') }}" class="text-stone-600 underline-offset-4 hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-emerald-700">Back home</a>
        </nav>

        <article class="rounded-3xl border border-stone-200 bg-white p-8 shadow-sm">
            <p class="text-sm font-semibold tracking-widest text-emerald-800 uppercase">About Sibol</p>
            <h1 class="mt-5 text-3xl font-semibold tracking-tight text-stone-900">Built from everyday school moments.</h1>
            <p class="mt-4 text-base leading-relaxed text-stone-600">Sibol started as a simple idea: make preschool communication feel warmer, clearer, and easier for families in the Philippines.</p>

            <div class="mt-7 space-y-5 text-sm leading-6 text-stone-600">
                <p>For now, this is placeholder origin copy. The working story is that Sibol grew from conversations with parents, teachers, and school owners who wanted one calm place for attendance, daily updates, messages, pickup notes, and tuition reminders.</p>
                <p>The name means growth. Our early product direction is shaped around small schools, mobile-first family communication, and practical local workflows like verified pickups, peso invoices, and English or Filipino interface text.</p>
                <p>As the pilot takes shape, this page will be updated with the real founding story, team details, and the schools helping us build Sibol responsibly.</p>
            </div>

            <div class="mt-8 rounded-2xl bg-emerald-50 p-5">
                <h2 class="text-base font-semibold text-emerald-950">Preview note</h2>
                <p class="mt-2 text-sm leading-6 text-emerald-900">This page is part of the development preview. Do not enter or publish real child, parent, staff, or payment information yet.</p>
            </div>
        </article>
    </main>
</body>
</html>
