<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#286446">
    <title>Class calendar · Sibol</title>
    <link rel="icon" href="{{ asset('sibol-icon.svg') }}" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#F8F7F2] font-sans antialiased">
    <main class="mx-auto max-w-5xl px-5 py-8">
        <nav class="mb-6 flex flex-wrap items-center justify-between gap-3 text-sm">
            <a href="{{ route('dashboard') }}" class="rounded-xl border border-[#E6DFD2] bg-white px-4 py-2 font-semibold text-[#286446] underline-offset-4 hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#286446]">Dashboard</a>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('attendance.index') }}" class="rounded-xl border border-[#E6DFD2] bg-white px-4 py-2 font-semibold text-[#286446] underline-offset-4 hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#286446]">Attendance</a>
                <a href="{{ route('daily-updates.index') }}" class="rounded-xl border border-[#E6DFD2] bg-white px-4 py-2 font-semibold text-[#286446] underline-offset-4 hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#286446]">Daily updates</a>
            </div>
        </nav>

        <section class="overflow-hidden rounded-[2rem] border border-[#E6DFD2] bg-white shadow-sm">
            <div class="bg-[#F8F7F2] p-6">
                <p class="text-sm font-semibold tracking-widest text-[#286446] uppercase">{{ $school->name }}</p>
                <div class="mt-4 flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <h1 class="text-3xl font-semibold tracking-tight text-[#2E2A24]">Class calendar</h1>
                        <p class="mt-3 text-sm leading-6 text-[#6D665C]">Plan class activities, reminders, and school events for {{ $classes->pluck('name')->join(', ') }}.</p>
                    </div>
                    <div class="flex gap-2 text-sm">
                        <a href="{{ route('class-calendar.index', ['month' => $previousMonth]) }}" class="rounded-xl border border-[#E6DFD2] bg-white px-4 py-2 font-semibold text-[#286446] underline-offset-4 hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#286446]">Previous</a>
                        <a href="{{ route('class-calendar.index', ['month' => $nextMonth]) }}" class="rounded-xl border border-[#E6DFD2] bg-white px-4 py-2 font-semibold text-[#286446] underline-offset-4 hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#286446]">Next</a>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 p-4 sm:p-6">
                <div class="rounded-3xl border border-[#E6DFD2] bg-[#FBFAF6] p-4">
                    <h2 class="text-xl font-semibold text-[#2E2A24]">{{ $month->format('F Y') }}</h2>

                    <div class="mt-4 grid grid-cols-7 gap-1 text-center text-[0.68rem] font-semibold tracking-widest text-[#6D665C] uppercase">
                        @foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $dayName)
                            <div>{{ $dayName }}</div>
                        @endforeach
                    </div>

                    <div class="mt-2 grid gap-1">
                        @foreach ($calendarWeeks as $week)
                            <div class="grid grid-cols-7 gap-1">
                                @foreach ($week as $day)
                                    <div class="min-h-24 rounded-2xl border p-2 {{ $day['inCurrentMonth'] ? 'border-[#E6DFD2] bg-white' : 'border-transparent bg-[#F1EFE7] text-[#A39A8C]' }} {{ $day['isToday'] ? 'ring-2 ring-[#F7C948]' : '' }}">
                                        <p class="text-xs font-semibold {{ $day['inCurrentMonth'] ? 'text-[#2E2A24]' : 'text-[#A39A8C]' }}">{{ $day['date']->day }}</p>
                                        <div class="mt-2 grid gap-1">
                                            @foreach ($day['events']->take(2) as $event)
                                                <p class="truncate rounded-lg bg-[#E9F3EE] px-2 py-1 text-[0.68rem] font-semibold text-[#286446]" title="{{ $event->title }}">
                                                    {{ $event->title }}
                                                </p>
                                            @endforeach
                                            @if ($day['events']->count() > 2)
                                                <p class="text-[0.68rem] font-semibold text-[#6D665C]">+{{ $day['events']->count() - 2 }} more</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>

                <section class="grid gap-3">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-xl font-semibold text-[#2E2A24]">Upcoming class events</h2>
                        <p class="text-xs font-semibold tracking-widest text-[#286446] uppercase">{{ $events->count() }} this view</p>
                    </div>

                    @forelse ($events as $event)
                        <article class="rounded-3xl border border-[#E6DFD2] bg-white p-4">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <p class="text-xs font-semibold tracking-widest text-[#286446] uppercase">{{ $event->displayType() }}</p>
                                    <h3 class="mt-2 text-lg font-semibold text-[#2E2A24]">{{ $event->title }}</h3>
                                    <p class="mt-1 text-sm leading-6 text-[#6D665C]">{{ $event->schoolClass->name }} · {{ $event->event_date->format('M j, Y') }}@if ($event->starts_at) · {{ $event->starts_at->format('g:i A') }}@endif</p>
                                </div>
                                <span class="rounded-full bg-[#FFF1C7] px-3 py-1 text-xs font-semibold text-[#3B3014]">{{ $event->schoolClass->age_group ?? 'Class' }}</span>
                            </div>
                            @if ($event->description)
                                <p class="mt-3 text-sm leading-6 text-[#6D665C]">{{ $event->description }}</p>
                            @endif
                        </article>
                    @empty
                        <div class="rounded-3xl border border-dashed border-[#D8D0C3] bg-white p-6 text-center">
                            <p class="text-sm font-semibold text-[#2E2A24]">No class events yet for {{ $month->format('F') }}.</p>
                            <p class="mt-2 text-sm leading-6 text-[#6D665C]">Use the next story to let admins and teachers add events from this screen.</p>
                        </div>
                    @endforelse
                </section>
            </div>
        </section>
    </main>
</body>
</html>
