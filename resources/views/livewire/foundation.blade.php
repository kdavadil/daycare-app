<section class="rounded-3xl border border-stone-200 bg-white p-8 shadow-sm">
    <p class="text-sm font-semibold tracking-widest text-emerald-800 uppercase">Sibol</p>
    <h1 class="mt-5 text-3xl font-semibold tracking-tight text-stone-900">{{ $locale === 'en' ? 'A little space to grow.' : 'Munting espasyo para lumago.' }}</h1>
    <p class="mt-4 text-base leading-relaxed text-stone-600">{{ $locale === 'en' ? 'Our school and family app is taking root. This is the development foundation; family accounts and school features are coming next.' : 'Inihahanda pa ang app para sa paaralan at pamilya. Susunod ang mga account at tampok para sa paaralan.' }}</p>
    <button wire:click="toggleLanguage" wire:loading.attr="disabled" type="button" class="mt-7 min-h-11 rounded-xl bg-emerald-800 px-5 py-3 text-sm font-semibold text-white focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-emerald-700" aria-label="Change language">
        {{ $locale === 'en' ? 'Filipino' : 'English' }}
    </button>
    <p class="mt-5 text-xs text-stone-500" role="status">{{ $locale === 'en' ? 'Preview • No real child or payment data' : 'Preview • Walang tunay na datos ng bata o bayad' }}</p>
</section>
