<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>カリキュラム進捗管理</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50 text-slate-800 antialiased min-h-screen p-4 md:p-8">

    <div class="max-w-7xl mx-auto">

        @if(session('success'))
            <div
                class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-sm font-bold shadow-sm flex items-center gap-2">
                ✨ {{ session('success') }}
            </div>
        @endif

        <header
            class="mb-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-200 pb-6">
            <div class="text-center sm:text-left">
                <h1 class="text-3xl font-black tracking-tight text-slate-900">カリキュラム進捗管理</h1>
                <p class="text-sm text-slate-500 mt-1">各チュートリアルをクリックして、チャプターごとの進捗を記録・確認できます。</p>
            </div>

            <div class="flex flex-col items-center sm:items-end gap-3 min-w-[180px]">
                <div class="flex items-center gap-3 text-xs">
                    <span class="font-bold text-slate-600">👤 {{ auth()->user()->name }} さん</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit"
                            class="font-bold text-rose-500 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 px-2.5 py-1 rounded-md transition-colors">
                            ログアウト
                        </button>
                    </form>
                </div>

                <div
                    class="flex items-center justify-center bg-white border border-slate-100 rounded-2xl p-4 shadow-sm w-full">
                    <div class="text-center w-full">
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Total
                            Progress</span>
                        <div class="flex items-baseline justify-center gap-1">
                            <span class="text-4xl font-black text-indigo-600 tracking-tight">{{ $progressRate }}</span>
                            <span class="text-sm font-bold text-indigo-500">%</span>
                        </div>
                        <div class="w-full bg-slate-100 h-2 rounded-full mt-2 overflow-hidden">
                            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 h-full transition-all duration-500"
                                style="width: {{ $progressRate }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        @if($flaggedChapters->count() > 0)
            <div class="mb-10 bg-rose-50/60 border border-rose-100 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center gap-2 mb-4">
                    <span class="flex h-3 w-3 relative">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-rose-500"></span>
                    </span>
                    <h2 class="text-base font-black text-rose-900">⚠️ 質問中・要確認のチャプター（{{ $flaggedChapters->count() }}件）</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($flaggedChapters as $chapter)
                        @php $parentTitle = $categories[$chapter->major_id] ?? '未定義の教材'; @endphp
                        <div
                            class="flex items-start justify-between bg-white border border-rose-100 rounded-xl p-3 shadow-sm hover:border-rose-200 transition-colors">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="text-[9px] font-mono font-bold bg-rose-100 text-rose-700 px-1.5 py-0.5 rounded">
                                        ID: {{ $chapter->major_id }}-{{ $chapter->mid_sort }}-{{ $chapter->chapter_no }}
                                    </span>
                                    <span class="text-xs font-bold text-slate-500 truncate">{{ $parentTitle }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @php
            $cardGradients = [
                1 => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                2 => 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
                3 => 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
                4 => 'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
                5 => 'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
                6 => 'linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%)',
                7 => 'linear-gradient(135deg, #fccb90 0%, #d57eeb 100%)',
                8 => 'linear-gradient(135deg, #e0c3fc 0%, #8ec5fc 100%)',
                9 => 'linear-gradient(135deg, #f6d365 0%, #fda085 100%)',
                10 => 'linear-gradient(135deg, #89f7fe 0%, #66a6ff 100%)',
                11 => 'linear-gradient(135deg, #fddb92 0%, #d1fdff 100%)',
                12 => 'linear-gradient(135deg, #a1c4fd 0%, #c2e9fb 100%)',
                13 => 'linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%)',
            ];
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($textbooks as $majorId => $group)
                @php
                    $categoryName = $categories[$majorId] ?? '未定義の教材';
                    $groupTotal = $group->count();
                    $groupCompleted = $group->filter(function ($item) {
                        return $item->progressLog && $item->progressLog->status == 2;
                    })->count();
                    $cardProgress = $groupTotal > 0 ? round(($groupCompleted / $groupTotal) * 100) : 0;
                    $gradient = $cardGradients[$majorId] ?? 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
                @endphp

                <a href="{{ route('textbooks.show', $majorId) }}"
                    class="group block bg-white rounded-2xl overflow-hidden shadow-sm border border-slate-100 hover:shadow-md hover:border-slate-200 transition-all duration-300">

                    {{-- グラデーション帯（TOPICラベルのみ） --}}
                    <div class="px-4 py-3" style="background: {{ $gradient }}">
                        <span class="text-[10px] font-mono font-bold tracking-wider text-white/85">
                            TOPIC {{ sprintf('%02d', $majorId) }}
                        </span>
                    </div>

                    {{-- 白地エリア --}}
                    <div class="px-4 pt-3 pb-4 flex flex-col gap-3">
                        <div class="flex items-start justify-between gap-2">
                            <h3
                                class="text-sm font-black text-slate-800 tracking-tight group-hover:text-indigo-600 transition-colors line-clamp-2 leading-snug">
                                {{ $categoryName }}
                            </h3>

                            @can('admin')
                                <object class="shrink-0">
                                    <a href="{{ route('textbooks.edit', $majorId) }}"
                                        class="text-xs font-bold text-slate-400 hover:text-indigo-600 bg-slate-50 hover:bg-indigo-50 px-2 py-1 rounded-lg border border-slate-200/60 transition-all whitespace-nowrap">
                                        ⚙️ 編集
                                    </a>
                                </object>
                            @endcan
                        </div>

                        <div>
                            <div
                                class="text-right text-[11px] font-medium text-slate-400 mb-1 group-hover:text-indigo-400 transition-colors">
                                {{ $cardProgress }}%
                            </div>
                            <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500"
                                    style="width: {{ $cardProgress }}%; background: {{ $gradient }}"></div>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

    </div>

</body>

</html>