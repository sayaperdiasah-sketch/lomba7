@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    {{-- ─── HEADER ─── --}}
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start
                gap-3 flex-shrink-0">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-[#1e4d4a]">Halo, Putranda!</h1>
            <p class="text-xs text-[#1e4d4a]/70 mt-0.5">Selamat datang di mindo</p>
        </div>

        {{-- Search Bar --}}
        <div class="relative w-full sm:w-64">
            <!-- GANTI ICON SEARCH DI SINI -->
            <div class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 img-slot rounded"></div>
            <input type="text" placeholder="Cari fitur, topik, atau kata kunci"
                   class="w-full pl-9 pr-3 py-2 text-[11px] bg-white border border-gray-300
                          rounded-full focus:outline-none focus:ring-2 focus:ring-[#2d7a7a]">
        </div>
    </div>

    {{-- ─── ROW 1: Cek Mental + Blue Rectangle ─── --}}
    <div class="grid grid-cols-1 md:grid-cols-[1.4fr_1fr] gap-4 flex-shrink-0">

        {{-- Cek Mental Yuk --}}
        <div class="bg-[#2d7a7a] rounded-2xl p-4 sm:p-5 relative overflow-hidden
                    text-white min-h-[140px]">
            <h3 class="font-bold text-base">Cek Mental Yuk</h3>
            <p class="text-[11px] mt-1 opacity-90 max-w-[60%] leading-snug">
                Mari Kenali Gejala yang sedang kamu alami sekarang
            </p>

            {{-- Icon plus pojok kiri bawah --}}
            <div class="absolute bottom-4 left-4 sm:left-5 w-7 h-7 bg-white/20 rounded-lg
                        flex items-center justify-center">
                <!-- GANTI ICON PLUS DI SINI -->
                <div class="w-4 h-4 img-slot rounded"></div>
            </div>

            {{-- Ilustrasi kanan --}}
            <!-- GANTI ILUSTRASI CEK MENTAL DI SINI -->
            <div class="absolute right-2 bottom-2 w-24 sm:w-28 h-20 sm:h-24
                        rounded-2xl img-slot"></div>
        </div>

        {{-- Blue Rectangle --}}
        <!-- GANTI GAMBAR DI SINI -->
        <div class="bg-[#a5d4e8] rounded-2xl min-h-[140px] img-slot"></div>

    </div>

    {{-- ─── ROW 2: 3 Gray Cards ─── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 flex-shrink-0">
        <!-- GANTI KONTEN CARD 1 -->
        <div class="bg-[#a8b8b8] rounded-2xl h-20 sm:h-24"></div>
        <!-- GANTI KONTEN CARD 2 -->
        <div class="bg-[#a8b8b8] rounded-2xl h-20 sm:h-24"></div>
        <!-- GANTI KONTEN CARD 3 -->
        <div class="bg-[#a8b8b8] rounded-2xl h-20 sm:h-24"></div>
    </div>

    {{-- ─── ROW 3: Big Teal + Small Teal ─── --}}
    <div class="grid grid-cols-1 md:grid-cols-[2.5fr_1fr] gap-4 flex-1 min-h-[120px]">
        <!-- GANTI KONTEN BIG TEAL CARD -->
        <div class="bg-[#2d7a7a] rounded-2xl"></div>
        <!-- GANTI KONTEN SMALL TEAL CARD -->
        <div class="bg-[#2d7a7a] rounded-2xl"></div>
    </div>

@endsection