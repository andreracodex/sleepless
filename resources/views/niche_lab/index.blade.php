@extends('layouts.app')

@section('title', 'Niche Lab — YouTube Auto Content Studio')

@section('header_title', 'Phase 1: Niche Lab & CollabPals Directory')
@section('header_subtitle', 'Eksplorasi 200+ Niche YouTube, Skor CollabPals Aktual & Pemilihan Channel Pilot')

@section('header_actions')
    <a href="{{ route('ideas.index') }}" class="text-xs px-3.5 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-semibold transition flex items-center gap-1.5 shadow">
        <span>💡 Idea Board →</span>
    </a>
    <a href="https://www.collabpals.com/tools/youtube-niche-finder" target="_blank" class="text-xs px-3 py-1.5 rounded-lg bg-slate-800 text-slate-300 hover:text-white hover:bg-slate-700 transition flex items-center gap-1.5 border border-slate-700">
        <span>Source: CollabPals</span>
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
    </a>
@endsection

@section('content')

    <!-- Active Pilot Status Banner -->
    <div class="rounded-2xl p-6 bg-gradient-to-r from-slate-900 via-indigo-950/40 to-slate-900 border border-indigo-500/20 shadow-xl relative overflow-hidden">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2 max-w-3xl">
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 text-xs font-bold uppercase tracking-wider rounded-md bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                        PRD Section 3.4 — Decision Engine
                    </span>
                    @php
                        $selectedSnap = $selectedNiche->latestVerifiedSnapshot ?? $selectedNiche->latestSnapshot;
                    @endphp
                    <span class="text-xs px-2.5 py-0.5 rounded-md bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-semibold">
                        Niche Pilot Terpilih: {{ $selectedNiche->name }} (Skor {{ $selectedSnap?->niche_score ?? '-' }})
                    </span>
                </div>
                <h2 class="text-xl font-extrabold text-white">Status Channel Pilot Aktif</h2>
                <p class="text-sm text-slate-300 leading-relaxed">
                    Konten kreator dapat memilih niche pilot dari katalog 200+ data CollabPals di bawah. Seluruh pipeline otomatisasi (Ideation, Research Dossier, Script, dan Rendering) akan diselaraskan dengan niche yang dipilih.
                </p>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                <div class="px-4 py-3 rounded-xl bg-emerald-500/20 border border-emerald-500 text-emerald-300 text-center font-bold text-sm flex items-center justify-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>⭐ Pilot Aktif: {{ $selectedNiche->name }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- SECTION: BROWSE & SELECT 200+ NICHES -->
    <div class="bg-slate-900/90 rounded-2xl border border-slate-800 p-6 space-y-6 shadow-xl">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-800 pb-5">
            <div>
                <h3 class="text-lg font-extrabold text-white flex items-center gap-2">
                    <span>📚 Katalog Niche YouTube (CollabPals 2026 Directory)</span>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                        {{ $allNiches->total() }} Niche Tersedia
                    </span>
                </h3>
                <p class="text-xs text-slate-400 mt-1">
                    Cari dan bandingkan skor potensi, CPM, kompetisi, dan format video. Klik <strong>"Pilih Niche Ini"</strong> untuk mengaktifkan sebagai pilot channel studio.
                </p>
            </div>

            <!-- Total count & Per Page -->
            <div class="flex items-center gap-2 text-xs text-slate-400">
                <span>Tampilkan per halaman:</span>
                <a href="{{ request()->fullUrlWithQuery(['per_page' => 24, 'page' => 1]) }}" class="px-2 py-1 rounded {{ request('per_page', 24) == 24 ? 'bg-indigo-600 text-white font-bold' : 'bg-slate-800 text-slate-300 hover:text-white' }}">24</a>
                <a href="{{ request()->fullUrlWithQuery(['per_page' => 48, 'page' => 1]) }}" class="px-2 py-1 rounded {{ request('per_page') == 48 ? 'bg-indigo-600 text-white font-bold' : 'bg-slate-800 text-slate-300 hover:text-white' }}">48</a>
                <a href="{{ request()->fullUrlWithQuery(['per_page' => 'all', 'page' => 1]) }}" class="px-2 py-1 rounded {{ request('per_page') == 'all' ? 'bg-indigo-600 text-white font-bold' : 'bg-slate-800 text-slate-300 hover:text-white' }}">Semua (200+)</a>
            </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <form method="GET" action="{{ route('niches.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <!-- Search Keyword -->
            <div class="lg:col-span-2">
                <label class="block text-[11px] font-semibold text-slate-400 mb-1">Cari Niche / Topik</label>
                <div class="relative">
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="Contoh: Cybersecurity, Finance, AI, Sleep, Golf..." 
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500"
                    >
                    @if(request('search'))
                        <a href="{{ route('niches.index') }}" class="absolute right-3 top-2.5 text-xs text-slate-400 hover:text-white">✕</a>
                    @endif
                </div>
            </div>

            <!-- Category Filter -->
            <div>
                <label class="block text-[11px] font-semibold text-slate-400 mb-1">Kategori</label>
                <select name="category" onchange="this.form.submit()" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Faceless Filter -->
            <div>
                <label class="block text-[11px] font-semibold text-slate-400 mb-1">Format Faceless</label>
                <select name="faceless" onchange="this.form.submit()" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                    <option value="">Semua Format</option>
                    <option value="1" {{ request('faceless') === '1' ? 'selected' : '' }}>✓ Khusus Faceless</option>
                    <option value="0" {{ request('faceless') === '0' ? 'selected' : '' }}>Non-Faceless (On-camera)</option>
                </select>
            </div>

            <!-- Sort By -->
            <div>
                <label class="block text-[11px] font-semibold text-slate-400 mb-1">Urutkan</label>
                <div class="flex gap-2">
                    <select name="sort" onchange="this.form.submit()" class="flex-1 bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                        <option value="score_desc" {{ request('sort', 'score_desc') === 'score_desc' ? 'selected' : '' }}>Skor Tertinggi ↓</option>
                        <option value="score_asc" {{ request('sort') === 'score_asc' ? 'selected' : '' }}>Skor Terendah ↑</option>
                        <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Nama Niche A-Z</option>
                    </select>
                    <button type="submit" class="px-3 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs transition">
                        Cari
                    </button>
                </div>
            </div>
        </form>

        <!-- Niches Grid Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @forelse($allNiches as $niche)
                @php
                    $snap = $niche->latestVerifiedSnapshot ?? $niche->latestSnapshot;
                    $score = $snap?->niche_score;
                    $category = $niche->audience_profile['category'] ?? 'General';
                    $isSelected = ($niche->status === 'selected');
                @endphp
                <div class="rounded-2xl border @if($isSelected) border-emerald-500 bg-slate-900 shadow-lg shadow-emerald-500/10 ring-1 ring-emerald-500/50 @else border-slate-800/80 bg-slate-950/60 hover:border-slate-700 @endif p-5 flex flex-col justify-between transition-all group">
                    <div class="space-y-3">
                        <!-- Top Badges: Category & Score -->
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-slate-800 text-slate-300 border border-slate-700">
                                {{ $category }}
                            </span>

                            @if($score)
                                <div class="flex items-center gap-1.5">
                                    <span class="text-[10px] text-slate-400 font-medium">Skor CollabPals</span>
                                    <span class="text-sm font-extrabold px-2 py-0.5 rounded-lg @if($score >= 80) bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 @elseif($score >= 70) bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 @else bg-amber-500/20 text-amber-400 border border-amber-500/30 @endif">
                                        {{ $score }}
                                    </span>
                                </div>
                            @else
                                <span class="text-[10px] text-slate-500 italic">Unverified</span>
                            @endif
                        </div>

                        <!-- Niche Name -->
                        <div>
                            <h4 class="text-base font-bold text-white group-hover:text-indigo-400 transition leading-snug">
                                {{ $niche->name }}
                            </h4>
                            @if(!empty($niche->audience_profile['example_channels']))
                                <p class="text-[11px] text-slate-400 line-clamp-1 mt-0.5">
                                    <span class="text-slate-500 font-medium">Contoh:</span> {{ $niche->audience_profile['example_channels'] }}
                                </p>
                            @endif
                        </div>

                        <!-- Metrics Grid -->
                        <div class="grid grid-cols-2 gap-2 text-[11px] pt-1">
                            <div class="p-2 rounded-lg bg-slate-900/80 border border-slate-800/80">
                                <span class="text-[10px] text-slate-500 block">Estimasi CPM</span>
                                <strong class="text-emerald-400 font-semibold">{{ $snap?->cpm_range ?? '$5-$15' }}</strong>
                            </div>
                            <div class="p-2 rounded-lg bg-slate-900/80 border border-slate-800/80">
                                <span class="text-[10px] text-slate-500 block">Kompetisi</span>
                                <strong class="text-slate-200 font-semibold">{{ $snap?->competition_score ? $snap->competition_score : ($snap?->notes && str_contains($snap->notes, 'Low') ? 'Low' : 'Low') }}</strong>
                            </div>
                            <div class="p-2 rounded-lg bg-slate-900/80 border border-slate-800/80">
                                <span class="text-[10px] text-slate-500 block">Format Faceless</span>
                                @if($snap?->faceless_friendly)
                                    <strong class="text-emerald-400 font-semibold flex items-center gap-1">✓ Ya (Faceless)</strong>
                                @else
                                    <strong class="text-slate-400 font-medium">On-Camera</strong>
                                @endif
                            </div>
                            <div class="p-2 rounded-lg bg-slate-900/80 border border-slate-800/80">
                                <span class="text-[10px] text-slate-500 block">Tren</span>
                                <strong class="text-cyan-400 font-semibold">📈 {{ $snap?->growth_trend ?? 'Rising' }}</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <div class="mt-4 pt-3 border-t border-slate-800/80">
                        @if($isSelected)
                            <div class="w-full py-2 px-3 rounded-xl bg-emerald-500/20 text-emerald-300 font-bold text-xs border border-emerald-500/40 flex items-center justify-center gap-1.5 shadow-sm">
                                <span>⭐ Channel Pilot Aktif</span>
                            </div>
                        @else
                            <form action="{{ route('niches.select', $niche->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full py-2 px-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md hover:shadow-indigo-500/20 transition flex items-center justify-center gap-1.5">
                                    <span>🎯 Pilih Niche Ini</span>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center text-slate-400 space-y-3">
                    <p class="text-base font-semibold text-slate-300">Tidak ada niche yang cocok dengan pencarian "{{ request('search') }}".</p>
                    <a href="{{ route('niches.index') }}" class="text-xs text-indigo-400 hover:text-indigo-300 underline font-semibold">
                        Reset Filter dan Tampilkan Semua Niche
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Pagination Links -->
        <div class="pt-4 border-t border-slate-800 flex items-center justify-between">
            <div class="text-xs text-slate-400">
                Menampilkan <strong>{{ $allNiches->firstItem() ?? 0 }}</strong> sampai <strong>{{ $allNiches->lastItem() ?? 0 }}</strong> dari <strong>{{ $allNiches->total() }}</strong> Niche
            </div>
            <div>
                {{ $allNiches->links() }}
            </div>
        </div>
    </div>

    <!-- SECTION: COMPARATIVE GRID: EV VS SLEEP CONTENT (PRD REQUIREMENT) -->
    <div class="bg-slate-900/90 rounded-2xl border border-slate-800 p-6 space-y-6 shadow-xl">
        <div class="border-b border-slate-800 pb-4">
            <h3 class="text-lg font-extrabold text-white flex items-center gap-2">
                <span>⚖️ Head-to-Head Comparison & Evaluasi Internal (PRD Section 3.4)</span>
            </h3>
            <p class="text-xs text-slate-400 mt-1">
                Perbandingan mendalam dua niche rujukan PRD: Electric Vehicles (EV) vs Sleep Content dengan formula Lapisan B ($0.40 \times \text{niche} + 0.20 \times \text{feasibility} + \dots$).
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            <!-- Card 1: Electric Vehicles (EV) -->
            @php
                $evSnap = $ev->latestVerifiedSnapshot ?? $ev->latestSnapshot;
                $evEval = $ev->latestEvaluation;
            @endphp
            <div class="bg-slate-950/80 rounded-2xl border @if($ev->status === 'selected') border-emerald-500 shadow-emerald-500/10 @else border-slate-800 @endif shadow-xl p-6 flex flex-col justify-between space-y-6">
                <div>
                    <!-- Header -->
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-2xl">⚡</span>
                                <h3 class="text-xl font-bold text-white">{{ $ev->name }}</h3>
                            </div>
                            <p class="text-xs text-slate-400 mt-1">{{ $ev->description }}</p>
                        </div>
                        @if($ev->status === 'selected')
                            <span class="px-3 py-1 text-xs font-bold rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/40">SELECTED PILOT</span>
                        @else
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-slate-800 text-slate-300">CANDIDATE</span>
                        @endif
                    </div>

                    <!-- CollabPals Score Badge -->
                    <div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="bg-slate-900 rounded-xl p-3 border border-slate-800 text-center">
                            <span class="text-[10px] text-slate-400 uppercase font-medium">CollabPals Score</span>
                            <div class="text-3xl font-extrabold text-indigo-400 mt-1">
                                {{ $evSnap?->niche_score ?? 'null' }}
                            </div>
                            <span class="text-[10px] text-slate-500">Skor 0–100</span>
                        </div>
                        <div class="bg-slate-900 rounded-xl p-3 border border-slate-800 text-center">
                            <span class="text-[10px] text-slate-400 uppercase font-medium">CPM Range</span>
                            <div class="text-lg font-bold text-emerald-400 mt-2">
                                {{ $evSnap?->cpm_range ?? 'null' }}
                            </div>
                            <span class="text-[10px] text-slate-500">Automotive</span>
                        </div>
                        <div class="bg-slate-900 rounded-xl p-3 border border-slate-800 text-center">
                            <span class="text-[10px] text-slate-400 uppercase font-medium">Kompetisi</span>
                            <div class="text-lg font-bold text-slate-200 mt-2">
                                Low
                            </div>
                            <span class="text-[10px] text-slate-500">CollabPals</span>
                        </div>
                        <div class="bg-slate-900 rounded-xl p-3 border border-slate-800 text-center">
                            <span class="text-[10px] text-slate-400 uppercase font-medium">Status Data</span>
                            <div class="text-xs font-bold uppercase mt-2.5 @if($evSnap?->verification_status === 'verified') text-emerald-400 @else text-amber-400 @endif">
                                {{ $evSnap?->verification_status ?? 'UNVERIFIED' }}
                            </div>
                            <span class="text-[10px] text-slate-500">{{ $evSnap ? $evSnap->captured_at->diffForHumans() : 'Belum captured' }}</span>
                        </div>
                    </div>

                    <!-- Internal Evaluation (Lapisan B) -->
                    <div class="mt-6 bg-slate-900 rounded-xl p-4 border border-slate-800 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-300">Internal Score (Lapisan B):</span>
                            @if($evEval)
                                <span class="text-sm font-extrabold text-indigo-400">{{ $evEval->internal_score }} / 100</span>
                            @else
                                <span class="text-xs text-slate-500 italic">Belum dievaluasi</span>
                            @endif
                        </div>
                        @if($evEval)
                            <div class="grid grid-cols-2 gap-2 text-xs text-slate-400">
                                <div>Feasibility: <strong class="text-slate-200">{{ $evEval->feasibility_score }}</strong></div>
                                <div>Audience Validation: <strong class="text-slate-200">{{ $evEval->audience_validation_score }}</strong></div>
                                <div>Production Econ: <strong class="text-slate-200">{{ $evEval->production_economics_score }}</strong></div>
                                <div>Monetization Div: <strong class="text-slate-200">{{ $evEval->monetization_diversification_score }}</strong></div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="pt-4 border-t border-slate-800 flex flex-wrap gap-2 justify-between items-center">
                    <div class="flex gap-2">
                        <form action="{{ route('niches.capture', $ev->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-medium transition flex items-center gap-1 border border-slate-700">
                                <span>🔄 Live Scrape</span>
                            </button>
                        </form>
                        <button type="button" onclick="openEvalModal({{ $ev->id }}, '{{ $ev->name }}', {{ $evSnap?->id ?? 'null' }})" class="px-3 py-1.5 rounded-lg bg-indigo-600/30 hover:bg-indigo-600/50 text-indigo-300 text-xs font-medium transition border border-indigo-500/30">
                            ⚙️ Evaluasi Lapisan B
                        </button>
                    </div>

                    @if($ev->status !== 'selected')
                        <form action="{{ route('niches.select', $ev->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg transition">
                                ✓ Pilih EV sebagai Pilot
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Card 2: Sleep Content -->
            @php
                $sleepSnap = $sleep->latestVerifiedSnapshot ?? $sleep->latestSnapshot;
                $sleepEval = $sleep->latestEvaluation;
            @endphp
            <div class="bg-slate-950/80 rounded-2xl border @if($sleep->status === 'selected') border-emerald-500 shadow-emerald-500/10 @else border-slate-800 @endif shadow-xl p-6 flex flex-col justify-between space-y-6">
                <div>
                    <!-- Header -->
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-2xl">🌙</span>
                                <h3 class="text-xl font-bold text-white">{{ $sleep->name }}</h3>
                            </div>
                            <p class="text-xs text-slate-400 mt-1">{{ $sleep->description }}</p>
                        </div>
                        @if($sleep->status === 'selected')
                            <span class="px-3 py-1 text-xs font-bold rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/40">SELECTED PILOT</span>
                        @else
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-slate-800 text-slate-300">CANDIDATE</span>
                        @endif
                    </div>

                    <!-- CollabPals Score Badge -->
                    <div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="bg-slate-900 rounded-xl p-3 border border-slate-800 text-center">
                            <span class="text-[10px] text-slate-400 uppercase font-medium">CollabPals Score</span>
                            <div class="text-3xl font-extrabold text-indigo-400 mt-1">
                                {{ $sleepSnap?->niche_score ?? 'null' }}
                            </div>
                            <span class="text-[10px] text-slate-500">Skor 0–100</span>
                        </div>
                        <div class="bg-slate-900 rounded-xl p-3 border border-slate-800 text-center">
                            <span class="text-[10px] text-slate-400 uppercase font-medium">CPM Range</span>
                            <div class="text-lg font-bold text-emerald-400 mt-2">
                                {{ $sleepSnap?->cpm_range ?? 'null' }}
                            </div>
                            <span class="text-[10px] text-slate-500">ASMR / Music</span>
                        </div>
                        <div class="bg-slate-900 rounded-xl p-3 border border-slate-800 text-center">
                            <span class="text-[10px] text-slate-400 uppercase font-medium">Kompetisi</span>
                            <div class="text-lg font-bold text-slate-200 mt-2">
                                Low
                            </div>
                            <span class="text-[10px] text-slate-500">CollabPals</span>
                        </div>
                        <div class="bg-slate-900 rounded-xl p-3 border border-slate-800 text-center">
                            <span class="text-[10px] text-slate-400 uppercase font-medium">Status Data</span>
                            <div class="text-xs font-bold uppercase mt-2.5 @if($sleepSnap?->verification_status === 'verified') text-emerald-400 @else text-amber-400 @endif">
                                {{ $sleepSnap?->verification_status ?? 'UNVERIFIED' }}
                            </div>
                            <span class="text-[10px] text-slate-500">{{ $sleepSnap ? $sleepSnap->captured_at->diffForHumans() : 'Belum captured' }}</span>
                        </div>
                    </div>

                    <!-- Internal Evaluation (Lapisan B) -->
                    <div class="mt-6 bg-slate-900 rounded-xl p-4 border border-slate-800 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-300">Internal Score (Lapisan B):</span>
                            @if($sleepEval)
                                <span class="text-sm font-extrabold text-indigo-400">{{ $sleepEval->internal_score }} / 100</span>
                            @else
                                <span class="text-xs text-slate-500 italic">Belum dievaluasi</span>
                            @endif
                        </div>
                        @if($sleepEval)
                            <div class="grid grid-cols-2 gap-2 text-xs text-slate-400">
                                <div>Feasibility: <strong class="text-slate-200">{{ $sleepEval->feasibility_score }}</strong></div>
                                <div>Audience Validation: <strong class="text-slate-200">{{ $sleepEval->audience_validation_score }}</strong></div>
                                <div>Production Econ: <strong class="text-slate-200">{{ $sleepEval->production_economics_score }}</strong></div>
                                <div>Monetization Div: <strong class="text-slate-200">{{ $sleepEval->monetization_diversification_score }}</strong></div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="pt-4 border-t border-slate-800 flex flex-wrap gap-2 justify-between items-center">
                    <div class="flex gap-2">
                        <form action="{{ route('niches.capture', $sleep->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-medium transition flex items-center gap-1 border border-slate-700">
                                <span>🔄 Live Scrape</span>
                            </button>
                        </form>
                        <button type="button" onclick="openEvalModal({{ $sleep->id }}, '{{ $sleep->name }}', {{ $sleepSnap?->id ?? 'null' }})" class="px-3 py-1.5 rounded-lg bg-indigo-600/30 hover:bg-indigo-600/50 text-indigo-300 text-xs font-medium transition border border-indigo-500/30">
                            ⚙️ Evaluasi Lapisan B
                        </button>
                    </div>

                    @if($sleep->status !== 'selected')
                        <form action="{{ route('niches.select', $sleep->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg transition">
                                ✓ Pilih Sleep sebagai Pilot
                            </button>
                        </form>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <!-- SECTION: RECENT SNAPSHOTS & AUDIT LOG -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Snapshots -->
        <div class="bg-slate-900/90 rounded-2xl border border-slate-800 p-6 space-y-4 shadow-xl">
            <h3 class="text-base font-bold text-white flex items-center gap-2">
                <span>📸 Snapshot History (Evidence Trail)</span>
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="text-[10px] uppercase text-slate-400 bg-slate-950/80 border-b border-slate-800">
                        <tr>
                            <th class="py-2.5 px-3">Waktu</th>
                            <th class="py-2.5 px-3">Niche</th>
                            <th class="py-2.5 px-3">Skor</th>
                            <th class="py-2.5 px-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 font-mono">
                        @forelse($recentSnapshots as $snap)
                            <tr class="hover:bg-slate-800/40">
                                <td class="py-2 px-3 text-slate-400">{{ $snap->captured_at->format('d M H:i') }}</td>
                                <td class="py-2 px-3 font-sans font-semibold text-white">{{ $snap->nicheCandidate->name ?? '-' }}</td>
                                <td class="py-2 px-3 text-indigo-400 font-bold">{{ $snap->niche_score ?? '-' }}</td>
                                <td class="py-2 px-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] @if($snap->verification_status === 'verified') bg-emerald-500/20 text-emerald-400 @else bg-amber-500/20 text-amber-400 @endif">
                                        {{ $snap->verification_status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-slate-500 italic">Belum ada snapshot skor tersimpan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Audit Log -->
        <div class="bg-slate-900/90 rounded-2xl border border-slate-800 p-6 space-y-4 shadow-xl">
            <h3 class="text-base font-bold text-white flex items-center gap-2">
                <span>🛡️ Audit Log & Kebijakan Gate PRD</span>
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="text-[10px] uppercase text-slate-400 bg-slate-950/80 border-b border-slate-800">
                        <tr>
                            <th class="py-2.5 px-3">Waktu</th>
                            <th class="py-2.5 px-3">Aksi</th>
                            <th class="py-2.5 px-3">Aktor</th>
                            <th class="py-2.5 px-3">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 font-mono">
                        @forelse($auditLogs as $log)
                            <tr class="hover:bg-slate-800/40">
                                <td class="py-2 px-3 text-slate-400">{{ $log->created_at->format('d M H:i') }}</td>
                                <td class="py-2 px-3 text-indigo-300 font-semibold font-sans">{{ $log->action }}</td>
                                <td class="py-2 px-3 text-slate-400">{{ $log->actor_type }}</td>
                                <td class="py-2 px-3 text-slate-400 font-sans truncate max-w-xs">{{ json_encode($log->after_state) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-slate-500 italic">Belum ada audit log tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal: Input Evaluasi Lapisan B -->
    <div id="evalModal" class="hidden fixed inset-0 z-50 bg-black/75 flex items-center justify-center p-4">
        <div class="bg-slate-900 rounded-2xl border border-slate-800 max-w-md w-full p-6 space-y-4 shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-base font-bold text-white">Evaluasi Internal Lapisan B</h3>
                <button onclick="document.getElementById('evalModal').classList.add('hidden')" class="text-slate-400 hover:text-white">✕</button>
            </div>
            <p class="text-xs text-slate-400">Menilai kandidat <strong id="evalCandidateName" class="text-white"></strong> berdasarkan bobot PRD Section 3.4.</p>

            <form action="" id="evalForm" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="snapshot_id" id="evalSnapshotId">

                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-300 flex justify-between">
                        <span>Production Feasibility (Bobot 20%)</span>
                        <span id="feasVal" class="text-indigo-400">80</span>
                    </label>
                    <input type="range" min="0" max="100" value="80" name="feasibility_score" oninput="document.getElementById('feasVal').innerText=this.value" class="w-full accent-indigo-500">
                    <p class="text-[10px] text-slate-400">Kemampuan AI & tools menghasilkan visual 1080p tanpa footage mahal.</p>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-300 flex justify-between">
                        <span>Audience Validation (Bobot 20%)</span>
                        <span id="audVal" class="text-indigo-400">75</span>
                    </label>
                    <input type="range" min="0" max="100" value="75" name="audience_validation_score" oninput="document.getElementById('audVal').innerText=this.value" class="w-full accent-indigo-500">
                    <p class="text-[10px] text-slate-400">Tingkat pencarian YouTube, interaksi komentar, dan pertanyaan berulang.</p>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-300 flex justify-between">
                        <span>Production Economics (Bobot 10%)</span>
                        <span id="econVal" class="text-indigo-400">70</span>
                    </label>
                    <input type="range" min="0" max="100" value="70" name="production_economics_score" oninput="document.getElementById('econVal').innerText=this.value" class="w-full accent-indigo-500">
                    <p class="text-[10px] text-slate-400">Efisiensi biaya dan waktu rendering per video.</p>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-300 flex justify-between">
                        <span>Monetization Diversification (Bobot 10%)</span>
                        <span id="divVal" class="text-indigo-400">80</span>
                    </label>
                    <input type="range" min="0" max="100" value="80" name="monetization_diversification_score" oninput="document.getElementById('divVal').innerText=this.value" class="w-full accent-indigo-500">
                    <p class="text-[10px] text-slate-400">Peluang affiliate, sponsor brand, merchandise, selain AdSense.</p>
                </div>

                <div class="pt-4 border-t border-slate-800 flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('evalModal').classList.add('hidden')" class="px-4 py-2 rounded-lg bg-slate-800 text-slate-300 text-xs">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs">Simpan Evaluasi</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEvalModal(candidateId, candidateName, snapshotId) {
            if (!snapshotId) {
                alert('Belum ada snapshot skor untuk kandidat ini. Silakan jalankan live capture terlebih dahulu.');
                return;
            }
            document.getElementById('evalCandidateName').innerText = candidateName;
            document.getElementById('evalSnapshotId').value = snapshotId;
            document.getElementById('evalForm').action = '/niches/' + candidateId + '/evaluate';
            document.getElementById('evalModal').classList.remove('hidden');
        }
    </script>
@endsection
