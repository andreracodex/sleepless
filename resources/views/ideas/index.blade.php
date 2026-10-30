@extends('layouts.app')

@section('title', 'Idea Board — ' . $channel->channel_name)

@section('header_title', 'Phase 2: Idea Board')
@section('header_subtitle', 'Channel Pilot: ' . $channel->channel_name . ' (' . $channel->handle . ')')

@section('header_actions')
    <a href="{{ route('niches.index') }}" class="text-xs px-3 py-1.5 rounded-lg bg-slate-800 text-slate-300 hover:text-white hover:bg-slate-700 transition border border-slate-700">
        ← Niche Lab
    </a>
    <button onclick="document.getElementById('newIdeaModal').classList.remove('hidden')" class="text-xs px-3.5 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-semibold transition shadow-lg shadow-indigo-600/30 flex items-center gap-1.5">
        <span>+ Buat Ide Baru</span>
    </button>
@endsection

@section('content')

    <!-- Channel Strategy & Brand Banner -->
    <div class="rounded-2xl p-6 bg-gradient-to-r from-slate-900 via-cyan-950/40 to-slate-900 border border-cyan-500/20 shadow-xl space-y-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-cyan-500/20 text-cyan-300 border border-cyan-500/30">
                        Active Pilot Channel
                    </span>
                    <span class="text-xs text-slate-400">Target Region: {{ $channel->target_region }} • Bahasa: {{ strtoupper($channel->primary_language) }}</span>
                </div>
                <h2 class="text-2xl font-extrabold text-white mt-1">{{ $channel->channel_name }}</h2>
                <p class="text-xs text-slate-300 mt-1 max-w-3xl">
                    {{ $channel->brand_config['tone_of_voice'] ?? '' }}. {{ $channel->brand_config['editorial_policy'] ?? '' }}
                </p>
            </div>
            <div class="flex gap-2">
                <span class="text-xs px-3 py-1.5 rounded-lg bg-slate-950 border border-slate-800 text-slate-300 flex items-center gap-1.5">
                    <span>🔒 Default Upload: Private</span>
                </span>
            </div>
        </div>

        <!-- Stats Ribbon -->
        <div class="grid grid-cols-2 sm:grid-cols-6 gap-3 pt-4 border-t border-slate-800 text-center">
            <div class="bg-slate-950/70 rounded-xl p-3 border border-slate-850">
                <span class="text-[10px] uppercase font-semibold text-slate-400">Total Ide</span>
                <div class="text-2xl font-extrabold text-white mt-0.5">{{ $stats['total'] }}</div>
            </div>
            <div class="bg-slate-950/70 rounded-xl p-3 border border-slate-850">
                <span class="text-[10px] uppercase font-semibold text-slate-400">Long-form</span>
                <div class="text-2xl font-extrabold text-indigo-400 mt-0.5">{{ $stats['long_form'] }}</div>
            </div>
            <div class="bg-slate-950/70 rounded-xl p-3 border border-slate-850">
                <span class="text-[10px] uppercase font-semibold text-slate-400">Shorts</span>
                <div class="text-2xl font-extrabold text-cyan-400 mt-0.5">{{ $stats['shorts'] }}</div>
            </div>
            <div class="bg-slate-950/70 rounded-xl p-3 border border-slate-850">
                <span class="text-[10px] uppercase font-semibold text-slate-400">Disetujui</span>
                <div class="text-2xl font-extrabold text-emerald-400 mt-0.5">{{ $stats['accepted'] }}</div>
            </div>
            <div class="bg-slate-950/70 rounded-xl p-3 border border-slate-850">
                <span class="text-[10px] uppercase font-semibold text-slate-400">Tahap Riset</span>
                <div class="text-2xl font-extrabold text-amber-400 mt-0.5">{{ $stats['researching'] }}</div>
            </div>
            <div class="bg-slate-950/70 rounded-xl p-3 border border-slate-850">
                <span class="text-[10px] uppercase font-semibold text-slate-400">Dossier Approved</span>
                <div class="text-2xl font-extrabold text-emerald-300 mt-0.5">{{ $stats['approved_dossiers'] }}</div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center space-x-2">
            <a href="{{ route('ideas.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold @if(!$formatFilter) bg-indigo-600 text-white @else bg-slate-900 text-slate-300 hover:bg-slate-800 @endif transition border border-slate-800">
                Semua Format
            </a>
            <a href="{{ route('ideas.index', ['format' => 'long_form']) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold @if($formatFilter === 'long_form') bg-indigo-600 text-white @else bg-slate-900 text-slate-300 hover:bg-slate-800 @endif transition border border-slate-800">
                📺 Long-form (16:9)
            </a>
            <a href="{{ route('ideas.index', ['format' => 'shorts']) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold @if($formatFilter === 'shorts') bg-indigo-600 text-white @else bg-slate-900 text-slate-300 hover:bg-slate-800 @endif transition border border-slate-800">
                📱 Shorts (9:16)
            </a>
        </div>

        <div class="text-xs text-slate-400">
            Menampilkan {{ $ideas->count() }} ide terencana
        </div>
    </div>

    <!-- Idea Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($ideas as $idea)
            <div class="bg-slate-900/90 rounded-2xl border @if($idea->priority === 'critical') border-rose-500/50 @elseif($idea->status === 'researching') border-indigo-500/50 @else border-slate-800 @endif shadow-xl p-6 flex flex-col justify-between space-y-4 hover:border-slate-700 transition">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase @if($idea->format === 'long_form') bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 @else bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 @endif">
                            {{ $idea->format === 'long_form' ? '📺 Long-form' : '📱 Shorts' }} • {{ gmdate("i:s", $idea->target_duration_seconds) }}
                        </span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase @if($idea->priority === 'critical') bg-rose-500/20 text-rose-400 @elseif($idea->priority === 'high') bg-amber-500/20 text-amber-400 @else bg-slate-800 text-slate-300 @endif">
                            {{ $idea->priority }}
                        </span>
                    </div>

                    <h3 class="text-base font-bold text-white leading-snug">
                        {{ $idea->title_idea }}
                    </h3>

                    <div class="text-xs text-slate-300 space-y-2">
                        <p><strong class="text-slate-400">Angle:</strong> {{ $idea->angle }}</p>
                        <p><strong class="text-slate-400">Audience Promise:</strong> {{ $idea->audience_promise }}</p>
                    </div>

                    @if(!empty($idea->keyword_set))
                        <div class="flex flex-wrap gap-1.5 pt-2">
                            @foreach($idea->keyword_set as $kw)
                                <span class="text-[10px] px-2 py-0.5 rounded-md bg-slate-950 text-slate-400 border border-slate-800">
                                    #{{ $kw }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="pt-4 border-t border-slate-800 flex items-center justify-between">
                    <div class="text-xs font-semibold">
                        <span class="px-2 py-0.5 rounded uppercase text-[10px] @if($idea->status === 'researching') bg-amber-500/20 text-amber-300 @elseif($idea->status === 'accepted') bg-emerald-500/20 text-emerald-300 @elseif($idea->status === 'scripting') bg-indigo-500/20 text-indigo-300 @else bg-slate-800 text-slate-400 @endif">
                            {{ $idea->status }}
                        </span>
                    </div>

                    @if($idea->researchDossier)
                        <a href="{{ route('ideas.dossier', $idea->id) }}" class="px-3 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition flex items-center gap-1 shadow">
                            📑 Buka Dossier ({{ count($idea->researchDossier->claim_ledger) }} Klaim)
                        </a>
                    @else
                        <a href="{{ route('ideas.dossier', $idea->id) }}" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold transition">
                            🔬 Mulai Riset
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center text-slate-500 text-sm">
                Belum ada ide konten yang terdaftar.
            </div>
        @endforelse
    </div>

    <!-- Modal: Buat Ide Baru -->
    <div id="newIdeaModal" class="hidden fixed inset-0 z-50 bg-black/75 flex items-center justify-center p-4">
        <div class="bg-slate-900 rounded-2xl border border-slate-800 max-w-lg w-full p-6 space-y-4 shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-base font-bold text-white">Tambah Ide Konten Baru</h3>
                <button onclick="document.getElementById('newIdeaModal').classList.add('hidden')" class="text-slate-400 hover:text-white">✕</button>
            </div>
            <form action="{{ route('ideas.store') }}" method="POST" class="space-y-3">
                @csrf
                <input type="hidden" name="channel_id" value="{{ $channel->id }}">

                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-300">Judul Ide Video</label>
                    <input type="text" name="title_idea" placeholder="mis. EV Solid-State Batteries: Timeline & Facts" required class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-xs text-white">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-300">Format</label>
                        <select name="format" class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-xs text-white">
                            <option value="long_form">📺 Long-form (16:9)</option>
                            <option value="shorts">📱 Shorts (9:16)</option>
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-300">Durasi Target (detik)</label>
                        <input type="number" name="target_duration_seconds" value="600" min="15" max="3600" required class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-xs text-white">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-300">Angle / Sudut Pandang Unik</label>
                    <textarea name="angle" rows="2" placeholder="Fokus pada pengujian laboratorium independen vs klaim marketing..." required class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-xs text-white"></textarea>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-300">Audience Promise (Janji Manfaat ke Penonton)</label>
                    <textarea name="audience_promise" rows="2" placeholder="Penonton tahu kapan teknologi ini benar-benar bisa dibeli di pasar..." required class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-xs text-white"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-300">Keywords (pisahkan koma)</label>
                        <input type="text" name="keywords" placeholder="ev, solid state, battery" class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-xs text-white">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-300">Prioritas</label>
                        <select name="priority" class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-xs text-white">
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="critical">Critical (Pilot Priority)</option>
                            <option value="low">Low</option>
                        </select>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-800 flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('newIdeaModal').classList.add('hidden')" class="px-4 py-2 rounded-lg bg-slate-800 text-slate-300 text-xs">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs">Simpan Ide</button>
                </div>
            </form>
        </div>
    </div>
@endsection
