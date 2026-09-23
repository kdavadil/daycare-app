<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#286446">
    <title>{{ $child->preferred_name }} messages · Sibol</title>
    <link rel="icon" href="{{ asset('sibol-icon.svg') }}" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#F8F7F2] font-sans antialiased">
    <main class="mx-auto max-w-lg px-5 py-8">
        <nav class="mb-6 flex flex-wrap items-center justify-between gap-3 text-sm">
            <a href="{{ route('dashboard') }}" class="rounded-xl border border-[#E6DFD2] bg-white px-4 py-2 font-semibold text-[#286446] underline-offset-4 hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#286446]">Dashboard</a>
            <a href="{{ route('children.show', $child) }}" class="rounded-xl border border-[#E6DFD2] bg-white px-4 py-2 font-semibold text-[#286446] underline-offset-4 hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#286446]">{{ $child->preferred_name }}’s day</a>
        </nav>

        <section class="overflow-hidden rounded-[2rem] border border-[#E6DFD2] bg-white shadow-sm">
            <div class="bg-[#F8F7F2] p-6">
                <p class="text-sm font-semibold tracking-widest text-[#286446] uppercase">Child message thread</p>
                <h1 class="mt-4 text-3xl font-semibold tracking-tight text-[#2E2A24]">{{ $child->preferred_name }} {{ $child->last_name }}</h1>
                <p class="mt-3 text-sm leading-6 text-[#6D665C]">{{ $child->schoolClass->name }} class · Messages are visible only to linked family and assigned school staff.</p>
            </div>

            <div class="grid gap-5 px-6 py-6">
                @if (session('message_status'))
                    <p class="rounded-2xl bg-[#E9F3EE] p-4 text-sm font-semibold text-[#286446]" role="status">{{ session('message_status') }}</p>
                @endif

                @if ($errors->any())
                    <div class="rounded-2xl bg-[#FFF1C7] p-4" role="alert">
                        <p class="text-sm font-semibold text-[#3B3014]">Please check the message.</p>
                        <ul class="mt-2 list-inside list-disc text-sm leading-6 text-[#6D665C]">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <section class="grid gap-3">
                    @forelse ($child->messages as $message)
                        @php
                            $isMine = $message->sender_id === auth()->id();
                        @endphp
                        <article class="rounded-3xl border {{ $isMine ? 'border-[#C7DDCF] bg-[#E9F3EE]' : 'border-[#E6DFD2] bg-white' }} p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-xs font-semibold tracking-widest text-[#286446] uppercase">{{ $message->displayRole() }}</p>
                                    <h2 class="mt-1 text-sm font-semibold text-[#2E2A24]">{{ $message->sender->name }}</h2>
                                </div>
                                <time class="text-right text-xs leading-5 text-[#6D665C]">{{ $message->sent_at->timezone($child->school->timezone)->format('M j, g:i A') }}</time>
                            </div>
                            <p class="mt-3 whitespace-pre-line text-sm leading-6 text-[#2E2A24]">{{ $message->body }}</p>
                        </article>
                    @empty
                        <div class="rounded-2xl bg-[#FFF1C7] p-4">
                            <p class="text-sm font-semibold text-[#3B3014]">No messages yet.</p>
                            <p class="mt-2 text-sm leading-6 text-[#6D665C]">Start the thread with a quick question or teacher note for {{ $child->preferred_name }}.</p>
                        </div>
                    @endforelse
                </section>

                <form method="POST" action="{{ route('children.messages.store', $child) }}" class="rounded-3xl border border-[#E6DFD2] bg-[#FBFAF6] p-4">
                    @csrf
                    <label for="body" class="text-sm font-semibold text-[#2E2A24]">New message</label>
                    <textarea id="body" name="body" rows="4" required maxlength="1200" class="mt-2 w-full rounded-2xl border border-[#D8D0C3] bg-white px-4 py-3 text-sm text-[#2E2A24] focus:border-[#286446] focus:outline-none focus:ring-2 focus:ring-[#C7DDCF]" placeholder="Type a message about {{ $child->preferred_name }}...">{{ old('body') }}</textarea>
                    <p class="mt-2 text-xs leading-5 text-[#6D665C]">Text-only for the pilot. Photos stay in daily updates for now.</p>
                    <button type="submit" class="mt-4 inline-flex min-h-11 items-center justify-center rounded-xl bg-[#286446] px-5 py-3 text-sm font-semibold text-white focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#286446]">Send message</button>
                </form>
            </div>
        </section>
    </main>
</body>
</html>
