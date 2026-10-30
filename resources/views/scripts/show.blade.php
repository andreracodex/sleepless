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

    <!-- Pipeline Step Breadcrumb Indicator -->
    <div class="flex items-center gap-2 text-xs text-slate-400 mb-2 overflow-x-auto py-1">
        <a href="{{ route('niches.index') }}" class="hover:text-indigo-400 flex items-center gap-1">
            <span class="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-[10px]">✓</span>
            <span>1. Niche Lab</span>
        </a>
        <span class="text-slate-600">›</span>
        <a href="{{ route('ideas.index') }}" class="hover:text-indigo-400 flex items-center gap-1">
            <span class="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-[10px]">✓</span>
            <span>2. Idea Board</span>
        </a>
        <span class="text-slate-600">›</span>
        <a href="{{ route('ideas.dossier', $idea->id) }}" class="hover:text-indigo-400 flex items-center gap-1">
            <span class="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-[10px]">✓</span>
            <span>3. Research Dossier</span>
        </a>
        <span class="text-slate-600">›</span>
        <span class="text-indigo-400 font-bold flex items-center gap-1">
            <span class="w-5 h-5 rounded-full bg-indigo-500 text-white flex items-center justify-center font-bold text-[10px]">4</span>
            <span>Script & Audio Studio (Langkah Saat Ini)</span>
        </span>
    </div>

    <!-- Script Header Context Card -->
    <div class="bg-slate-900/90 rounded-2xl border border-slate-800 p-6 space-y-4 shadow-xl">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="space-y-1">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs font-bold uppercase tracking-wider px-2.5 py-0.5 rounded bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                        PRD FR-007 — Scriptwriting & Storyboard
                    </span>
                    @if($script)
                        @php
                            $curLang = $script->language ?? 'id';
                            $curLangData = $supportedLanguages[$curLang] ?? ['flag' => '🌐', 'name' => strtoupper($curLang)];
                        @endphp
                        <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-slate-800 text-emerald-400 border border-slate-700 flex items-center gap-1">
                            <span>{{ $curLangData['flag'] }}</span>
                            <span>{{ $curLangData['name'] }}</span>
                        </span>
                        <span class="text-xs text-slate-400">Versi: <strong class="text-white">v{{ $script->version }}</strong></span>

                        @if(isset($allVersions) && $allVersions->count() > 1)
                            <div class="flex items-center gap-1 text-xs text-slate-400 ml-2">
                                <span>Pilih versi:</span>
                                @foreach($allVersions as $v)
                                    <a href="{{ route('scripts.show', ['idea' => $idea->id, 'version' => $v->version]) }}" 
                                       class="px-2 py-0.5 rounded text-[11px] font-bold transition {{ $v->id === $script->id ? 'bg-indigo-600 text-white' : 'bg-slate-800 hover:bg-slate-700 text-slate-300' }}">
                                        v{{ $v->version }} ({{ strtoupper($v->language) }})
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    @endif
                </div>
                <h2 class="text-2xl font-extrabold text-white tracking-tight" style="font-family: 'Plus Jakarta Sans', sans-serif;">{{ $idea->title_idea }}</h2>
                <p class="text-xs text-slate-400">
                    Format: <span class="text-indigo-300 font-semibold uppercase">{{ $idea->format }}</span> • 
                    Angle: <span class="text-slate-300">{{ $idea->angle }}</span>
                </p>
            </div>

            <!-- Approval / Render Actions -->
            <div class="flex flex-wrap items-center gap-2">
                @if($script)
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
                    <div class="text-xl font-extrabold text-white mt-0.5" style="font-family: 'Plus Jakarta Sans', sans-serif;">{{ $script->word_count }} kata</div>
                </div>
                <div class="bg-slate-950/70 rounded-xl p-3 border border-slate-850">
                    <span class="text-[10px] uppercase font-semibold text-slate-400">Estimasi Durasi</span>
                    <div class="text-xl font-extrabold text-indigo-400 mt-0.5" style="font-family: 'Plus Jakarta Sans', sans-serif;">{{ gmdate("i:s", $script->estimated_duration_seconds) }}</div>
                </div>
                <div class="bg-slate-950/70 rounded-xl p-3 border border-slate-850">
                    <span class="text-[10px] uppercase font-semibold text-slate-400">Total Adegan</span>
                    <div class="text-xl font-extrabold text-cyan-400 mt-0.5" style="font-family: 'Plus Jakarta Sans', sans-serif;">{{ count($script->script_json['scenes'] ?? []) }} Adegan</div>
                </div>
                <div class="bg-slate-950/70 rounded-xl p-3 border border-slate-850">
                    <span class="text-[10px] uppercase font-semibold text-slate-400">Klaim Terkait</span>
                    <div class="text-xl font-extrabold text-emerald-400 mt-0.5" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                        {{ count($dossier->claim_ledger ?? []) }} Terverifikasi
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- LANGUAGE & VOICE SELECTION PANEL -->
    <div class="bg-gradient-to-br from-slate-900 to-indigo-950/40 rounded-2xl border border-indigo-500/30 p-6 space-y-4 shadow-xl">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div>
                <h3 class="text-base font-bold text-white flex items-center gap-2" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                    <span>🌐 Pengaturan Bahasa & Suara Neural AI</span>
                    <span class="text-[11px] px-2 py-0.5 rounded bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 font-semibold">
                        Pilih Bahasa Terlebih Dahulu
                    </span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">
                    Tentukan target bahasa narasi naskah dan suara voiceover Edge-TTS yang sinkron dengan hasil riset langkah sebelumnya.
                </p>
            </div>
            @if($script)
                <button type="button" onclick="document.getElementById('generate-form-box').classList.toggle('hidden')" class="text-xs px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-indigo-300 font-semibold border border-indigo-500/30 transition flex items-center gap-1.5 self-start md:self-auto">
                    <span>🔄 Generate Versi Baru (Ganti Bahasa)</span>
                </button>
            @endif
        </div>

        <div id="generate-form-box" class="{{ $script ? 'hidden' : '' }} pt-2 border-t border-slate-800/80">
            <form action="{{ route('scripts.generate', $idea->id) }}" method="POST" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Language Selection -->
                    <div class="space-y-1.5">
                        <label for="script-language" class="block text-xs font-bold text-slate-300 uppercase tracking-wider">
                            Pilih Bahasa Naskah (Target Audience)
                        </label>
                        <select id="script-language" name="language" onchange="syncLanguageVoices(this.value)" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs font-semibold focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                            @foreach($supportedLanguages as $langCode => $langInfo)
                                <option value="{{ $langCode }}" {{ ($script->language ?? 'id') === $langCode ? 'selected' : '' }}>
                                    {{ $langInfo['flag'] }} {{ $langInfo['name'] }} ({{ strtoupper($langCode) }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-slate-400">
                            Naskah adegan dan on-screen visual direction akan otomatis disusun dalam bahasa ini.
                        </p>
                    </div>

                    <!-- Voice Selection -->
                    <div class="space-y-1.5">
                        <label for="script-voice" class="block text-xs font-bold text-slate-300 uppercase tracking-wider">
                            Pilih Suara Voiceover (Edge Neural TTS)
                        </label>
                        <select id="script-voice" name="voice" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs font-semibold focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                            <!-- Populated dynamically by syncLanguageVoices() -->
                        </select>
                        <p class="text-[11px] text-slate-400">
                            Suara neural ultra-realistis dengan pengucapan asli sesuai bahasa yang dipilih.
                        </p>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2">
                    <span class="text-xs text-slate-400">
                        Topik & Riset: <strong class="text-white">{{ $idea->title_idea }}</strong>
                    </span>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-lg shadow-indigo-600/30 transition flex items-center gap-2">
                        <span>⚡ Generate Naskah & Siapkan Audio</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if($script)
        <!-- Voiceover Audio Player & Generator Box -->
        <div class="bg-slate-900/90 rounded-2xl border border-indigo-500/30 p-6 space-y-4 shadow-xl">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h3 class="text-base font-bold text-white flex items-center gap-2" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                        <span>🎙️ Voiceover Audio Pipeline (PRD FR-009)</span>
                        <span class="text-xs px-2.5 py-0.5 rounded-full bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 font-semibold">
                            Bahasa: {{ strtoupper($script->language) }}
                        </span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Audio voiceover studio orisinal dengan subtitle sinkron (SRT) siap untuk video rendering.
                    </p>
                </div>

                <!-- Audio Generation Form with Voice Picker -->
                <form action="{{ route('scripts.audio', $script->id) }}" method="POST" class="flex flex-wrap items-center gap-2">
                    @csrf
                    <div class="flex items-center gap-1.5">
                        <select name="voice" class="px-3 py-1.5 rounded-xl bg-slate-950 border border-slate-700 text-slate-200 text-xs font-semibold focus:border-indigo-500">
                            @php
                                $scriptLangVoices = $supportedLanguages[$script->language]['voices'] ?? [];
                                $selectedVoice = $voiceoverAsset->metadata['voice'] ?? ($script->script_json['selected_voice'] ?? key($scriptLangVoices));
                            @endphp
                            @foreach($scriptLangVoices as $vKey => $vLabel)
                                <option value="{{ $vKey }}" {{ $selectedVoice === $vKey ? 'selected' : '' }}>
                                    {{ $vLabel }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-lg transition flex items-center gap-1.5">
                        <span>🔊 {{ $voiceoverAsset ? 'Re-generate Audio' : 'Generate Voiceover MP3' }}</span>
                    </button>
                </form>
            </div>

            @if($voiceoverAsset)
                <div class="p-4 rounded-xl bg-slate-950/80 border border-slate-800 space-y-3">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-semibold text-emerald-400 flex items-center gap-1.5">
                                ✓ File Audio Siap: {{ basename($voiceoverAsset->storage_path) }}
                            </span>
                            <span class="text-[11px] px-2 py-0.5 rounded bg-slate-800 text-slate-300 border border-slate-700 font-mono">
                                {{ $voiceoverAsset->author }}
                            </span>
                        </div>
                        <span class="text-xs text-slate-400 font-mono">{{ round(($voiceoverAsset->metadata['file_size'] ?? 0) / 1024) }} KB</span>
                    </div>
                    <audio controls class="w-full h-10 accent-indigo-500 rounded-lg">
                        <source src="/{{ $voiceoverAsset->storage_path }}" type="audio/mpeg">
                        Browser Anda tidak mendukung tag audio HTML5.
                    </audio>
                    @if(isset($voiceoverAsset->metadata['srt_path']))
                        <div class="text-[11px] text-slate-400 flex items-center gap-1.5 font-mono">
                            <span>📄 Subtitle Sinkron (SRT):</span>
                            <span class="text-indigo-400">{{ $voiceoverAsset->metadata['srt_path'] }}</span>
                        </div>
                    @endif
                </div>
            @else
                <div class="p-4 rounded-xl bg-slate-950/50 border border-slate-800 text-center text-xs text-slate-500">
                    Klik tombol di atas untuk membuat rekaman voiceover neural otomatis dalam bahasa {{ $supportedLanguages[$script->language]['name'] ?? strtoupper($script->language) }}.
                </div>
            @endif
        </div>

        <!-- Scene by Scene Script Breakdown -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-bold text-white flex items-center gap-2" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                    <span>🎬 Storyboard & Scene Breakdown</span>
                    <span class="text-xs font-normal text-slate-400 font-mono">({{ count($script->script_json['scenes'] ?? []) }} Adegan)</span>
                </h3>
                <span class="text-xs text-indigo-300 font-medium bg-indigo-950/60 px-2.5 py-1 rounded-lg border border-indigo-800/40">
                    Bahasa Naskah: {{ $supportedLanguages[$script->language]['name'] ?? strtoupper($script->language) }}
                </span>
            </div>

            <div class="space-y-4">
                @foreach($script->script_json['scenes'] ?? [] as $scene)
                    <div class="bg-slate-900/90 rounded-2xl border border-slate-800 p-6 space-y-3 shadow-lg">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded-lg bg-indigo-600 text-white text-xs font-black">
                                    Scene {{ $scene['scene_number'] }}
                                </span>
                                <h4 class="text-sm font-bold text-white" style="font-family: 'Plus Jakarta Sans', sans-serif;">{{ $scene['title'] }}</h4>
                            </div>
                            <span class="text-xs font-mono text-slate-400 bg-slate-950 px-2 py-0.5 rounded border border-slate-800">
                                ⏱️ {{ $scene['duration_seconds'] }}s
                            </span>
                        </div>

                        <!-- Narrator Voiceover Text -->
                        <div class="p-4 rounded-xl bg-slate-950/80 border border-slate-800 space-y-1">
                            <span class="text-[10px] uppercase font-bold text-indigo-400 tracking-wider">🎙️ Voiceover (Narasi Suara):</span>
                            <p class="text-sm text-slate-200 leading-relaxed italic">
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
            <div class="text-4xl">📝</div>
            <h4 class="text-base font-bold text-white">Naskah Belum Dibuat</h4>
            <p class="text-xs max-w-md mx-auto">
                Silakan pilih bahasa (Bahasa Indonesia, English, dll.) dan suara voiceover pada panel di atas, lalu klik <strong>⚡ Generate Naskah & Siapkan Audio</strong>.
            </p>
        </div>
    @endif

    <!-- Dynamic Voices JavaScript helper -->
    <script>
        const supportedLanguagesData = @json($supportedLanguages);

        function syncLanguageVoices(selectedLang) {
            const voiceSelect = document.getElementById('script-voice');
            if (!voiceSelect || !supportedLanguagesData[selectedLang]) return;

            voiceSelect.innerHTML = '';
            const voices = supportedLanguagesData[selectedLang].voices || {};
            const defaultVoice = supportedLanguagesData[selectedLang].default_voice;

            for (const [vKey, vLabel] of Object.entries(voices)) {
                const opt = document.createElement('option');
                opt.value = vKey;
                opt.textContent = vLabel;
                if (vKey === defaultVoice) {
                    opt.selected = true;
                }
                voiceSelect.appendChild(opt);
            }
        }

        // Initialize voices on load
        document.addEventListener('DOMContentLoaded', function () {
            const langSelect = document.getElementById('script-language');
            if (langSelect) {
                syncLanguageVoices(langSelect.value);
            }
        });
    </script>

@endsection
