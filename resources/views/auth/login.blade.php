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

                <p class="text-xs leading-5 text-[#6D665C]">No passwords are stored for Google-only accounts. Access control is matched by school role.</p>

                @if (config('services.demo_login.enabled'))
                    <div class="rounded-2xl border border-[#E6DFD2] bg-[#FBFAF6] p-4">
                        <p class="text-sm font-semibold text-[#2E2A24]">Demo persona login</p>
                        <p class="mt-1 text-xs leading-5 text-[#6D665C]">Use this only for pilot testing on the temporary host.</p>

                        @error('pin')
                            <p class="mt-3 rounded-xl bg-[#FFF1C7] p-3 text-xs font-semibold text-[#3B3014]" role="alert">{{ $message }}</p>
                        @enderror

                        <form method="POST" action="{{ route('auth.demo') }}" class="mt-4 grid gap-3">
                            @csrf
                            <label class="grid gap-1 text-xs font-semibold text-[#6D665C]">
                                Persona
                                <select name="persona" class="min-h-11 rounded-xl border border-[#E6DFD2] bg-white px-3 text-sm text-[#2E2A24]">
                                    @foreach (config('services.demo_login.personas') as $key => $persona)
                                        <option value="{{ $key }}" @selected(old('persona') === $key)>{{ $persona['label'] }}</option>
                                    @endforeach
                                </select>
                            </label>

                            <label class="grid gap-1 text-xs font-semibold text-[#6D665C]">
                                Demo PIN
                                <input name="pin" type="password" inputmode="text" class="min-h-11 rounded-xl border border-[#E6DFD2] bg-white px-3 text-sm text-[#2E2A24]" autocomplete="off">
                            </label>

                            <button type="submit" class="min-h-11 rounded-xl bg-[#2E2A24] px-4 py-2 text-sm font-semibold text-white focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#286446]">Open demo dashboard</button>
                        </form>
                    </div>
                @endif
            </div>
        </section>
    </main>
</body>
</html>
