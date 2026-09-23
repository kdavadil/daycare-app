<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#286446">
    <title>{{ $child->preferred_name }} · Sibol</title>
    <link rel="icon" href="{{ asset('sibol-icon.svg') }}" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#F8F7F2] font-sans antialiased">
    <main class="mx-auto max-w-lg px-5 py-8">
        <nav class="mb-6 flex items-center justify-between text-sm">
            <a href="{{ route('dashboard') }}" class="rounded-xl border border-[#E6DFD2] bg-white px-4 py-2 font-semibold text-[#286446] underline-offset-4 hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#286446]">Dashboard</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="rounded-xl border border-[#E6DFD2] bg-white px-4 py-2 font-semibold text-[#286446] underline-offset-4 hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#286446]">Sign out</button>
            </form>
        </nav>

        <section class="overflow-hidden rounded-[2rem] border border-[#E6DFD2] bg-white shadow-sm">
            <div class="bg-[#F8F7F2] p-6">
                <p class="text-sm font-semibold tracking-widest text-[#286446] uppercase">Today at {{ $child->school->name }}</p>
                <h1 class="mt-4 text-3xl font-semibold tracking-tight text-[#2E2A24]">{{ $child->preferred_name }}’s day</h1>
                <p class="mt-3 text-sm leading-6 text-[#6D665C]">{{ $child->schoolClass->name }} class · {{ $child->schoolClass->age_group }}</p>
            </div>

            <div class="grid gap-5 px-6 py-6">
                @if ($linkedChildren->count() > 1)
                    <div class="rounded-2xl border border-[#E6DFD2] bg-[#FBFAF6] p-4">
                        <p class="text-xs font-semibold tracking-widest text-[#286446] uppercase">Your children</p>
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach ($linkedChildren as $linkedChild)
                                <a href="{{ route('children.show', $linkedChild) }}" class="rounded-full border border-[#E6DFD2] {{ $linkedChild->id === $child->id ? 'bg-[#286446] text-white' : 'bg-white text-[#286446]' }} px-3 py-2 text-xs font-semibold">{{ $linkedChild->preferred_name }}</a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <article class="rounded-3xl bg-[#E9F3EE] p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold tracking-widest text-[#286446] uppercase">School status</p>
                            <h2 class="mt-2 text-2xl font-semibold text-[#2E2A24]">{{ $child->isCheckedIn() ? 'In school' : 'Not checked in' }}</h2>
                            @if ($child->latestAttendanceRecord)
                                <p class="mt-2 text-sm leading-6 text-[#6D665C]">
                                    {{ $child->latestAttendanceRecord->type === \App\Models\AttendanceRecord::CheckIn ? 'Checked in' : 'Checked out' }}
                                    {{ $child->latestAttendanceRecord->occurred_at->timezone($child->school->timezone)->format('g:i A') }}
                                    by {{ $child->latestAttendanceRecord->actor_name }}
                                </p>
                            @else
                                <p class="mt-2 text-sm leading-6 text-[#6D665C]">No attendance record yet today.</p>
                            @endif
                        </div>
                        <span class="rounded-full bg-white px-3 py-1.5 text-xs font-semibold text-[#286446]">{{ now($child->school->timezone)->format('M j') }}</span>
                    </div>
                </article>

                <a href="{{ route('children.messages.index', $child) }}" class="rounded-3xl border border-[#E6DFD2] bg-white p-5 text-sm font-semibold text-[#2E2A24] underline-offset-4 hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#286446]">
                    Messages with {{ $child->preferred_name }}’s teachers
                    <span class="mt-1 block text-xs font-normal leading-5 text-[#6D665C]">Ask a question or reply to a teacher note in one child-specific thread.</span>
                </a>

                <section>
                    <div class="flex items-end justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold tracking-widest text-[#286446] uppercase">Daily journal</p>
                            <h2 class="mt-2 text-xl font-semibold text-[#2E2A24]">Latest updates</h2>
                        </div>
                    </div>

                    <div class="mt-4 grid gap-4">
                        @forelse ($child->journalEntries as $entry)
                            <article class="rounded-2xl border border-[#E6DFD2] bg-white p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-xs font-semibold tracking-widest text-[#286446] uppercase">{{ $entry->categoryLabel() }}</p>
                                        <h3 class="mt-1 text-base font-semibold text-[#2E2A24]">{{ $entry->title }}</h3>
                                    </div>
                                    <time class="text-right text-xs leading-5 text-[#6D665C]">{{ $entry->occurred_at->timezone($child->school->timezone)->format('g:i A') }}</time>
                                </div>

                                @if ($entry->photo_path)
                                    <img src="{{ route('daily-updates.photo', $entry) }}" alt="Photo for {{ $entry->title }}" class="mt-3 aspect-[4/3] w-full rounded-2xl object-cover">
                                @endif

                                @if ($entry->body)
                                    <p class="mt-3 text-sm leading-6 text-[#6D665C]">{{ $entry->body }}</p>
                                @endif

                                @if ($entry->meal_amount)
                                    <p class="mt-3 rounded-xl bg-[#FFF1C7] p-3 text-xs font-semibold text-[#3B3014]">Meal amount: {{ $entry->meal_amount }}</p>
                                @endif

                                <p class="mt-3 text-xs leading-5 text-[#6D665C]">Posted by {{ $entry->author->name }}</p>
                            </article>
                        @empty
                            <div class="rounded-2xl bg-[#FFF1C7] p-4">
                                <p class="text-sm font-semibold text-[#3B3014]">No daily updates yet.</p>
                                <p class="mt-2 text-sm leading-6 text-[#6D665C]">Teacher notes, care logs, and photos will appear here after they are posted.</p>
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>
        </section>
    </main>
</body>
</html>
