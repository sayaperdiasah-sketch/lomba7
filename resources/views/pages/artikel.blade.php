@extends('layouts.app', ['activeNav' => 'artikel'])

@section('title', 'Artikel Kesehatan')

@section('content')
    <header class="flex flex-col gap-1 flex-shrink-0">
        <p class="text-[10px] font-bold uppercase tracking-widest text-[#c0a05f]">Mindo Journal</p>
        <h1 class="text-2xl sm:text-3xl font-bold text-[#186369]">Artikel Kesehatan</h1>
        <p class="text-sm text-[#186369]/70">Informasi kesehatan yang telah ditinjau untuk kamu.</p>
    </header>

    @if ($articles->isEmpty())
        <section class="bg-white rounded-[32px] p-8 border border-gray-100 text-center">
            <p class="text-sm font-bold text-[#186369]">Belum ada artikel yang tersedia.</p>
            <p class="text-xs text-[#186369]/60 mt-1">Artikel baru akan muncul di sini setelah dipublikasikan.</p>
        </section>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach ($articles as $article)
                <a href="{{ route('artikel.detail', $article) }}"
                         class="card-animated group bg-white rounded-[28px] overflow-hidden border border-gray-100
                          hover:-translate-y-1 hover:shadow-xl hover:shadow-[#186369]/10 transition-all duration-300">
                    <div class="aspect-[16/9] bg-[#e8efed] overflow-hidden">
                        @if ($article->thumbnail_url)
                            <img src="{{ $article->thumbnail_url }}" alt="{{ $article->title }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-[#186369]/40 text-xs font-bold">
                                Mindo Journal
                            </div>
                        @endif
                    </div>
                    <div class="p-5">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-[#c0a05f]">
                            {{ optional($article->published_at)->translatedFormat('d F Y') ?? 'Artikel kesehatan' }}
                        </p>
                        <h2 class="mt-2 text-base font-bold leading-snug text-[#186369]">{{ $article->title }}</h2>
                        <p class="mt-2 text-xs leading-relaxed text-[#186369]/60 line-clamp-3">
                            {{ \Illuminate\Support\Str::limit(strip_tags($article->content ?? ''), 140) }}
                        </p>
                        <span class="inline-flex items-center gap-1 mt-4 text-[10px] font-bold text-[#186369]">
                            Baca artikel
                            <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
@endsection