{{-- x-app-header --}}
{{-- Props: $progressRate (int, optional) --}}
@props(['progressRate' => null])

<header class="mb-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-200 pb-6">
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

        @if (!is_null($progressRate))
            <div class="flex items-center justify-center bg-white border border-slate-100 rounded-2xl p-4 shadow-sm w-full">
                <div class="text-center w-full">
                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Total Progress</span>
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
        @endif
    </div>
</header>
