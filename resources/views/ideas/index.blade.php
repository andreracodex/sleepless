<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Idea Board — {{ $channel->channel_name }}</title>
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
                <a href="{{ route('niches.index') }}" class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-600 to-indigo-400 flex items-center justify-center shadow-lg shadow-brand-500/20 font-black text-xl text-white">
                    ▶
                </a>
                <div>
                    <h1 class="text-lg font-bold text-white tracking-tight flex items-center gap-2">
                        YouTube Auto Content Studio
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-cyan-500/10 text-cyan-400 border border-cyan-500/20">Phase 2: Content Planning</span>
                    </h1>
                    <p class="text-xs text-slate-400">Channel Pilot: {{ $channel->channel_name }} ({{ $channel->handle }})</p>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <a href="{{ route('niches.index') }}" class="text-xs px-3 py-1.5 rounded-lg bg-slate-800 text-slate-300 hover:text-white hover:bg-slate-700 transition border border-slate-700">
                    ← Kembali ke Niche Lab
                </a>
                <button onclick="document.getElementById('newIdeaModal').classList.remove('hidden')" class="text-xs px-3.5 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-semibold transition shadow-lg shadow-indigo-600/30 flex items-center gap-1.5">
                    <span>+ Buat Ide Baru</span>
                </button>
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

        <!-- Channel Strategy & Brand Banner -->
        <div class="rounded-2xl p-6 bg-gradient-to-r from-slate-800 via-cyan-950/40 to-slate-800 border border-cyan-500/20 shadow-xl space-y-4">
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
                    <span class="text-xs px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-slate-300 flex items-center gap-1.5">
                        <span>🔒 Default Upload: Private</span>
                    </span>
                </div>
            </div>

            <!-- Stats Ribbon -->
            <div class="grid grid-cols-2 sm:grid-cols-6 gap-3 pt-4 border-t border-slate-700/60 text-center">
                <div class="bg-slate-900/60 rounded-xl p-3 border border-slate-800">
                    <span class="text-[10px] uppercase font-semibold text-slate-400">Total Ide</span>
                    <div class="text-2xl font-extrabold text-white mt-0.5">{{ $stats['total'] }}</div>
                </div>
                <div class="bg-slate-900/60 rounded-xl p-3 border border-slate-800">
                    <span class="text-[10px] uppercase font-semibold text-slate-400">Long-form</span>
                    <div class="text-2xl font-extrabold text-indigo-400 mt-0.5">{{ $stats['long_form'] }}</div>
                </div>
                <div class="bg-slate-900/60 rounded-xl p-3 border border-slate-800">
                    <span class="text-[10px] uppercase font-semibold text-slate-400">Shorts</span>
                    <div class="text-2xl font-extrabold text-cyan-400 mt-0.5">{{ $stats['shorts'] }}</div>
                </div>
                <div class="bg-slate-900/60 rounded-xl p-3 border border-slate-800">
                    <span class="text-[10px] uppercase font-semibold text-slate-400">Disetujui</span>
                    <div class="text-2xl font-extrabold text-emerald-400 mt-0.5">{{ $stats['accepted'] }}</div>
                </div>
                <div class="bg-slate-900/60 rounded-xl p-3 border border-slate-800">
                    <span class="text-[10px] uppercase font-semibold text-slate-400">Tahap Riset</span>
                    <div class="text-2xl font-extrabold text-amber-400 mt-0.5">{{ $stats['researching'] }}</div>
                </div>
                <div class="bg-slate-900/60 rounded-xl p-3 border border-slate-800">
                    <span class="text-[10px] uppercase font-semibold text-slate-400">Dossier Approved</span>
                    <div class="text-2xl font-extrabold text-emerald-300 mt-0.5">{{ $stats['approved_dossiers'] }}</div>
                </div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center space-x-2">
                <a href="{{ route('ideas.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold @if(!$formatFilter) bg-indigo-600 text-white @else bg-slate-800 text-slate-300 hover:bg-slate-700 @endif transition">
                    Semua Format
                </a>
                <a href="{{ route('ideas.index', ['format' => 'long_form']) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold @if($formatFilter === 'long_form') bg-indigo-600 text-white @else bg-slate-800 text-slate-300 hover:bg-slate-700 @endif transition">
                    📺 Long-form (16:9)
                </a>
                <a href="{{ route('ideas.index', ['format' => 'shorts']) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold @if($formatFilter === 'shorts') bg-indigo-600 text-white @else bg-slate-800 text-slate-300 hover:bg-slate-700 @endif transition">
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
                <div class="bg-slate-800/80 rounded-2xl border @if($idea->priority === 'critical') border-rose-500/50 @elseif($idea->status === 'researching') border-indigo-500/50 @else border-slate-700/80 @endif shadow-xl p-6 flex flex-col justify-between space-y-4 hover:border-slate-600 transition">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase @if($idea->format === 'long_form') bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 @else bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 @endif">
                                {{ $idea->format === 'long_form' ? '📺 Long-form' : '📱 Shorts' }} • {{ gmdate("i:s", $idea->target_duration_seconds) }}
                            </span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase @if($idea->priority === 'critical') bg-rose-500/20 text-rose-400 @elseif($idea->priority === 'high') bg-amber-500/20 text-amber-400 @else bg-slate-700 text-slate-300 @endif">
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
                                    <span class="text-[10px] px-2 py-0.5 rounded-md bg-slate-900 text-slate-400 border border-slate-700">
                                        #{{ $kw }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="pt-4 border-t border-slate-700/60 flex items-center justify-between">
                        <div class="text-xs font-semibold">
                            <span class="px-2 py-0.5 rounded uppercase text-[10px] @if($idea->status === 'researching') bg-amber-500/20 text-amber-300 @elseif($idea->status === 'accepted') bg-emerald-500/20 text-emerald-300 @elseif($idea->status === 'scripting') bg-indigo-500/20 text-indigo-300 @else bg-slate-700 text-slate-400 @endif">
                                {{ $idea->status }}
                            </span>
                        </div>

                        @if($idea->researchDossier)
                            <a href="{{ route('ideas.dossier', $idea->id) }}" class="px-3 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition flex items-center gap-1 shadow">
                                📑 Buka Dossier ({{ count($idea->researchDossier->claim_ledger) }} Klaim)
                            </a>
                        @else
                            <a href="{{ route('ideas.dossier', $idea->id) }}" class="px-3 py-1.5 rounded-lg bg-slate-700 hover:bg-slate-600 text-slate-200 text-xs font-semibold transition">
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

    </main>

    <!-- Modal: Buat Ide Baru -->
    <div id="newIdeaModal" class="hidden fixed inset-0 z-50 bg-black/70 flex items-center justify-center p-4">
        <div class="bg-slate-800 rounded-2xl border border-slate-700 max-w-lg w-full p-6 space-y-4 shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-700 pb-3">
                <h3 class="text-base font-bold text-white">Tambah Ide Konten Baru</h3>
                <button onclick="document.getElementById('newIdeaModal').classList.add('hidden')" class="text-slate-400 hover:text-white">✕</button>
            </div>
            <form action="{{ route('ideas.store') }}" method="POST" class="space-y-3">
                @csrf
                <input type="hidden" name="channel_id" value="{{ $channel->id }}">

                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-300">Judul Ide Video</label>
                    <input type="text" name="title_idea" placeholder="mis. EV Solid-State Batteries: Timeline & Facts" required class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2 text-xs text-white">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-300">Format</label>
                        <select name="format" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2 text-xs text-white">
                            <option value="long_form">📺 Long-form (16:9)</option>
                            <option value="shorts">📱 Shorts (9:16)</option>
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-300">Durasi Target (detik)</label>
                        <input type="number" name="target_duration_seconds" value="600" min="15" max="3600" required class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2 text-xs text-white">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-300">Angle / Sudut Pandang Unik</label>
                    <textarea name="angle" rows="2" placeholder="Fokus pada pengujian laboratorium independen vs klaim marketing..." required class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2 text-xs text-white"></textarea>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-300">Audience Promise (Janji Manfaat ke Penonton)</label>
                    <textarea name="audience_promise" rows="2" placeholder="Penonton tahu kapan teknologi ini benar-benar bisa dibeli di pasar..." required class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2 text-xs text-white"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-300">Keywords (pisahkan koma)</label>
                        <input type="text" name="keywords" placeholder="ev, solid state, battery" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2 text-xs text-white">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-300">Prioritas</label>
                        <select name="priority" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2 text-xs text-white">
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="critical">Critical (Pilot Priority)</option>
                            <option value="low">Low</option>
                        </select>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-700 flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('newIdeaModal').classList.add('hidden')" class="px-4 py-2 rounded-lg bg-slate-700 text-slate-300 text-xs">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs">Simpan Ide</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
