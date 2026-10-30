@extends('layouts.app')

@section('title', 'Script & Audio Studio — ' . $idea->title_idea)

@section('header_title', 'Phase 3: Script & Audio Studio')
@section('header_subtitle', $idea->title_idea)

@section('header_actions')
    <a href="{{ route('ideas.dossier', $idea->id) }}" class="text-xs px-3 py-1.5 rounded-lg bg-slate-800 text-slate-300 hover:text-white hover:bg-slate-700 transition border border-slate-700">
        📑 Lihat Dossier
    </a>
    <a href="{{ route('production.index') }}" class="text-xs px-3.5 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-semibold transition flex items-center gap-1.5 shadow">
        <span>🎬 Production Studio →</span>
    </a>
@endsection

@section('content')

    <!-- Script Header Context -->
    <div class="bg-slate-900/90 rounded-2xl border border-slate-800 p-6 space-y-4 shadow-xl">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold uppercase tracking-wider px-2.5 py-0.5 rounded bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                        PRD FR-007 — Scriptwriting & Storyboard
                    </span>
                    <span class="text-xs text-slate-400">Bahasa: {{ strtoupper($script->language ?? 'EN') }} • Versi: v{{ $script->version ?? 1 }}</span>
                </div>
                <h2 class="text-2xl font-extrabold text-white">{{ $idea->title_idea }}</h2>
            </div>

            <!-- Actions -->
            <div class="flex flex-wrap items-center gap-2">
                @if(!$script)
                    <form action="{{ route('scripts.generate', $idea->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-lg transition flex items-center gap-1.5">
                            <span>⚡ Generate Script dari Dossier</span>
                        </button>
                    </form>
                @else
                    @if($script->status !== 'approved')
                        <form action="{{ route('scripts.approve', $script->id) }}" method="POST" class="flex gap-2">
                            @csrf
                            <button type="submit" name="action" value="approve" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg transition flex items-center gap-1.5">
                                <span>✓ Setujui Naskah (Approve)</span>
                            </button>
                            <button type="submit" name="action" value="needs_revision" class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs transition border border-slate-700">
                                Minta Revisi
                            </button>
                        </form>
                    @else
                        <div class="flex items-center gap-2">
                            <div class="px-3 py-2 rounded-xl bg-emerald-500/20 border border-emerald-500 text-emerald-300 font-bold text-xs">
                                ⭐ Naskah Disetujui
                            </div>
                            <form action="{{ route('production.render', $script->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-4 py-2 rounded-xl bg-gradient-to-r from-rose-600 to-indigo-600 hover:from-rose-500 hover:to-indigo-500 text-white font-bold text-xs shadow-lg shadow-rose-600/30 transition flex items-center gap-1.5">
                                    <span>🎬 Render 1080p Video →</span>
                                </button>
                            </form>
                        </div>
                    @endif
                @endif
            </div>
        </div>

        @if($script)
            <!-- Metrics Ribbon -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-3 border-t border-slate-800 text-center">
                <div class="bg-slate-950/70 rounded-xl p-3 border border-slate-850">
                    <span class="text-[10px] uppercase font-semibold text-slate-400">Total Kata</span>
                    <div class="text-xl font-extrabold text-white mt-0.5">{{ $script->word_count }} kata</div>
                </div>
                <div class="bg-slate-950/70 rounded-xl p-3 border border-slate-850">
                    <span class="text-[10px] uppercase font-semibold text-slate-400">Estimasi Durasi</span>
                    <div class="text-xl font-extrabold text-indigo-400 mt-0.5">{{ gmdate("i:s", $script->estimated_duration_seconds) }}</div>
                </div>
                <div class="bg-slate-950/70 rounded-xl p-3 border border-slate-850">
                    <span class="text-[10px] uppercase font-semibold text-slate-400">Total Adegan</span>
                    <div class="text-xl font-extrabold text-cyan-400 mt-0.5">{{ count($script->script_json['scenes'] ?? []) }} Adegan</div>
                </div>
                <div class="bg-slate-950/70 rounded-xl p-3 border border-slate-850">
                    <span class="text-[10px] uppercase font-semibold text-slate-400">Klaim Terkait</span>
                    <div class="text-xl font-extrabold text-emerald-400 mt-0.5">4 Terverifikasi</div>
                </div>
            </div>
        @endif
    </div>

    @if($script)
        <!-- Voiceover Audio Player & Generator Box -->
        <div class="bg-slate-900/90 rounded-2xl border border-indigo-500/30 p-6 space-y-4 shadow-xl">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <span>🎙️ Voiceover Audio Pipeline (PRD FR-009)</span>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                            Edge Neural TTS: en-US-ChristopherNeural
                        </span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Audio voiceover studio orisinal dengan subtitle sinkron (SRT) siap untuk video rendering.</p>
                </div>

                <form action="{{ route('scripts.audio', $script->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-lg transition flex items-center gap-1.5">
                        <span>🔊 Generate Voiceover Neural MP3</span>
                    </button>
                </form>
            </div>

            @if($voiceoverAsset)
                <div class="p-4 rounded-xl bg-slate-950/80 border border-slate-800 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-emerald-400 flex items-center gap-1.5">
                            ✓ File Audio Siap: {{ basename($voiceoverAsset->storage_path) }}
                        </span>
                        <span class="text-xs text-slate-400 font-mono">{{ round(($voiceoverAsset->metadata['file_size'] ?? 0) / 1024) }} KB</span>
                    </div>
                    <audio controls class="w-full h-10 accent-indigo-500 rounded-lg">
                        <source src="/{{ $voiceoverAsset->storage_path }}" type="audio/mpeg">
                        Browser Anda tidak mendukung tag audio HTML5.
                    </audio>
                </div>
            @else
                <div class="p-4 rounded-xl bg-slate-950/50 border border-slate-800 text-center text-xs text-slate-500">
                    Klik tombol di atas untuk membuat rekaman voiceover neural otomatis.
                </div>
            @endif
        </div>

        <!-- Scene by Scene Script Breakdown -->
        <div class="space-y-4">
            <h3 class="text-base font-bold text-white flex items-center gap-2">
                <span>🎬 Storyboard & Scene Breakdown</span>
            </h3>

            <div class="space-y-4">
                @foreach($script->script_json['scenes'] ?? [] as $scene)
                    <div class="bg-slate-900/90 rounded-2xl border border-slate-800 p-6 space-y-3 shadow-lg">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded-lg bg-indigo-600 text-white text-xs font-black">
                                    Scene {{ $scene['scene_number'] }}
                                </span>
                                <h4 class="text-sm font-bold text-white">{{ $scene['title'] }}</h4>
                            </div>
                            <span class="text-xs font-mono text-slate-400 bg-slate-950 px-2 py-0.5 rounded border border-slate-800">
                                ⏱️ {{ $scene['duration_seconds'] }}s
                            </span>
                        </div>

                        <!-- Narrator Voiceover Text -->
                        <div class="p-4 rounded-xl bg-slate-950/80 border border-slate-800 space-y-1">
                            <span class="text-[10px] uppercase font-bold text-indigo-400 tracking-wider">🎙️ Voiceover (Narasi):</span>
                            <p class="text-sm text-slate-200 leading-relaxed font-serif">
                                “{{ $scene['narrator_text'] }}”
                            </p>
                        </div>

                        <!-- Visual Direction & On-Screen Text -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                            <div class="p-3 rounded-lg bg-slate-950/40 border border-slate-800">
                                <span class="text-[10px] font-bold text-slate-400 uppercase">🎥 Visual Direction (Remotion Cue):</span>
                                <p class="text-slate-300 mt-1">{{ $scene['visual_direction'] }}</p>
                            </div>
                            <div class="p-3 rounded-lg bg-slate-950/40 border border-slate-800">
                                <span class="text-[10px] font-bold text-slate-400 uppercase">🔤 On-Screen Motion Text:</span>
                                <p class="text-slate-300 mt-1 font-mono text-[11px]">{{ $scene['on_screen_text'] }}</p>
                            </div>
                        </div>

                        @if(!empty($scene['claim_references']))
                            <div class="flex items-center gap-2 pt-1 text-xs">
                                <span class="text-[10px] font-semibold text-slate-400">Rujukan Klaim Faktual:</span>
                                @foreach($scene['claim_references'] as $ref)
                                    <span class="px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-400 font-mono text-[10px] border border-emerald-500/30">
                                        ✓ {{ $ref }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="bg-slate-900/90 rounded-2xl border border-slate-800 p-12 text-center text-slate-400 space-y-3">
            <p>Naskah belum dibuat. Klik tombol di kanan atas untuk membuat naskah otomatis dari Research Dossier.</p>
        </div>
    @endif

@endsection
