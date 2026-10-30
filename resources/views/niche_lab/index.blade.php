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
                    Pilih niche pilot dari katalog 200+ data CollabPals (termasuk Electric Vehicles dan Sleep Content) dalam bentuk <strong>Tabel</strong> atau <strong>Grid</strong> di bawah. Seluruh pipeline otomatisasi konten studio akan diselaraskan dengan niche yang Anda pilih.
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
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-slate-800 pb-5">
            <div>
                <h3 class="text-lg font-extrabold text-white flex items-center gap-2">
                    <span>📚 Katalog Niche YouTube (CollabPals 2026 Directory)</span>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                        {{ $allNiches->total() }} Niche Tersedia
                    </span>
                </h3>
                <p class="text-xs text-slate-400 mt-1">
                    Pilih niche pilot channel studio langsung dari tabel atau ubah tampilan ke kartu grid.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <!-- VIEW SWITCHER: TABLE VS GRID -->
                <div class="flex items-center gap-1 p-1 bg-slate-950 rounded-xl border border-slate-800">
                    <button 
                        type="button" 
                        onclick="switchView('table')" 
                        id="btnViewTable" 
                        class="px-3 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-1.5 transition bg-indigo-600 text-white shadow-sm"
                        title="Tampilan Tabel"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18M3 6h18M3 18h18"/></svg>
                        <span>Tabel</span>
                    </button>
                    <button 
                        type="button" 
                        onclick="switchView('grid')" 
                        id="btnViewGrid" 
                        class="px-3 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-1.5 transition text-slate-400 hover:text-white"
                        title="Tampilan Grid"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        <span>Grid</span>
                    </button>
                </div>

                <!-- Total count & Per Page -->
                <div class="flex items-center gap-1.5 text-xs text-slate-400 bg-slate-950 px-2.5 py-1 rounded-xl border border-slate-800">
                    <span class="hidden sm:inline">Per hal:</span>
                    <a href="{{ request()->fullUrlWithQuery(['per_page' => 24, 'page' => 1]) }}" class="px-2 py-0.5 rounded {{ request('per_page', 24) == 24 ? 'bg-indigo-600 text-white font-bold' : 'text-slate-400 hover:text-white' }}">24</a>
                    <a href="{{ request()->fullUrlWithQuery(['per_page' => 48, 'page' => 1]) }}" class="px-2 py-0.5 rounded {{ request('per_page') == 48 ? 'bg-indigo-600 text-white font-bold' : 'text-slate-400 hover:text-white' }}">48</a>
                    <a href="{{ request()->fullUrlWithQuery(['per_page' => 'all', 'page' => 1]) }}" class="px-2 py-0.5 rounded {{ request('per_page') == 'all' ? 'bg-indigo-600 text-white font-bold' : 'text-slate-400 hover:text-white' }}">Semua</a>
                </div>
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

        <!-- VIEW 1: TABLE VIEW (DEFAULT) -->
        <div id="tableView" class="overflow-x-auto rounded-xl border border-slate-800">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="text-[10px] uppercase tracking-wider text-slate-400 bg-slate-950/90 border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Pilih / Status</th>
                        <th class="py-3 px-4">Nama Niche & Channel Referensi</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4 text-center">Skor CollabPals</th>
                        <th class="py-3 px-4">Estimasi CPM</th>
                        <th class="py-3 px-4">Kompetisi</th>
                        <th class="py-3 px-4">Faceless</th>
                        <th class="py-3 px-4">Tren</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 font-sans">
                    @forelse($allNiches as $niche)
                        @php
                            $snap = $niche->latestVerifiedSnapshot ?? $niche->latestSnapshot;
                            $score = $snap?->niche_score;
                            $category = $niche->audience_profile['category'] ?? 'General';
                            $isSelected = ($niche->status === 'selected');
                        @endphp
                        <tr class="transition-colors @if($isSelected) bg-emerald-500/10 hover:bg-emerald-500/15 @else hover:bg-slate-800/40 @endif">
                            <!-- Action / Status Button -->
                            <td class="py-3 px-4 whitespace-nowrap">
                                @if($isSelected)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 text-[11px] font-bold shadow-sm">
                                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                        <span>⭐ Pilot Aktif</span>
                                    </span>
                                @else
                                    <form action="{{ route('niches.select', $niche->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow transition">
                                            <span>🎯 Pilih Niche</span>
                                        </button>
                                    </form>
                                @endif
                            </td>

                            <!-- Niche Name & Description -->
                            <td class="py-3 px-4">
                                <div class="font-bold text-white text-sm flex items-center gap-2">
                                    <span>{{ $niche->name }}</span>
                                    @if($isSelected)
                                        <span class="px-1.5 py-0.2 rounded bg-emerald-500/30 text-emerald-300 text-[9px] font-extrabold uppercase">ACTIVE</span>
                                    @endif
                                </div>
                                @if(!empty($niche->audience_profile['example_channels']))
                                    <div class="text-[11px] text-slate-400 mt-0.5 line-clamp-1">
                                        <span class="text-slate-500 font-medium">Contoh:</span> {{ $niche->audience_profile['example_channels'] }}
                                    </div>
                                @endif
                            </td>

                            <!-- Category -->
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-slate-800 text-slate-300 border border-slate-700">
                                    {{ $category }}
                                </span>
                            </td>

                            <!-- CollabPals Score -->
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                @if($score)
                                    <span class="text-sm font-extrabold px-2.5 py-1 rounded-lg inline-block @if($score >= 80) bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 @elseif($score >= 70) bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 @else bg-amber-500/20 text-amber-400 border border-amber-500/30 @endif">
                                        {{ $score }}
                                    </span>
                                @else
                                    <span class="text-xs text-slate-500 italic">Unverified</span>
                                @endif
                            </td>

                            <!-- CPM Range -->
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="font-semibold text-emerald-400 font-mono">{{ $snap?->cpm_range ?? '$5-$15' }}</span>
                            </td>

                            <!-- Competition -->
                            <td class="py-3 px-4 whitespace-nowrap text-slate-300">
                                <span class="px-2 py-0.5 rounded bg-slate-900 border border-slate-800 text-[11px]">
                                    Low
                                </span>
                            </td>

                            <!-- Faceless Friendly -->
                            <td class="py-3 px-4 whitespace-nowrap">
                                @if($snap?->faceless_friendly)
                                    <span class="px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-400 text-[11px] font-semibold">
                                        ✓ Ya (Faceless)
                                    </span>
                                @else
                                    <span class="text-slate-400 text-[11px]">
                                        On-Camera
                                    </span>
                                @endif
                            </td>

                            <!-- Trend -->
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="text-cyan-400 font-semibold text-[11px]">
                                    📈 {{ $snap?->growth_trend ?? 'Rising' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400">
                                Tidak ada niche yang cocok dengan pencarian "{{ request('search') }}".
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- VIEW 2: GRID VIEW (ALTERNATIF) -->
        <div id="gridView" class="hidden grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
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
                                <strong class="text-slate-200 font-semibold">Low</strong>
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

    <!-- Toggle View & Modal Script -->
    <script>
        function switchView(mode) {
            const tableView = document.getElementById('tableView');
            const gridView = document.getElementById('gridView');
            const btnTable = document.getElementById('btnViewTable');
            const btnGrid = document.getElementById('btnViewGrid');

            if (mode === 'grid') {
                tableView.classList.add('hidden');
                gridView.classList.remove('hidden');

                btnGrid.classList.add('bg-indigo-600', 'text-white', 'shadow-sm');
                btnGrid.classList.remove('text-slate-400');

                btnTable.classList.remove('bg-indigo-600', 'text-white', 'shadow-sm');
                btnTable.classList.add('text-slate-400');

                localStorage.setItem('niche_view_mode', 'grid');
            } else {
                gridView.classList.add('hidden');
                tableView.classList.remove('hidden');

                btnTable.classList.add('bg-indigo-600', 'text-white', 'shadow-sm');
                btnTable.classList.remove('text-slate-400');

                btnGrid.classList.remove('bg-indigo-600', 'text-white', 'shadow-sm');
                btnGrid.classList.add('text-slate-400');

                localStorage.setItem('niche_view_mode', 'table');
            }
        }

        // Initialize saved view preference on page load
        document.addEventListener('DOMContentLoaded', function () {
            const saved = localStorage.getItem('niche_view_mode') || 'table';
            switchView(saved);
        });
    </script>
@endsection
