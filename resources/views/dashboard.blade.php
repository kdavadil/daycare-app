<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#286446">
    <title>Dashboard · Sibol</title>
    <link rel="icon" href="{{ asset('sibol-icon.svg') }}" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#F8F7F2] font-sans antialiased">
    <main class="mx-auto max-w-lg px-5 py-8">
        <nav class="mb-6 flex items-center justify-between text-sm">
            <a href="{{ route('home') }}" class="rounded-2xl focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#286446]">
                <x-brand-mark />
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="rounded-xl border border-[#E6DFD2] bg-white px-4 py-2 font-semibold text-[#286446] underline-offset-4 hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#286446]">Sign out</button>
            </form>
        </nav>

        <section class="overflow-hidden rounded-[2rem] border border-[#E6DFD2] bg-white shadow-sm">
            <div class="bg-[#F8F7F2] p-6">
                <p class="text-sm font-semibold tracking-widest text-[#286446] uppercase">Account</p>
                <h1 class="mt-4 text-3xl font-semibold tracking-tight text-[#2E2A24]">Hi, {{ $user->name }}.</h1>
                <p class="mt-3 text-sm leading-6 text-[#6D665C]">{{ $user->email }}</p>
            </div>

            <div class="grid gap-5 px-6 py-6">
                @if ($memberships->isNotEmpty())
                    <div class="grid gap-3">
                        @foreach ($memberships as $membership)
                            <article class="rounded-2xl border border-[#E6DFD2] bg-[#FBFAF6] p-4">
                                <p class="text-xs font-semibold tracking-widest text-[#286446] uppercase">{{ $membership->displayRole() }}</p>
                                <h2 class="mt-2 text-lg font-semibold text-[#2E2A24]">{{ $membership->school->name }}</h2>
                                <p class="mt-2 text-sm leading-6 text-[#6D665C]">Access matched from your school records. Your workspace will expand as staff and family tools are enabled.</p>
                            </article>
                        @endforeach
                    </div>

                    <div class="grid gap-3">
                        <a href="{{ route('roster.index') }}" class="rounded-2xl border border-[#E6DFD2] bg-white p-4 text-sm font-semibold text-[#2E2A24] underline-offset-4 hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#286446]">Open roster</a>
                        <a href="{{ route('attendance.index') }}" class="rounded-2xl border border-[#E6DFD2] bg-white p-4 text-sm font-semibold text-[#2E2A24] underline-offset-4 hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#286446]">Open attendance</a>
                    </div>
                @else
                    <div class="rounded-2xl bg-[#FFF1C7] p-4">
                        <p class="text-sm font-semibold text-[#3B3014]">Your Google account is signed in, but it is not linked to a school yet.</p>
                        <p class="mt-2 text-sm leading-6 text-[#6D665C]">For the pilot, Sibol only opens school workspaces for approved staff and family email addresses already listed in the roster.</p>
                    </div>
                @endif
            </div>
        </section>
    </main>
</body>
</html>
