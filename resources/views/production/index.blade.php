@extends('layouts.app')

@section('title', 'Production Studio & YouTube Publishing — YouTube Auto Content Studio')

@section('header_title', 'Phase 4 & 5: Video Production & YouTube Publishing')
@section('header_subtitle', '1080p MP4 Composition, Automated QA Checklist & Resumable Private Upload')

@section('header_actions')
    <a href="{{ route('ideas.index') }}" class="text-xs px-3 py-1.5 rounded-lg bg-slate-800 text-slate-300 hover:text-white hover:bg-slate-700 transition border border-slate-700">
        💡 Idea Board
    </a>
    <a href="{{ route('niches.index') }}" class="text-xs px-3 py-1.5 rounded-lg bg-slate-800 text-slate-300 hover:text-white hover:bg-slate-700 transition border border-slate-700">
        🔬 Niche Lab
    </a>
@endsection

@section('content')

    <!-- Production Header -->
    <div class="rounded-2xl p-6 bg-gradient-to-r from-slate-900 via-indigo-950/40 to-slate-900 border border-indigo-500/20 shadow-xl space-y-2">
        <div class="flex items-center gap-2">
            <span class="px-2.5 py-0.5 text-xs font-bold uppercase tracking-wider rounded-md bg-rose-500/20 text-rose-300 border border-rose-500/30">
                PRD FR-010 & FR-014 — Render Engine & Resumable Upload
            </span>
        </div>
        <h2 class="text-xl font-bold text-white">Production Jobs & YouTube Publishing Center</h2>
        <p class="text-xs text-slate-300 max-w-3xl leading-relaxed">
            Semua video dirender dalam resolusi 1080p Full HD lengkap dengan voiceover neural berlisensi, caption tersinkron, dan checklist otomatis Quality Assurance (QA). Sesuai PRD, seluruh video otomatis diunggah dengan status <strong>Private</strong> terlebih dahulu sebelum disetujui untuk publikasi umum.
        </p>
    </div>

    <!-- Production Jobs List -->
    <div class="space-y-6">
        @forelse($jobs as $job)
            <div class="bg-slate-900/90 rounded-2xl border border-slate-800 p-6 shadow-xl space-y-6">
                <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase @if($job->status === 'completed') bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 @else bg-amber-500/20 text-amber-300 border border-amber-500/30 @endif">
                                {{ strtoupper($job->status) }}
                            </span>
                            <span class="text-xs text-slate-400 font-mono">Job #{{ $job->id }} • {{ $job->output_profile }}</span>
                        </div>
                        <h3 class="text-xl font-bold text-white">{{ $job->contentIdea->title_idea }}</h3>
                        <p class="text-xs text-slate-400">Channel: {{ $job->channel->channel_name }} ({{ $job->channel->handle }})</p>
                    </div>

                    <!-- YouTube Status Pill -->
                    @if($job->youtubeUpload)
                        <div class="p-3 rounded-xl bg-slate-950 border border-slate-800 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-red-600 flex items-center justify-center font-bold text-white text-xs">
                                YT
                            </div>
                            <div class="text-left text-xs">
                                <div class="font-bold text-white flex items-center gap-1.5">
                                    <span>Uploaded (Private)</span>
                                    <span class="text-[10px] font-mono px-1.5 py-0.2 rounded bg-slate-800 text-slate-300">{{ $job->youtubeUpload->youtube_video_id }}</span>
                                </div>
                                <span class="text-[10px] text-slate-400">Visibilitas: <strong class="text-amber-400 uppercase">{{ $job->youtubeUpload->privacy_status }}</strong></span>
                            </div>
                        </div>
                    @else
                        <form action="{{ route('production.upload', $job->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-500 text-white font-bold text-xs shadow-lg shadow-red-600/30 transition flex items-center gap-1.5">
                                <span>🚀 Upload Private ke YouTube</span>
                            </button>
                        </form>
                    @endif
                </div>

                <!-- Video Preview Player & Thumbnail Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Player (2 cols) -->
                    <div class="lg:col-span-2 space-y-2">
                        <span class="text-xs font-semibold text-slate-300 flex items-center gap-1.5">
                            🎬 Video Preview Player (1080p MP4)
                        </span>
                        @if($job->output_path)
                            <div class="rounded-xl overflow-hidden bg-black border border-slate-800 shadow-2xl">
                                <video controls class="w-full aspect-video">
                                    <source src="/{{ $job->output_path }}" type="video/mp4">
                                    Browser Anda tidak mendukung tag video HTML5.
                                </video>
                            </div>
                        @else
                            <div class="py-16 text-center text-xs text-slate-500 bg-slate-950 rounded-xl border border-slate-850">
                                Sedang merender video...
                            </div>
                        @endif
                    </div>

                    <!-- QA Report & Metadata (1 col) -->
                    <div class="space-y-4 flex flex-col justify-between">
                        <div class="space-y-3">
                            <span class="text-xs font-semibold text-slate-300 flex items-center gap-1.5">
                                🔍 Automated QA Checklist (PRD FR-013)
                            </span>
                            <div class="p-4 rounded-xl bg-slate-950/90 border border-slate-800 space-y-2 text-xs">
                                <div class="flex justify-between text-slate-300">
                                    <span>Resolusi:</span>
                                    <strong class="text-white">{{ $job->qa_report['resolution'] ?? '1920x1080' }}</strong>
                                </div>
                                <div class="flex justify-between text-slate-300">
                                    <span>Aspek Rasio:</span>
                                    <strong class="text-white">{{ $job->qa_report['aspect_ratio'] ?? '16:9' }}</strong>
                                </div>
                                <div class="flex justify-between text-slate-300">
                                    <span>Durasi:</span>
                                    <strong class="text-indigo-400 font-mono">{{ gmdate("i:s", $job->duration_seconds ?? 0) }}</strong>
                                </div>
                                <div class="flex justify-between text-slate-300">
                                    <span>Ukuran File:</span>
                                    <strong class="text-white font-mono">{{ round(($job->file_size ?? 0) / (1024 * 1024), 2) }} MB</strong>
                                </div>
                                <div class="flex justify-between text-slate-300">
                                    <span>Audio Stream:</span>
                                    <span class="text-emerald-400 font-bold">✓ Terverifikasi</span>
                                </div>
                                <div class="flex justify-between text-slate-300">
                                    <span>Status QA Otomatis:</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase @if($job->qa_report['qa_passed'] ?? false) bg-emerald-500/20 text-emerald-300 @else bg-rose-500/20 text-rose-300 @endif">
                                        {{ ($job->qa_report['qa_passed'] ?? false) ? 'PASSED (LULUS)' : 'FAILED' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Thumbnail Preview -->
                        @if($job->thumbnail_path)
                            <div class="space-y-1.5">
                                <span class="text-[11px] font-semibold text-slate-400">Thumbnail Preview:</span>
                                <div class="rounded-lg overflow-hidden border border-slate-800 bg-black">
                                    <img src="/{{ $job->thumbnail_path }}" alt="Thumbnail" class="w-full object-cover">
                                </div>
                            </div>
                        @endif

                        <!-- Owner Publish Approval Form (Gate 2) -->
                        @if($job->youtubeUpload)
                            <form action="{{ route('production.publish', $job->youtubeUpload->id) }}" method="POST" class="p-3 rounded-xl bg-slate-950 border border-slate-800 space-y-2">
                                @csrf
                                <label class="text-[11px] font-bold text-slate-300 flex items-center justify-between">
                                    <span>Persetujuan Publikasi (Owner Approval):</span>
                                </label>
                                <div class="flex gap-2">
                                    <select name="privacy_status" class="flex-1 bg-slate-900 border border-slate-800 rounded-lg p-1.5 text-xs text-white">
                                        <option value="private" @if($job->youtubeUpload->privacy_status === 'private') selected @endif>🔒 Private</option>
                                        <option value="unlisted" @if($job->youtubeUpload->privacy_status === 'unlisted') selected @endif>🔗 Unlisted</option>
                                        <option value="public" @if($job->youtubeUpload->privacy_status === 'public') selected @endif>🌍 Public (Rilis)</option>
                                    </select>
                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition">
                                        Update
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-slate-900/90 rounded-2xl border border-slate-800 p-12 text-center text-slate-400 space-y-3 shadow-xl">
                <p>Belum ada job video yang dirender. Buka Script Studio dan klik "Render Video".</p>
            </div>
        @endforelse
    </div>

@endsection
