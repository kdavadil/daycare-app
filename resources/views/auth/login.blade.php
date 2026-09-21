<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#286446">
    <title>Sign in · Sibol</title>
    <link rel="icon" href="{{ asset('sibol-icon.svg') }}" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#F8F7F2] font-sans antialiased">
    <main class="mx-auto max-w-lg px-5 py-8">
        <section class="overflow-hidden rounded-[2rem] border border-[#E6DFD2] bg-white shadow-sm">
            <div class="bg-[#F8F7F2] p-6">
                <x-brand-mark />
                <p class="mt-8 text-sm font-semibold tracking-widest text-[#286446] uppercase">Sign in</p>
                <h1 class="mt-4 text-3xl font-semibold tracking-tight text-[#2E2A24]">Welcome back to Sibol.</h1>
                <p class="mt-3 text-sm leading-6 text-[#6D665C]">Use your approved school Google account. Public registration is still closed while we prepare the pilot.</p>
            </div>

            <div class="grid gap-5 px-6 py-6">
                @if (session('status'))
                    <p class="rounded-2xl bg-[#E9F3EE] p-4 text-sm font-semibold text-[#286446]" role="status">{{ session('status') }}</p>
                @endif

                @if (config('services.google.client_id') && config('services.google.client_secret'))
                    <a href="{{ route('auth.google.redirect') }}" class="inline-flex min-h-12 items-center justify-center gap-3 rounded-xl bg-[#286446] px-5 py-3 text-sm font-semibold text-white focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#286446]">
                        <span class="flex size-6 items-center justify-center rounded-full bg-white text-sm font-bold text-[#286446]">G</span>
                        Continue with Google
                    </a>
                @else
                    <p class="rounded-2xl bg-[#FFF1C7] p-4 text-sm font-semibold leading-6 text-[#3B3014]" role="status">Google sign-in is ready in code. Add Google OAuth credentials to enable the button.</p>
                @endif

                <p class="text-xs leading-5 text-[#6D665C]">No passwords are stored for Google-only accounts. Access control by school role comes next.</p>
            </div>
        </section>
    </main>
</body>
</html>
