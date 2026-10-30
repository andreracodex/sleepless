@extends('layouts.app')

@section('title', 'Niche Lab — YouTube Auto Content Studio')

@section('header_title', 'Phase 1: Niche Lab')
@section('header_subtitle', 'Electric Vehicles vs Sleep Content • CollabPals Niche Score–Driven')

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

    <!-- Decision Engine Banner -->
    <div class="rounded-2xl p-6 bg-gradient-to-r from-slate-900 via-indigo-950/40 to-slate-900 border border-indigo-500/20 shadow-xl relative overflow-hidden">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2 max-w-3xl">
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 text-xs font-bold uppercase tracking-wider rounded-md bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                        PRD Section 3.4 — Decision Engine
                    </span>
                    @if($comparison['score_difference'] !== null)
                        <span class="text-xs px-2.5 py-0.5 rounded-md bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-semibold">
                            Selisih Skor: {{ $comparison['score_difference'] }} Poin
                        </span>
                    @endif
                </div>
                <h2 class="text-xl font-bold text-white">Status Rekomendasi Pemilihan Channel Pilot</h2>
                <p class="text-sm text-slate-300 leading-relaxed">
                    {{ $comparison['recommendation'] }}
                </p>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                @if($ev->status === 'selected')
                    <div class="px-4 py-3 rounded-xl bg-emerald-500/20 border border-emerald-500 text-emerald-300 text-center font-bold text-sm">
                        ⭐ Channel Pilot Terpilih: Electric Vehicles
                    </div>
                @elseif($sleep->status === 'selected')
                    <div class="px-4 py-3 rounded-xl bg-emerald-500/20 border border-emerald-500 text-emerald-300 text-center font-bold text-sm">
                        ⭐ Channel Pilot Terpilih: Sleep Content
                    </div>
                @else
                    <div class="px-4 py-3 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-300 text-center font-medium text-xs">
                        ⏳ Menunggu Keputusan Pemilihan oleh Owner
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Comparative Grid: EV vs Sleep Content -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        <!-- Card 1: Electric Vehicles (EV) -->
        @php
            $evSnap = $ev->latestVerifiedSnapshot ?? $ev->latestSnapshot;
            $evEval = $ev->latestEvaluation;
        @endphp
        <div class="bg-slate-900/90 rounded-2xl border @if($ev->status === 'selected') border-emerald-500 shadow-emerald-500/10 @else border-slate-800 @endif shadow-xl p-6 flex flex-col justify-between space-y-6">
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
                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/40">SELECTED</span>
                    @else
                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-slate-800 text-slate-300">CANDIDATE</span>
                    @endif
                </div>

                <!-- CollabPals Score Badge -->
                <div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="bg-slate-950/80 rounded-xl p-3 border border-slate-800 text-center">
                        <span class="text-[10px] text-slate-400 uppercase font-medium">CollabPals Score</span>
                        <div class="text-3xl font-extrabold text-indigo-400 mt-1">
                            {{ $evSnap?->niche_score ?? 'null' }}
                        </div>
                        <span class="text-[10px] text-slate-500">Skor 0–100</span>
                    </div>
                    <div class="bg-slate-950/80 rounded-xl p-3 border border-slate-800 text-center">
                        <span class="text-[10px] text-slate-400 uppercase font-medium">CPM Range</span>
                        <div class="text-lg font-bold text-emerald-400 mt-2">
                            {{ $evSnap?->cpm_range ?? 'null' }}
                        </div>
                        <span class="text-[10px] text-slate-500">Automotive</span>
                    </div>
                    <div class="bg-slate-950/80 rounded-xl p-3 border border-slate-800 text-center">
                        <span class="text-[10px] text-slate-400 uppercase font-medium">Faceless</span>
                        <div class="text-base font-bold text-slate-200 mt-2">
                            {{ $evSnap?->faceless_friendly ? 'Ya' : 'Tidak (Basic)' }}
                        </div>
                        <span class="text-[10px] text-slate-500">Equipment: {{ $evSnap?->equipment_level ?? '-' }}</span>
                    </div>
                    <div class="bg-slate-950/80 rounded-xl p-3 border border-slate-800 text-center">
                        <span class="text-[10px] text-slate-400 uppercase font-medium">Status</span>
                        <div class="text-xs font-bold mt-2 uppercase px-2 py-0.5 rounded @if($evSnap?->verification_status === 'verified') bg-emerald-500/20 text-emerald-400 @else bg-amber-500/20 text-amber-400 @endif inline-block">
                            {{ $evSnap?->verification_status ?? 'unverified' }}
                        </div>
                        <div class="text-[10px] text-slate-500 mt-1">{{ $evSnap?->captured_at ? $evSnap->captured_at->diffForHumans() : '-' }}</div>
                    </div>
                </div>

                <!-- Screenshot Evidence Box -->
                <div class="mt-5 p-4 rounded-xl bg-slate-950/70 border border-slate-800 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-300 flex items-center gap-1.5">
                            📸 Bukti Capture Aktual (Screenshot & DOM)
                        </span>
                        @if($evSnap?->evidence_screenshot_path)
                            <a href="/{{ $evSnap->evidence_screenshot_path }}" target="_blank" class="text-xs text-indigo-400 hover:text-indigo-300 underline">
                                Buka Gambar Penuh ↗
                            </a>
                        @endif
                    </div>
                    @if($evSnap?->evidence_screenshot_path)
                        <div class="rounded-lg overflow-hidden border border-slate-800 max-h-48 bg-black flex items-center justify-center">
                            <img src="/{{ $evSnap->evidence_screenshot_path }}" alt="Bukti CollabPals EV" class="w-full object-contain">
                        </div>
                    @else
                        <div class="py-8 text-center text-xs text-slate-500">Belum ada screenshot bukti.</div>
                    @endif
                    @if(!empty($evSnap?->evidence_text['video_formats']))
                        <div class="pt-2 text-xs text-slate-400">
                            <span class="font-semibold text-slate-300">Format Video Terdeteksi:</span>
                            {{ implode(', ', $evSnap->evidence_text['video_formats']) }}
                        </div>
                    @endif
                </div>

                <!-- Internal Score (Lapisan B) -->
                <div class="mt-5 p-4 rounded-xl bg-indigo-950/20 border border-indigo-500/20 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-indigo-300 uppercase tracking-wider">Internal Pilot Score (Lapisan B)</span>
                        <span class="text-lg font-extrabold text-indigo-400">
                            {{ $evEval ? $evEval->internal_score : 'Belum Dievaluasi' }}
                        </span>
                    </div>
                    @if($evEval)
                        <p class="text-xs text-slate-300">{{ $evEval->rationale }}</p>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="pt-4 border-t border-slate-800 flex flex-wrap gap-2 justify-between">
                <form action="{{ route('niches.capture', $ev->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-3 py-2 text-xs font-semibold rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white transition flex items-center gap-1.5 shadow">
                        🔄 Live Capture CollabPals
                    </button>
                </form>

                <div class="flex gap-2">
                    <button onclick="openEvalModal('{{ $ev->id }}', '{{ $ev->name }}', '{{ $evSnap?->id }}')" class="px-3 py-2 text-xs font-semibold rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 transition">
                        ⚖️ Evaluasi Internal
                    </button>
                    <form action="{{ route('niches.select', $ev->id) }}" method="POST">
                        @csrf
                        <button type="submit" @if(!$evSnap || !$evSnap->isVerified()) disabled title="Harus berstatus verified" @endif class="px-3 py-2 text-xs font-bold rounded-lg @if($ev->status === 'selected') bg-emerald-600 text-white @else bg-slate-800 text-emerald-400 hover:bg-emerald-600 hover:text-white border border-emerald-500/40 @endif transition disabled:opacity-40 disabled:cursor-not-allowed">
                            @if($ev->status === 'selected') ✓ Terpilih @else Pilih Sebagai Pilot Channel @endif
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Card 2: Sleep Content -->
        @php
            $sleepSnap = $sleep->latestVerifiedSnapshot ?? $sleep->latestSnapshot;
            $sleepEval = $sleep->latestEvaluation;
        @endphp
        <div class="bg-slate-900/90 rounded-2xl border @if($sleep->status === 'selected') border-emerald-500 shadow-emerald-500/10 @else border-slate-800 @endif shadow-xl p-6 flex flex-col justify-between space-y-6">
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
                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/40">SELECTED</span>
                    @else
                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-slate-800 text-slate-300">CANDIDATE</span>
                    @endif
                </div>

                <!-- CollabPals Score Badge -->
                <div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="bg-slate-950/80 rounded-xl p-3 border border-slate-800 text-center">
                        <span class="text-[10px] text-slate-400 uppercase font-medium">CollabPals Score</span>
                        <div class="text-3xl font-extrabold text-indigo-400 mt-1">
                            {{ $sleepSnap?->niche_score ?? 'null' }}
                        </div>
                        <span class="text-[10px] text-slate-500">Skor 0–100</span>
                    </div>
                    <div class="bg-slate-950/80 rounded-xl p-3 border border-slate-800 text-center">
                        <span class="text-[10px] text-slate-400 uppercase font-medium">CPM Range</span>
                        <div class="text-lg font-bold text-emerald-400 mt-2">
                            {{ $sleepSnap?->cpm_range ?? 'null' }}
                        </div>
                        <span class="text-[10px] text-slate-500">ASMR / Ambience</span>
                    </div>
                    <div class="bg-slate-950/80 rounded-xl p-3 border border-slate-800 text-center">
                        <span class="text-[10px] text-slate-400 uppercase font-medium">Faceless</span>
                        <div class="text-base font-bold text-emerald-400 mt-2">
                            {{ $sleepSnap?->faceless_friendly ? 'Ya (Sangat Sesuai)' : 'Tidak' }}
                        </div>
                        <span class="text-[10px] text-slate-500">Equipment: {{ $sleepSnap?->equipment_level ?? '-' }}</span>
                    </div>
                    <div class="bg-slate-950/80 rounded-xl p-3 border border-slate-800 text-center">
                        <span class="text-[10px] text-slate-400 uppercase font-medium">Status</span>
                        <div class="text-xs font-bold mt-2 uppercase px-2 py-0.5 rounded @if($sleepSnap?->verification_status === 'verified') bg-emerald-500/20 text-emerald-400 @else bg-amber-500/20 text-amber-400 @endif inline-block">
                            {{ $sleepSnap?->verification_status ?? 'unverified' }}
                        </div>
                        <div class="text-[10px] text-slate-500 mt-1">{{ $sleepSnap?->captured_at ? $sleepSnap->captured_at->diffForHumans() : '-' }}</div>
                    </div>
                </div>

                <!-- Screenshot Evidence Box -->
                <div class="mt-5 p-4 rounded-xl bg-slate-950/70 border border-slate-800 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-300 flex items-center gap-1.5">
                            📸 Bukti Capture Aktual (Screenshot & DOM)
                        </span>
                        @if($sleepSnap?->evidence_screenshot_path)
                            <a href="/{{ $sleepSnap->evidence_screenshot_path }}" target="_blank" class="text-xs text-indigo-400 hover:text-indigo-300 underline">
                                Buka Gambar Penuh ↗
                            </a>
                        @endif
                    </div>
                    @if($sleepSnap?->evidence_screenshot_path)
                        <div class="rounded-lg overflow-hidden border border-slate-800 max-h-48 bg-black flex items-center justify-center">
                            <img src="/{{ $sleepSnap->evidence_screenshot_path }}" alt="Bukti CollabPals Sleep" class="w-full object-contain">
                        </div>
                    @else
                        <div class="py-8 text-center text-xs text-slate-500">Belum ada screenshot bukti.</div>
                    @endif
                    @if(!empty($sleepSnap?->evidence_text['video_formats']))
                        <div class="pt-2 text-xs text-slate-400">
                            <span class="font-semibold text-slate-300">Format Video Terdeteksi:</span>
                            {{ implode(', ', $sleepSnap->evidence_text['video_formats']) }}
                        </div>
                    @endif
                </div>

                <!-- Internal Score (Lapisan B) -->
                <div class="mt-5 p-4 rounded-xl bg-indigo-950/20 border border-indigo-500/20 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-indigo-300 uppercase tracking-wider">Internal Pilot Score (Lapisan B)</span>
                        <span class="text-lg font-extrabold text-indigo-400">
                            {{ $sleepEval ? $sleepEval->internal_score : 'Belum Dievaluasi' }}
                        </span>
                    </div>
                    @if($sleepEval)
                        <p class="text-xs text-slate-300">{{ $sleepEval->rationale }}</p>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="pt-4 border-t border-slate-800 flex flex-wrap gap-2 justify-between">
                <form action="{{ route('niches.capture', $sleep->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-3 py-2 text-xs font-semibold rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white transition flex items-center gap-1.5 shadow">
                        🔄 Live Capture CollabPals
                    </button>
                </form>

                <div class="flex gap-2">
                    <button onclick="openEvalModal('{{ $sleep->id }}', '{{ $sleep->name }}', '{{ $sleepSnap?->id }}')" class="px-3 py-2 text-xs font-semibold rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 transition">
                        ⚖️ Evaluasi Internal
                    </button>
                    <form action="{{ route('niches.select', $sleep->id) }}" method="POST">
                        @csrf
                        <button type="submit" @if(!$sleepSnap || !$sleepSnap->isVerified()) disabled title="Harus berstatus verified" @endif class="px-3 py-2 text-xs font-bold rounded-lg @if($sleep->status === 'selected') bg-emerald-600 text-white @else bg-slate-800 text-emerald-400 hover:bg-emerald-600 hover:text-white border border-emerald-500/40 @endif transition disabled:opacity-40 disabled:cursor-not-allowed">
                            @if($sleep->status === 'selected') ✓ Terpilih @else Pilih Sebagai Pilot Channel @endif
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <!-- Section: Riwayat Snapshot & Verifikasi Manual -->
    <div class="bg-slate-900/90 rounded-2xl border border-slate-800 p-6 space-y-4 shadow-xl">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-bold text-white">Riwayat Skor Snapshot (Audit Trail)</h3>
                <p class="text-xs text-slate-400">Bukti historis, timestamp capture, dan verifikasi reviewer (PRD FR-002).</p>
            </div>
            <button onclick="document.getElementById('manualModal').classList.remove('hidden')" class="px-3 py-2 text-xs font-semibold rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 transition border border-slate-700">
                ✍️ Input Manual Skor
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/80 uppercase font-semibold text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">ID</th>
                        <th class="py-3 px-4">Niche</th>
                        <th class="py-3 px-4">Score</th>
                        <th class="py-3 px-4">CPM</th>
                        <th class="py-3 px-4">Method</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Captured At</th>
                        <th class="py-3 px-4">Bukti</th>
                        <th class="py-3 px-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    @forelse($recentSnapshots as $snap)
                        <tr class="hover:bg-slate-850">
                            <td class="py-3 px-4 font-mono">#{{ $snap->id }}</td>
                            <td class="py-3 px-4 font-medium text-white">{{ $snap->nicheCandidate->name }}</td>
                            <td class="py-3 px-4 font-bold text-indigo-400">{{ $snap->niche_score ?? '-' }}</td>
                            <td class="py-3 px-4 text-emerald-400">{{ $snap->cpm_range ?? '-' }}</td>
                            <td class="py-3 px-4 capitalize">{{ str_replace('_', ' ', $snap->capture_method) }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase @if($snap->verification_status === 'verified') bg-emerald-500/20 text-emerald-400 @elseif($snap->verification_status === 'needs_review') bg-amber-500/20 text-amber-400 @else bg-slate-800 text-slate-400 @endif">
                                    {{ $snap->verification_status }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-mono">{{ $snap->captured_at->format('Y-m-d H:i') }}</td>
                            <td class="py-3 px-4">
                                @if($snap->evidence_screenshot_path)
                                    <a href="/{{ $snap->evidence_screenshot_path }}" target="_blank" class="text-indigo-400 hover:underline">Screenshot</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @if($snap->verification_status === 'needs_review')
                                    <form action="{{ route('snapshots.verify', $snap->id) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="status" value="verified">
                                        <button type="submit" class="text-emerald-400 hover:text-emerald-300 font-bold mr-2">Setujui</button>
                                    </form>
                                    <form action="{{ route('snapshots.verify', $snap->id) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="status" value="unverified">
                                        <button type="submit" class="text-rose-400 hover:text-rose-300">Tolak</button>
                                    </form>
                                @else
                                    <span class="text-slate-500">Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-6 text-center text-slate-500">Belum ada riwayat snapshot.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal: Evaluasi Internal (Lapisan B) -->
    <div id="evalModal" class="hidden fixed inset-0 z-50 bg-black/75 flex items-center justify-center p-4">
        <div class="bg-slate-900 rounded-2xl border border-slate-800 max-w-lg w-full p-6 space-y-4 shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-base font-bold text-white">Evaluasi Internal Lapisan B: <span id="evalCandidateName"></span></h3>
                <button onclick="document.getElementById('evalModal').classList.add('hidden')" class="text-slate-400 hover:text-white">✕</button>
            </div>
            <form id="evalForm" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="snapshot_id" id="evalSnapshotId">

                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-300 flex justify-between">
                        <span>Content Feasibility (Bobot 20%)</span>
                        <span id="feasVal" class="text-indigo-400">80</span>
                    </label>
                    <input type="range" min="0" max="100" value="80" name="feasibility_score" oninput="document.getElementById('feasVal').innerText=this.value" class="w-full accent-indigo-500">
                    <p class="text-[10px] text-slate-400">Kemampuan memproduksi 20 ide orisinal dan aset berlisensi legal.</p>
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

    <!-- Modal: Input Manual Skor -->
    <div id="manualModal" class="hidden fixed inset-0 z-50 bg-black/75 flex items-center justify-center p-4">
        <div class="bg-slate-900 rounded-2xl border border-slate-800 max-w-md w-full p-6 space-y-4 shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-base font-bold text-white">Input Manual Skor CollabPals</h3>
                <button onclick="document.getElementById('manualModal').classList.add('hidden')" class="text-slate-400 hover:text-white">✕</button>
            </div>
            <form action="" id="manualForm" method="POST" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-300">Pilih Niche Candidate</label>
                    <select id="manualNicheSelect" onchange="document.getElementById('manualForm').action='/niches/' + this.value + '/manual-snapshot'" class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-xs text-white">
                        <option value="{{ $ev->id }}">{{ $ev->name }}</option>
                        <option value="{{ $sleep->id }}">{{ $sleep->name }}</option>
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-300">CollabPals Niche Score (0–100)</label>
                    <input type="number" name="niche_score" min="0" max="100" required class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-xs text-white">
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-300">CPM Range (mis. $6-$18)</label>
                    <input type="text" name="cpm_range" placeholder="$6-$18" class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-xs text-white">
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-300">Upload Screenshot Bukti (Wajib)</label>
                    <input type="file" name="evidence_screenshot" accept="image/*" required class="w-full text-xs text-slate-400 file:bg-slate-800 file:border-0 file:rounded file:px-2 file:py-1 file:text-white file:text-xs">
                </div>
                <div class="pt-3 border-t border-slate-800 flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('manualModal').classList.add('hidden')" class="px-3 py-1.5 rounded-lg bg-slate-800 text-slate-300 text-xs">Batal</button>
                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs">Kirim Input</button>
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

        const select = document.getElementById('manualNicheSelect');
        if (select) {
            document.getElementById('manualForm').action = '/niches/' + select.value + '/manual-snapshot';
        }
    </script>
@endsection
