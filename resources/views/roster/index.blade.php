<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#286446">
    <title>Roster · Sibol</title>
    <link rel="icon" href="{{ asset('sibol-icon.svg') }}" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#F8F7F2] font-sans antialiased">
    <main class="mx-auto max-w-lg px-5 py-8">
        <nav class="mb-6 flex items-center justify-between text-sm">
            <a href="{{ route('home') }}" class="rounded-2xl focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#286446]">
                <x-brand-mark />
            </a>
            <a href="{{ route('home') }}" class="rounded-xl border border-[#E6DFD2] bg-white px-4 py-2 font-semibold text-[#286446] underline-offset-4 hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#286446]">Home</a>
        </nav>

        <section class="overflow-hidden rounded-[2rem] border border-[#E6DFD2] bg-white shadow-sm">
            <div class="bg-[#F8F7F2] p-6">
                <p class="text-sm font-semibold tracking-widest text-[#286446] uppercase">School roster</p>
                <h1 class="mt-4 text-3xl font-semibold tracking-tight text-[#2E2A24]">{{ $school->name }}</h1>
                <p class="mt-3 text-sm leading-6 text-[#6D665C]">{{ $school->address }} · {{ $school->timezone }}</p>

                <div class="mt-5 grid grid-cols-3 gap-3">
                    <div class="rounded-2xl bg-white p-4 text-center">
                        <p class="text-2xl font-semibold text-[#286446]">{{ $school->classes->count() }}</p>
                        <p class="mt-1 text-xs font-semibold uppercase tracking-widest text-[#6D665C]">Classes</p>
                    </div>
                    <div class="rounded-2xl bg-white p-4 text-center">
                        <p class="text-2xl font-semibold text-[#286446]">{{ $school->classes->sum(fn ($class) => $class->children->count()) }}</p>
                        <p class="mt-1 text-xs font-semibold uppercase tracking-widest text-[#6D665C]">Children</p>
                    </div>
                    <div class="rounded-2xl bg-white p-4 text-center">
                        <p class="text-2xl font-semibold text-[#286446]">{{ $school->staffMembers->count() }}</p>
                        <p class="mt-1 text-xs font-semibold uppercase tracking-widest text-[#6D665C]">Staff</p>
                    </div>
                </div>
            </div>

            <div class="grid gap-5 px-6 py-6">
                @foreach ($school->classes as $class)
                    <section class="rounded-2xl border border-[#E6DFD2] bg-white p-5">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h2 class="text-lg font-semibold text-[#2E2A24]">{{ $class->name }}</h2>
                                <p class="mt-1 text-sm text-[#6D665C]">{{ $class->age_group }} · {{ $class->room }}</p>
                            </div>
                            <span class="rounded-full bg-[#E9F3EE] px-3 py-1.5 text-xs font-semibold text-[#286446]">{{ $class->children->count() }} children</span>
                        </div>

                        <div class="mt-4 rounded-xl bg-[#F8F7F2] p-4">
                            <p class="text-xs font-semibold uppercase tracking-widest text-[#6D665C]">Teachers</p>
                            <p class="mt-2 text-sm font-semibold text-[#2E2A24]">{{ $class->staffMembers->pluck('name')->join(', ') }}</p>
                        </div>

                        <div class="mt-4 grid gap-3">
                            @foreach ($class->children as $child)
                                @php
                                    $isCheckedIn = $child->latestAttendanceRecord?->type === \App\Models\AttendanceRecord::CheckIn;
                                @endphp

                                <article class="rounded-xl border border-[#E6DFD2] p-4">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <h3 class="text-base font-semibold text-[#2E2A24]">{{ $child->preferred_name ?? $child->first_name }} {{ $child->last_name }}</h3>
                                            <p class="mt-1 text-sm text-[#6D665C]">Guardian: {{ $child->guardians->map(fn ($guardian) => $guardian->first_name.' '.$guardian->last_name)->join(', ') }}</p>
                                        </div>
                                        <span class="rounded-full {{ $isCheckedIn ? 'bg-[#E9F3EE] text-[#286446]' : 'bg-[#F4C95D] text-[#3B3014]' }} px-3 py-1.5 text-xs font-semibold">{{ $isCheckedIn ? 'In school' : ucfirst($child->status) }}</span>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endforeach

                <a href="{{ route('attendance.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[#286446] px-5 py-3 text-sm font-semibold text-white focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#286446]">Open attendance</a>
                <p class="text-xs leading-5 text-[#6D665C]">Preview roster only. Editing, invitations, and account permissions come next.</p>
            </div>
        </section>
    </main>
</body>
</html>
