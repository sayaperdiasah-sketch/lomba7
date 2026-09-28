@extends('layouts.app', ['activeNav' => 'artikel'])

@section('title', $article->title)

@section('content')
    <a href="{{ route('artikel') }}" class="text-xs font-bold text-[#186369]/60 hover:text-[#186369]">
        &larr; Kembali ke semua artikel
    </a>

    <article class="bg-white rounded-[32px] p-6 sm:p-8 border border-gray-100 max-w-4xl">
        <p class="text-[10px] font-bold uppercase tracking-wider text-[#c0a05f]">
            {{ optional($article->published_at)->translatedFormat('d F Y') ?? 'Artikel kesehatan' }}
        </p>
        <h1 class="mt-2 text-2xl sm:text-3xl font-bold leading-tight text-[#186369]">{{ $article->title }}</h1>

        @if ($article->pdf_url)
            <div class="mt-6 rounded-2xl overflow-hidden border border-gray-200 bg-gray-100">
                <iframe src="{{ $article->pdf_url }}#view=FitH"
                        title="{{ $article->title }}"
                        class="w-full h-[70vh] min-h-[520px]"
                        loading="lazy"></iframe>
            </div>
            <a href="{{ $article->pdf_url }}" target="_blank" rel="noopener"
               class="inline-flex items-center gap-2 mt-3 px-4 py-2 rounded-xl
                      bg-[#186369] text-white text-xs font-bold hover:opacity-90 transition">
                Buka PDF di tab baru &rarr;
            </a>
        @elseif ($article->source_url)
            <a href="{{ $article->source_url }}" target="_blank" rel="noopener"
               class="inline-flex items-center gap-2 mt-6 px-4 py-2 rounded-xl
                      bg-[#186369] text-white text-xs font-bold hover:opacity-90 transition">
                Buka sumber artikel &rarr;
            </a>
        @elseif ($article->thumbnail_url)
            <img src="{{ $article->thumbnail_url }}" alt="{{ $article->title }}"
                 class="w-full max-h-96 object-cover rounded-2xl mt-6">
        @endif

        <div class="mt-6 text-sm leading-7 text-gray-700 whitespace-pre-line">{{ $article->content }}</div>
    </article>
@endsection