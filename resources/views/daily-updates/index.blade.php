<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#286446">
    <title>Daily updates · Sibol</title>
    <link rel="icon" href="{{ asset('sibol-icon.svg') }}" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#F8F7F2] font-sans antialiased">
    <main class="mx-auto max-w-lg px-5 py-8">
        <nav class="mb-6 flex items-center justify-between text-sm">
            <a href="{{ route('dashboard') }}" class="rounded-xl border border-[#E6DFD2] bg-white px-4 py-2 font-semibold text-[#286446] underline-offset-4 hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#286446]">Dashboard</a>
            <a href="{{ route('attendance.index') }}" class="rounded-xl border border-[#E6DFD2] bg-white px-4 py-2 font-semibold text-[#286446] underline-offset-4 hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#286446]">Attendance</a>
        </nav>

        <section class="overflow-hidden rounded-[2rem] border border-[#E6DFD2] bg-white shadow-sm">
            <div class="bg-[#F8F7F2] p-6">
                <p class="text-sm font-semibold tracking-widest text-[#286446] uppercase">Teacher journal</p>
                <h1 class="mt-4 text-3xl font-semibold tracking-tight text-[#2E2A24]">Post a daily update.</h1>
                <p class="mt-3 text-sm leading-6 text-[#6D665C]">Add activity notes, care logs, meal records, and optional photos for students in your class.</p>
            </div>

            <div class="grid gap-6 px-6 py-6">
                @if (session('daily_update_status'))
                    <p class="rounded-2xl bg-[#E9F3EE] p-4 text-sm font-semibold text-[#286446]" role="status">{{ session('daily_update_status') }}</p>
                @endif

                @if ($errors->any())
                    <div class="rounded-2xl bg-[#FFF1C7] p-4" role="alert">
                        <p class="text-sm font-semibold text-[#3B3014]">Please check the update details.</p>
                        <ul class="mt-2 list-inside list-disc text-sm leading-6 text-[#6D665C]">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('daily-updates.store') }}" enctype="multipart/form-data" class="grid gap-4">
                    @csrf

                    <label class="grid gap-1 text-xs font-semibold text-[#6D665C]">
                        Student
                        <select name="child_id" class="min-h-12 rounded-xl border border-[#E6DFD2] bg-white px-3 text-base text-[#2E2A24]" required>
                            <option value="">Choose a student</option>
                            @foreach ($classes as $class)
                                <optgroup label="{{ $class->name }} · {{ $class->age_group }}">
                                    @foreach ($class->children as $child)
                                        <option value="{{ $child->id }}" @selected((int) old('child_id') === $child->id)>{{ $child->preferred_name }} {{ $child->last_name }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </label>

                    <label class="grid gap-1 text-xs font-semibold text-[#6D665C]">
                        Update type
                        <select name="category" class="min-h-12 rounded-xl border border-[#E6DFD2] bg-white px-3 text-base text-[#2E2A24]" required>
                            @foreach ($categories as $key => $label)
                                <option value="{{ $key }}" @selected(old('category', \App\Models\JournalEntry::LearningMoment) === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label class="grid gap-1 text-xs font-semibold text-[#6D665C]">
                        Title
                        <input name="title" value="{{ old('title') }}" class="min-h-12 rounded-xl border border-[#E6DFD2] bg-white px-3 text-base text-[#2E2A24]" maxlength="120" required placeholder="A little artist at work">
                    </label>

                    <label class="grid gap-1 text-xs font-semibold text-[#6D665C]">
                        Note
                        <textarea name="body" rows="4" class="rounded-xl border border-[#E6DFD2] bg-white px-3 py-3 text-base text-[#2E2A24]" maxlength="1200" placeholder="What happened today?">{{ old('body') }}</textarea>
                    </label>

                    <label class="grid gap-1 text-xs font-semibold text-[#6D665C]">
                        Meal amount, if this is a meal note
                        <input name="meal_amount" value="{{ old('meal_amount') }}" class="min-h-12 rounded-xl border border-[#E6DFD2] bg-white px-3 text-base text-[#2E2A24]" maxlength="80" placeholder="All finished / half eaten / water offered">
                    </label>

                    <label class="grid gap-1 text-xs font-semibold text-[#6D665C]">
                        Time
                        <input name="occurred_at" type="datetime-local" value="{{ old('occurred_at', now($school->timezone)->format('Y-m-d\\TH:i')) }}" class="min-h-12 rounded-xl border border-[#E6DFD2] bg-white px-3 text-base text-[#2E2A24]" required>
                    </label>

                    <label class="grid gap-1 text-xs font-semibold text-[#6D665C]">
                        Optional photo
                        <input name="photo" type="file" accept="image/*" class="min-h-12 rounded-xl border border-[#E6DFD2] bg-white px-3 py-3 text-sm text-[#2E2A24]">
                        <span class="text-xs font-normal leading-5 text-[#6D665C]">Photos are stored privately and only shown to authorized staff and linked guardians.</span>
                    </label>

                    <button type="submit" class="min-h-12 rounded-xl bg-[#286446] px-5 py-3 text-sm font-semibold text-white focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#286446]">Publish daily update</button>
                </form>

                <section>
                    <p class="text-xs font-semibold tracking-widest text-[#286446] uppercase">Recent class updates</p>
                    <div class="mt-4 grid gap-3">
                        @forelse ($recentEntries as $entry)
                            <article class="rounded-2xl border border-[#E6DFD2] bg-[#FBFAF6] p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-xs font-semibold tracking-widest text-[#286446] uppercase">{{ $entry->categoryLabel() }}</p>
                                        <h2 class="mt-1 text-base font-semibold text-[#2E2A24]">{{ $entry->title }}</h2>
                                        <p class="mt-1 text-xs leading-5 text-[#6D665C]">{{ $entry->child->preferred_name }} · {{ $entry->child->schoolClass->name }}</p>
                                    </div>
                                    <time class="text-right text-xs leading-5 text-[#6D665C]">{{ $entry->occurred_at->timezone($school->timezone)->format('M j, g:i A') }}</time>
                                </div>
                                @if ($entry->body)
                                    <p class="mt-3 text-sm leading-6 text-[#6D665C]">{{ $entry->body }}</p>
                                @endif
                            </article>
                        @empty
                            <p class="rounded-2xl bg-[#FFF1C7] p-4 text-sm leading-6 text-[#6D665C]">No daily updates have been posted yet.</p>
                        @endforelse
                    </div>
                </section>
            </div>
        </section>
    </main>
</body>
</html>
