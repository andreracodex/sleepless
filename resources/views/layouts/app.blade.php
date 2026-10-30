<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'YouTube Auto Content Studio')</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            900: '#312e81',
                            950: '#1e1b4b',
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
<body class="bg-slate-950 text-slate-100 min-h-screen flex antialiased">

    <!-- Mobile Sidebar Backdrop -->
    <div id="mobileBackdrop" onclick="toggleSidebar()" class="fixed inset-0 bg-black/60 z-40 hidden lg:hidden"></div>

    <!-- LEFT SIDEBAR -->
    <aside id="sidebar" class="fixed top-0 bottom-0 left-0 z-50 w-72 bg-slate-900 border-r border-slate-800 flex flex-col justify-between transition-transform duration-300 -translate-x-full lg:translate-x-0">
        
        <!-- Top Section -->
        <div class="flex-1 flex flex-col min-h-0">
            
            <!-- Brand Header -->
            <div class="h-16 flex items-center justify-between px-5 border-b border-slate-800/80 bg-slate-900/60">
                <a href="{{ route('niches.index') }}" class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-brand-600 to-indigo-400 flex items-center justify-center shadow-lg shadow-brand-500/20 font-black text-lg text-white">
                        ▶
                    </div>
                    <div>
                        <h1 class="text-sm font-black tracking-tight text-white flex items-center gap-1.5">
                            AUTO STUDIO
                            <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">PRO</span>
                        </h1>
                        <p class="text-[10px] text-slate-400 font-medium tracking-wide">YouTube Automation Studio</p>
                    </div>
                </a>
                <button onclick="toggleSidebar()" class="text-slate-400 hover:text-white lg:hidden p-1">
                    ✕
                </button>
            </div>

            @php
                $activePilotNiche = \App\Models\NicheCandidate::with('latestVerifiedSnapshot')->where('status', 'selected')->first();
                $channel = \App\Models\Channel::first();
            @endphp
            <!-- Active Channel Badge -->
            <div class="p-4 mx-3 my-3 rounded-xl bg-slate-800/70 border border-slate-700/60 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-[9px] uppercase font-bold tracking-wider text-cyan-400">⚡ Active Pilot Channel</span>
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                </div>
                <div class="font-bold text-xs text-white mt-1 truncate">
                    {{ $activePilotNiche ? $activePilotNiche->name : ($channel->name ?? 'VoltWise: The EV Explainer') }}
                </div>
                <div class="flex items-center justify-between text-[10px] text-slate-400 mt-0.5">
                    <span>{{ $channel->handle ?? '@VoltWiseEV' }}</span>
                    <span class="text-emerald-400 font-semibold">
                        Score: {{ $activePilotNiche?->latestVerifiedSnapshot?->niche_score ?? 78 }}
                    </span>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-3 py-2 space-y-6 overflow-y-auto">
                
                <!-- Section: Strategy & Discovery -->
                <div>
                    <div class="px-3 mb-2 text-[10px] font-extrabold uppercase tracking-wider text-slate-500">
                        Strategy & Decision
                    </div>
                    <div class="space-y-1">
                        <a href="{{ route('niches.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('niches.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <div class="flex items-center gap-2.5">
                                <span class="text-base">🔬</span>
                                <span>Niche Lab</span>
                            </div>
                            <span class="text-[9px] px-1.5 py-0.5 rounded {{ request()->routeIs('niches.*') ? 'bg-indigo-700 text-white' : 'bg-slate-800 text-slate-400' }}">EV vs Sleep</span>
                        </a>
                    </div>
                </div>

                <!-- Section: Content Planning -->
                <div>
                    <div class="px-3 mb-2 text-[10px] font-extrabold uppercase tracking-wider text-slate-500">
                        Content Planning
                    </div>
                    <div class="space-y-1">
                        <a href="{{ route('ideas.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('ideas.index') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <div class="flex items-center gap-2.5">
                                <span class="text-base">💡</span>
                                <span>Idea Board</span>
                            </div>
                            <span class="text-[9px] px-1.5 py-0.5 rounded {{ request()->routeIs('ideas.index') ? 'bg-indigo-700 text-white' : 'bg-slate-800 text-slate-400' }}">5 Ide</span>
                        </a>

                        <a href="{{ route('ideas.dossier', isset($idea) ? $idea->id : 1) }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('ideas.dossier') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <div class="flex items-center gap-2.5">
                                <span class="text-base">📑</span>
                                <span>Research Dossier</span>
                            </div>
                            <span class="text-[9px] px-1.5 py-0.5 rounded {{ request()->routeIs('ideas.dossier') ? 'bg-indigo-700 text-white' : 'bg-emerald-500/20 text-emerald-400' }}">Claim Ledger</span>
                        </a>
                    </div>
                </div>

                <!-- Section: Production Studio -->
                <div>
                    <div class="px-3 mb-2 text-[10px] font-extrabold uppercase tracking-wider text-slate-500">
                        Production & Publish
                    </div>
                    <div class="space-y-1">
                        <a href="{{ route('scripts.show', isset($idea) ? $idea->id : 1) }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('scripts.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <div class="flex items-center gap-2.5">
                                <span class="text-base">✍️</span>
                                <span>Script & Audio</span>
                            </div>
                            <span class="text-[9px] px-1.5 py-0.5 rounded {{ request()->routeIs('scripts.*') ? 'bg-indigo-700 text-white' : 'bg-slate-800 text-slate-400' }}">Edge TTS</span>
                        </a>

                        <a href="{{ route('production.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('production.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <div class="flex items-center gap-2.5">
                                <span class="text-base">🎬</span>
                                <span>Production & QA</span>
                            </div>
                            <span class="text-[9px] px-1.5 py-0.5 rounded {{ request()->routeIs('production.*') ? 'bg-indigo-700 text-white' : 'bg-rose-500/20 text-rose-400' }}">1080p MP4</span>
                        </a>
                    </div>
                </div>

            </nav>

        </div>

        <!-- Sidebar Bottom Footer -->
        <div class="p-3 border-t border-slate-800 bg-slate-900/80 space-y-2">
            <div class="p-2.5 rounded-lg bg-slate-950/60 border border-slate-800/80 text-[10px] space-y-1">
                <div class="flex items-center justify-between text-slate-400">
                    <span>Engine:</span>
                    <strong class="text-slate-200">FFmpeg 7.1 + Edge-TTS</strong>
                </div>
                <div class="flex items-center justify-between text-slate-400">
                    <span>Database:</span>
                    <strong class="text-emerald-400">MariaDB Port 3308</strong>
                </div>
                <div class="flex items-center justify-between text-slate-400">
                    <span>Default Upload:</span>
                    <strong class="text-amber-400">Private (Protected)</strong>
                </div>
            </div>
            <a href="https://www.collabpals.com/tools/youtube-niche-finder" target="_blank" class="w-full py-1.5 px-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-[11px] font-medium flex items-center justify-center gap-1.5 transition border border-slate-700">
                <span>CollabPals Niche Finder</span>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
        </div>
    </aside>

    <!-- RIGHT MAIN CONTENT WRAPPER -->
    <div class="flex-1 lg:pl-72 flex flex-col min-w-0 min-h-screen">
        
        <!-- Top Sticky Header -->
        <header class="h-16 border-b border-slate-800 bg-slate-900/80 backdrop-blur sticky top-0 z-30 px-4 sm:px-6 lg:px-8 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <!-- Hamburger Button (Mobile) -->
                <button onclick="toggleSidebar()" class="lg:hidden p-2 rounded-lg bg-slate-800 text-slate-300 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                
                <div>
                    <h2 class="text-sm font-bold text-white leading-tight">
                        @yield('header_title', 'YouTube Auto Content Studio')
                    </h2>
                    <p class="text-[11px] text-slate-400 hidden sm:block">
                        @yield('header_subtitle', 'Automated Research, Scripting, Audio, Rendering & YouTube Uploads')
                    </p>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                @yield('header_actions')
            </div>
        </header>

        <!-- Main Body Content -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 space-y-8">
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

            @yield('content')
        </main>
    </div>

    <!-- Toggle Sidebar Script -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('mobileBackdrop');
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
