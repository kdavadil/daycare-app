<section class="overflow-hidden rounded-[2rem] border border-[#E6DFD2] bg-white shadow-sm">
    <div class="flex items-center justify-between px-6 py-5">
        <x-brand-mark />
        <a href="{{ route('about') }}" class="rounded-xl border border-[#E6DFD2] px-4 py-2 text-sm font-semibold text-[#286446] underline-offset-4 hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#286446]">
            About
        </a>
    </div>

    <div class="border-y border-[#E6DFD2] bg-[#F8F7F2] px-6 py-7">
        <p class="text-sm font-semibold tracking-widest text-[#286446] uppercase">Preview app</p>
        <h1 class="mt-4 text-3xl font-semibold tracking-tight text-[#2E2A24]">{{ $locale === 'en' ? 'A little space to grow.' : 'Munting espasyo para lumago.' }}</h1>
        <p class="mt-4 text-base leading-relaxed text-[#6D665C]">{{ $locale === 'en' ? 'Our school and family app is taking root. This is the development foundation; family accounts and school features are coming next.' : 'Inihahanda pa ang app para sa paaralan at pamilya. Susunod ang mga account at tampok para sa paaralan.' }}</p>
        <div class="mt-6 flex flex-wrap gap-3">
            <button wire:click="toggleLanguage" wire:loading.attr="disabled" type="button" class="min-h-11 rounded-xl bg-[#286446] px-5 py-3 text-sm font-semibold text-white focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#286446]" aria-label="Change language">
                {{ $locale === 'en' ? 'Filipino' : 'English' }}
            </button>
            <span class="inline-flex min-h-11 items-center rounded-xl bg-[#F4C95D] px-4 py-3 text-sm font-semibold text-[#3B3014]">
                {{ $locale === 'en' ? 'Pilot preview' : 'Pilot preview' }}
            </span>
        </div>
    </div>

    <div class="grid gap-4 px-6 py-6">
        <div class="rounded-2xl border border-[#E6DFD2] bg-white p-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-[#6D665C]">Today</p>
                    <h2 class="mt-2 text-lg font-semibold text-[#2E2A24]">Maya is checked in</h2>
                </div>
                <span class="rounded-full bg-[#E9F3EE] px-3 py-1.5 text-xs font-semibold text-[#286446]">8:04 AM</span>
            </div>
            <p class="mt-3 text-sm leading-6 text-[#6D665C]">Daily updates, pickup notes, and school messages will appear here once accounts are ready.</p>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div class="rounded-2xl border border-[#E6DFD2] bg-white p-4">
                <p class="text-xs font-semibold uppercase tracking-widest text-[#6D665C]">Next</p>
                <p class="mt-2 text-sm font-semibold text-[#2E2A24]">Journal</p>
            </div>
            <div class="rounded-2xl border border-[#E6DFD2] bg-white p-4">
                <p class="text-xs font-semibold uppercase tracking-widest text-[#6D665C]">Soon</p>
                <p class="mt-2 text-sm font-semibold text-[#2E2A24]">Fees</p>
            </div>
        </div>

        <p class="text-xs text-[#6D665C]" role="status">{{ $locale === 'en' ? 'Preview - No real child or payment data' : 'Preview - Walang tunay na datos ng bata o bayad' }}</p>
    </div>
</section>
