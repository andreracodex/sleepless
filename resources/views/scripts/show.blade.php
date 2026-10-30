<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Script & Audio Studio — {{ $idea->title_idea }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            900: '#312e81',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex flex-col antialiased">

    <!-- Top Navigation -->
    <header class="border-b border-slate-800 bg-slate-900/90 backdrop-blur sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="{{ route('ideas.index') }}" class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-600 to-indigo-400 flex items-center justify-center shadow-lg shadow-brand-500/20 font-black text-xl text-white">
                    ▶
                </a>
                <div>
                    <h1 class="text-lg font-bold text-white tracking-tight flex items-center gap-2">
                        Script & Audio Studio
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full @if($script?->status === 'approved') bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 @else bg-amber-500/20 text-amber-400 border border-amber-500/30 @endif">
                            {{ $script ? strtoupper($script->status) : 'NOT GENERATED' }}
                        </span>
                    </h1>
                    <p class="text-xs text-slate-400">{{ $idea->title_idea }}</p>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <a href="{{ route('ideas.dossier', $idea->id) }}" class="text-xs px-3 py-1.5 rounded-lg bg-slate-800 text-slate-300 hover:text-white hover:bg-slate-700 transition border border-slate-700">
                    📑 Lihat Research Dossier
                </a>
                <a href="{{ route('ideas.index') }}" class="text-xs px-3 py-1.5 rounded-lg bg-slate-800 text-slate-300 hover:text-white hover:bg-slate-700 transition border border-slate-700">
                    ← Kembali ke Idea Board
                </a>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

        <!-- Flash Notifications -->
        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm flex items-center justify-between shadow-lg">
                <div class="flex items-center space-x-2">
                    <span>✅</span>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-200">✕</button>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-400 text-sm flex items-center justify-between shadow-lg">
                <div class="flex items-center space-x-2">
                    <span>⚠️</span>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-rose-400 hover:text-rose-200">✕</button>
            </div>
        @endif

        <!-- Script Header Context -->
        <div class="bg-slate-800/80 rounded-2xl border border-slate-700 p-6 space-y-4 shadow-xl">
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
                                <button type="submit" name="action" value="needs_revision" class="px-3 py-2 rounded-xl bg-slate-700 hover:bg-slate-600 text-slate-200 font-semibold text-xs transition">
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
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-3 border-t border-slate-700/60 text-center">
                    <div class="bg-slate-900/60 rounded-xl p-3 border border-slate-800">
                        <span class="text-[10px] uppercase font-semibold text-slate-400">Total Kata</span>
                        <div class="text-xl font-extrabold text-white mt-0.5">{{ $script->word_count }} kata</div>
                    </div>
                    <div class="bg-slate-900/60 rounded-xl p-3 border border-slate-800">
                        <span class="text-[10px] uppercase font-semibold text-slate-400">Estimasi Durasi</span>
                        <div class="text-xl font-extrabold text-indigo-400 mt-0.5">{{ gmdate("i:s", $script->estimated_duration_seconds) }}</div>
                    </div>
                    <div class="bg-slate-900/60 rounded-xl p-3 border border-slate-800">
                        <span class="text-[10px] uppercase font-semibold text-slate-400">Total Adegan</span>
                        <div class="text-xl font-extrabold text-cyan-400 mt-0.5">{{ count($script->script_json['scenes'] ?? []) }} Adegan</div>
                    </div>
                    <div class="bg-slate-900/60 rounded-xl p-3 border border-slate-800">
                        <span class="text-[10px] uppercase font-semibold text-slate-400">Klaim Terkait</span>
                        <div class="text-xl font-extrabold text-emerald-400 mt-0.5">4 Terverifikasi</div>
                    </div>
                </div>
            @endif
        </div>

        @if($script)
            <!-- Voiceover Audio Player & Generator Box -->
            <div class="bg-slate-800/80 rounded-2xl border border-indigo-500/30 p-6 space-y-4 shadow-xl">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h3 class="text-base font-bold text-white flex items-center gap-2">
                            <span>🎙️ Voiceover Audio Pipeline (PRD FR-009)</span>
                            <span class="text-xs px-2 py-0.5 rounded-full bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                                Edge Neural TTS: en-US-ChristopherNeural
                            </span>
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">Audio voiceover studio orisinal dengan subtitle sinkron (VTT) siap untuk Remotion video render.</p>
                    </div>

                    <form action="{{ route('scripts.audio', $script->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-lg transition flex items-center gap-1.5">
                            <span>🔊 Generate Voiceover Neural MP3</span>
                        </button>
                    </form>
                </div>

                @if($voiceoverAsset)
                    <div class="p-4 rounded-xl bg-slate-900/80 border border-slate-700/80 space-y-3">
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
                    <div class="p-4 rounded-xl bg-slate-900/40 border border-slate-800 text-center text-xs text-slate-500">
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
                        <div class="bg-slate-800/80 rounded-2xl border border-slate-700 p-6 space-y-3 shadow-lg">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-0.5 rounded-lg bg-indigo-600 text-white text-xs font-black">
                                        Scene {{ $scene['scene_number'] }}
                                    </span>
                                    <h4 class="text-sm font-bold text-white">{{ $scene['title'] }}</h4>
                                </div>
                                <span class="text-xs font-mono text-slate-400 bg-slate-900 px-2 py-0.5 rounded border border-slate-700">
                                    ⏱️ {{ $scene['duration_seconds'] }}s
                                </span>
                            </div>

                            <!-- Narrator Voiceover Text -->
                            <div class="p-4 rounded-xl bg-slate-900/80 border border-slate-700/80 space-y-1">
                                <span class="text-[10px] uppercase font-bold text-indigo-400 tracking-wider">🎙️ Voiceover (Narasi):</span>
                                <p class="text-sm text-slate-200 leading-relaxed font-serif">
                                    “{{ $scene['narrator_text'] }}”
                                </p>
                            </div>

                            <!-- Visual Direction & On-Screen Text -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                                <div class="p-3 rounded-lg bg-slate-900/40 border border-slate-800">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase">🎥 Visual Direction (Remotion Cue):</span>
                                    <p class="text-slate-300 mt-1">{{ $scene['visual_direction'] }}</p>
                                </div>
                                <div class="p-3 rounded-lg bg-slate-900/40 border border-slate-800">
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
            <div class="bg-slate-800/80 rounded-2xl border border-slate-700 p-12 text-center text-slate-400 space-y-3">
                <p>Naskah belum dibuat. Klik tombol di kanan atas untuk membuat naskah otomatis dari Research Dossier.</p>
            </div>
        @endif

    </main>

</body>
</html>
