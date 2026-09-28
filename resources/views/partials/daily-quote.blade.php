@php
    $quotes = config('motivational_quotes.quotes', []);
    $quote = count($quotes) > 0 ? $quotes[now()->dayOfYear % count($quotes)] : null;
@endphp

@if($quote && session('show_daily_quote'))
        <div x-data="{ open: false }"
            x-init="requestAnimationFrame(() => open = true)"
         x-show="open"
         x-cloak
         x-transition:enter="transform transition ease-out duration-500"
         x-transition:enter-start="opacity-0 translate-y-6 scale-95 sm:translate-y-0 sm:translate-x-6"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100 sm:translate-x-0"
         x-transition:leave="transform transition ease-in duration-300"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100 sm:translate-x-0"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95 sm:translate-y-0 sm:translate-x-6"
         class="fixed inset-x-4 bottom-4 z-[100] sm:left-auto sm:right-6 sm:w-[360px]"
         role="status"
         aria-live="polite">
         <section class="relative overflow-hidden rounded-3xl border border-[#186369]/20
                   bg-white/95 p-5 shadow-[0_20px_60px_rgba(24,99,105,0.3)]
                   ring-2 ring-[#c0a05f]/15
                        backdrop-blur-xl"
                 aria-label="Quotes motivasi hari ini">
             <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r
                   from-[#186369] via-[#c0a05f] to-[#186369]"
               aria-hidden="true"></div>
            <div class="absolute -right-8 -top-8 h-24 w-24 rounded-full
                        pointer-events-none bg-[#c0a05f]/20 blur-2xl" aria-hidden="true"></div>

<button type="button"
        @click="open = false"
        class="absolute right-4 top-4 z-20 flex h-6 w-6 cursor-pointer
               items-center justify-center rounded-full p-0
               text-[#186369]/50 transition hover:text-[#186369]"
        aria-label="Tutup quotes motivasi">
    <svg viewBox="0 0 24 24"
         class="block h-5 w-5"
         fill="none"
         stroke="currentColor"
         stroke-width="2.25"
         stroke-linecap="round"
         aria-hidden="true">
        <path d="M6 6l12 12M18 6L6 18" />
    </svg>
</button>

            <div class="relative flex items-start gap-3 pr-7">
                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center
                            rounded-2xl bg-[#186369] text-2xl font-serif text-white"
                     aria-hidden="true">
                    &ldquo;
                </div>
                <div class="min-w-0">
                      <p class="mb-1 flex items-center gap-1.5 text-[10px] font-bold
                            uppercase tracking-[0.18em] text-[#c0a05f]">
                        <span class="h-1.5 w-1.5 rounded-full bg-[#c0a05f] animate-pulse"
                            aria-hidden="true"></span>
                        Pesan untuk hari ini
                    </p>
                    <blockquote class="text-sm font-semibold leading-relaxed text-[#186369]">
                        {{ $quote }}
                    </blockquote>
                    <p class="mt-3 text-[11px] font-medium text-[#186369]/55">
                        Semoga harimu berjalan dengan baik.
                    </p>
                </div>
            </div>
        </section>
    </div>
@endif
