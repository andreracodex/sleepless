@extends('layouts.app')

@section('title', 'Research Dossier — ' . $idea->title_idea)

@section('header_title', 'Phase 2: Research Dossier & Claim Ledger')
@section('header_subtitle', $idea->title_idea)

@section('header_actions')
    <a href="{{ route('scripts.show', $idea->id) }}" class="text-xs px-3.5 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-semibold transition flex items-center gap-1.5 shadow">
        <span>🎬 Buka Script Studio →</span>
    </a>
    <a href="{{ route('ideas.index') }}" class="text-xs px-3 py-1.5 rounded-lg bg-slate-800 text-slate-300 hover:text-white hover:bg-slate-700 transition border border-slate-700">
        ← Idea Board
    </a>
@endsection

@section('content')

    <!-- Dossier Header & Idea Context -->
    <div class="bg-slate-900/90 rounded-2xl border border-slate-800 p-6 space-y-4 shadow-xl">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold uppercase tracking-wider px-2.5 py-0.5 rounded bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                        PRD FR-006 — Fact-Checking & Source Ledger
                    </span>
                    <span class="text-xs text-slate-400">Format: {{ $idea->format === 'long_form' ? 'Long-form (16:9)' : 'Shorts (9:16)' }} • Target: {{ gmdate("i:s", $idea->target_duration_seconds) }}</span>
                </div>
                <h2 class="text-2xl font-extrabold text-white">{{ $idea->title_idea }}</h2>
            </div>

            <!-- Review Action Bar (Gate) -->
            @if($dossier)
                <form action="{{ route('dossier.review', $dossier->id) }}" method="POST" class="flex gap-2">
                    @csrf
                    @if($dossier->status !== 'approved')
                        <button type="submit" name="action" value="approve" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg transition flex items-center gap-1.5">
                            <span>✓ Setujui Dossier & Mulai Script</span>
                        </button>
                        <button type="submit" name="action" value="needs_revision" class="px-3 py-2 rounded-xl bg-amber-600 hover:bg-amber-500 text-white font-semibold text-xs transition">
                            Minta Revisi
                        </button>
                    @else
                        <div class="px-4 py-2 rounded-xl bg-emerald-500/20 border border-emerald-500 text-emerald-300 font-bold text-xs flex items-center gap-2">
                            <span>⭐ Dossier Disetujui (Approved)</span>
                        </div>
                    @endif
                </form>
            @endif
        </div>

        <!-- Executive Summary -->
        <div class="p-4 rounded-xl bg-slate-950/70 border border-slate-800 space-y-2">
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Ringkasan Riset & Temuan Utama:</h4>
            <p class="text-sm text-slate-200 leading-relaxed">
                {{ $dossier?->research_summary ?? 'Riset sedang dikompilasi oleh tim peneliti.' }}
            </p>
        </div>
    </div>

    @if($dossier)
        <!-- Section 1: Claim Ledger (Tabel Klaim Faktual) -->
        <div class="bg-slate-900/90 rounded-2xl border border-slate-800 p-6 space-y-4 shadow-xl">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <span>📋 Claim Ledger (Klaim Faktual Terverifikasi)</span>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                            {{ count($dossier->claim_ledger ?? []) }} Klaim
                        </span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Semua poin klaim wajib memiliki rujukan sumber primer sebelum diizinkan masuk ke naskah final.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-950/80 uppercase font-semibold text-slate-400 border-b border-slate-800">
                        <tr>
                            <th class="py-3 px-4 w-20">ID</th>
                            <th class="py-3 px-4">Pernyataan Klaim Faktual</th>
                            <th class="py-3 px-4 w-32">Ref Sumber</th>
                            <th class="py-3 px-4 w-32">Confidence</th>
                            <th class="py-3 px-4 w-64">Catatan Verifikasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        @foreach($dossier->claim_ledger ?? [] as $clm)
                            <tr class="hover:bg-slate-850">
                                <td class="py-3 px-4 font-mono font-bold text-indigo-400">{{ $clm['claim_id'] }}</td>
                                <td class="py-3 px-4 text-white font-medium leading-relaxed">{{ $clm['claim'] }}</td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-0.5 rounded bg-slate-950 text-slate-300 font-mono text-[10px] border border-slate-800">
                                        {{ $clm['source_ref'] }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-1.5">
                                        <div class="w-12 bg-slate-800 rounded-full h-1.5 overflow-hidden">
                                            <div class="bg-emerald-400 h-full" style="width: {{ ($clm['confidence'] ?? 0.9) * 100 }}%"></div>
                                        </div>
                                        <span class="font-mono text-emerald-400">{{ round(($clm['confidence'] ?? 0.9) * 100) }}%</span>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-slate-400">{{ $clm['verification_note'] ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 2: Source Ledger (Rujukan Sumber) -->
        <div class="bg-slate-900/90 rounded-2xl border border-slate-800 p-6 space-y-4 shadow-xl">
            <div>
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <span>📚 Source Ledger (Daftar Sumber Primer & Regulasi)</span>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-cyan-500/20 text-cyan-300 border border-cyan-500/30">
                        {{ count($dossier->source_ledger ?? []) }} Sumber
                    </span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Rujukan yang dikutip di deskripsi YouTube untuk transparansi dan kepatuhan monetisasi (PRD FR-012).</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($dossier->source_ledger ?? [] as $src)
                    <div class="p-4 rounded-xl bg-slate-950/70 border border-slate-800 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="px-2 py-0.5 rounded bg-cyan-500/20 text-cyan-300 text-[10px] font-mono font-bold">
                                {{ $src['source_id'] }}
                            </span>
                            <span class="text-[10px] text-slate-400">{{ $src['date'] ?? '-' }}</span>
                        </div>
                        <h4 class="text-sm font-bold text-white leading-snug">{{ $src['title'] }}</h4>
                        <p class="text-xs text-slate-400">Penerbit: <strong class="text-slate-300">{{ $src['publisher'] }}</strong></p>
                        @if(!empty($src['sample_size']))
                            <p class="text-[11px] text-slate-400">Data Sampel: {{ $src['sample_size'] }}</p>
                        @endif
                        @if(!empty($src['url']))
                            <div class="pt-1">
                                <a href="{{ $src['url'] }}" target="_blank" class="text-xs text-indigo-400 hover:text-indigo-300 underline flex items-center gap-1">
                                    <span>Kunjungi Tautan Rujukan</span>
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Section 3: Risk Flags (Kepatuhan Editorial & Keamanan) -->
        @if(!empty($dossier->risk_flags))
            <div class="bg-rose-950/20 rounded-2xl border border-rose-500/30 p-6 space-y-3 shadow-xl">
                <h3 class="text-base font-bold text-rose-300 flex items-center gap-2">
                    <span>⚠️ Editorial Risk Flags & Disclaimers</span>
                </h3>
                <ul class="list-disc list-inside text-xs text-rose-200 space-y-1 leading-relaxed">
                    @foreach($dossier->risk_flags as $risk)
                        <li>{{ $risk }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

    @else
        <div class="bg-slate-900/90 rounded-2xl border border-slate-800 p-12 text-center text-slate-400 space-y-3">
            <p>Belum ada Research Dossier untuk ide ini.</p>
        </div>
    @endif

@endsection
