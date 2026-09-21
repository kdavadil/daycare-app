<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#286446">
    <title>Attendance · Sibol</title>
    <link rel="icon" href="{{ asset('sibol-icon.svg') }}" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#F8F7F2] font-sans antialiased">
    <main class="mx-auto max-w-lg px-5 py-8">
        <nav class="mb-6 flex items-center justify-between text-sm">
            <a href="{{ route('home') }}" class="rounded-2xl focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#286446]">
                <x-brand-mark />
            </a>
            <a href="{{ route('roster.index') }}" class="rounded-xl border border-[#E6DFD2] bg-white px-4 py-2 font-semibold text-[#286446] underline-offset-4 hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#286446]">Roster</a>
        </nav>

        <section class="overflow-hidden rounded-[2rem] border border-[#E6DFD2] bg-white shadow-sm">
            <div class="bg-[#F8F7F2] p-6">
                <p class="text-sm font-semibold tracking-widest text-[#286446] uppercase">Attendance</p>
                <h1 class="mt-4 text-3xl font-semibold tracking-tight text-[#2E2A24]">Check children in and out</h1>
                <p class="mt-3 text-sm leading-6 text-[#6D665C]">Preview workflow for {{ $school->name }}. Times use {{ $school->timezone }}.</p>

                @if (session('attendance_status'))
                    <p class="mt-5 rounded-2xl bg-[#E9F3EE] p-4 text-sm font-semibold text-[#286446]" role="status">{{ session('attendance_status') }}</p>
                @endif

                @if (session('attendance_error'))
                    <p class="mt-5 rounded-2xl bg-[#FFF1C7] p-4 text-sm font-semibold text-[#3B3014]" role="alert">{{ session('attendance_error') }}</p>
                @endif
            </div>

            <div class="grid gap-5 px-6 py-6">
                @foreach ($school->classes as $class)
                    <section class="rounded-2xl border border-[#E6DFD2] bg-white p-5">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h2 class="text-lg font-semibold text-[#2E2A24]">{{ $class->name }}</h2>
                                <p class="mt-1 text-sm text-[#6D665C]">{{ $class->staffMembers->pluck('name')->join(', ') }}</p>
                            </div>
                            <span class="rounded-full bg-[#E9F3EE] px-3 py-1.5 text-xs font-semibold text-[#286446]">{{ $class->children->count() }} children</span>
                        </div>

                        <div class="mt-4 grid gap-3">
                            @foreach ($class->children as $child)
                                @php
                                    $latestAttendance = $child->latestAttendanceRecord;
                                    $isCheckedIn = $latestAttendance?->type === \App\Models\AttendanceRecord::CheckIn;
                                @endphp

                                <article class="rounded-xl border border-[#E6DFD2] p-4">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <h3 class="text-base font-semibold text-[#2E2A24]">{{ $child->preferred_name ?? $child->first_name }} {{ $child->last_name }}</h3>
                                            <p class="mt-1 text-sm text-[#6D665C]">
                                                {{ $isCheckedIn ? 'Checked in' : 'Checked out' }}
                                                @if ($latestAttendance)
                                                    · {{ $latestAttendance->occurred_at->timezone($school->timezone)->format('g:i A') }}
                                                @endif
                                            </p>
                                        </div>
                                        <span class="rounded-full {{ $isCheckedIn ? 'bg-[#E9F3EE] text-[#286446]' : 'bg-[#F4C95D] text-[#3B3014]' }} px-3 py-1.5 text-xs font-semibold">
                                            {{ $isCheckedIn ? 'In school' : 'Away' }}
                                        </span>
                                    </div>

                                    <form method="POST" action="{{ route('attendance.store') }}" class="mt-4 flex flex-wrap gap-2">
                                        @csrf
                                        <input type="hidden" name="child_id" value="{{ $child->id }}">
                                        <button name="type" value="check_in" type="submit" class="min-h-11 flex-1 rounded-xl bg-[#286446] px-4 py-3 text-sm font-semibold text-white focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#286446]" @disabled($isCheckedIn)>
                                            Check in
                                        </button>
                                        <button name="type" value="check_out" type="submit" class="min-h-11 flex-1 rounded-xl border border-[#E6DFD2] px-4 py-3 text-sm font-semibold text-[#286446] focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#286446]" @disabled(! $isCheckedIn)>
                                            Check out
                                        </button>
                                    </form>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endforeach

                <p class="text-xs leading-5 text-[#6D665C]">Preview attendance only. User accounts, permissions, corrections, and audit review come next.</p>
            </div>
        </section>
    </main>
</body>
</html>
