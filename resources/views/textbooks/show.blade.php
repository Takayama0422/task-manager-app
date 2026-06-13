@extends('layouts.app')

@section('title', $currentTitle . ' - 詳細進捗管理')

@section('content')

    {{-- 戻るボタン --}}
    <div class="mb-6">
        <a href="{{ route('textbooks.index') }}"
            class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            ダッシュボードに戻る
        </a>
    </div>

    {{-- ヘッダー --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 mb-8">
        <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-md uppercase tracking-wider">
            tutorial: {{ $majorId }}
        </span>
        <h1 class="text-2xl font-black text-slate-900 mt-2">{{ $currentTitle }}</h1>
        <p class="text-xs text-slate-400 mt-1">チャプターをクリックして展開し、各小項目の進捗ステータスやメモをシームレスに更新できます。</p>
    </div>

    {{-- アコーディオンエリア --}}
    <div class="space-y-4">
        @php
            $groupedTextbooks = $textbooks->groupBy('mid_sort');
        @endphp

        @foreach ($groupedTextbooks as $midSort => $chapters)
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden accordion-item">

                <button type="button"
                    class="w-full flex items-center justify-between p-4 bg-slate-50 hover:bg-slate-100/80 font-bold text-slate-700 text-sm transition-colors cursor-pointer outline-none select-none accordion-trigger">
                    <span class="flex items-center gap-2">
                        <span class="bg-indigo-100 text-indigo-700 text-[10px] px-2 py-0.5 rounded font-bold">チャプター {{ $midSort }}</span>
                        <span class="text-slate-700 font-black">のカリキュラム</span>
                    </span>
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 transform transition-transform duration-200 arrow-icon text-slate-400"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div class="accordion-content hidden border-t border-slate-100 divide-y divide-slate-100">
                    @foreach ($chapters as $chapter)
                        <div class="p-4 hover:bg-slate-50/40 transition-all">

                            <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
                                <div class="flex-1">
                                    <span class="text-xs font-mono font-bold text-slate-400 mr-2 bg-slate-100 px-1.5 py-0.5 rounded">
                                        {{ $chapter->major_id }}-{{ $chapter->mid_sort }}-{{ $chapter->chapter_no }}
                                    </span>
                                    <span class="text-sm font-semibold text-slate-800">
                                        チャプター {{ $chapter->chapter_no }}
                                    </span>
                                </div>

                                <div class="flex flex-wrap items-center gap-4">
                                    <label class="inline-flex items-center text-xs cursor-pointer select-none">
                                        <input type="checkbox"
                                            class="flag-checkbox rounded border-slate-300 text-rose-600 focus:ring-rose-500 mr-1.5 transition-colors"
                                            data-id="{{ $chapter->id }}"
                                            {{ $chapter->progressLog && $chapter->progressLog->is_flagged ? 'checked' : '' }}>
                                        <span class="text-slate-600 font-bold">⚠️ 質問あり</span>
                                    </label>

                                    <select
                                        class="status-select text-xs font-bold rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-slate-700 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 cursor-pointer outline-none"
                                        data-id="{{ $chapter->id }}">
                                        <option value="0" {{ !$chapter->progressLog || $chapter->progressLog->status == 0 ? 'selected' : '' }}>💤 未着手</option>
                                        <option value="1" {{ $chapter->progressLog && $chapter->progressLog->status == 1 ? 'selected' : '' }}>⚡ 学習中</option>
                                        <option value="2" {{ $chapter->progressLog && $chapter->progressLog->status == 2 ? 'selected' : '' }}>✅ 完了</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-3">
                                <textarea
                                    class="memo-textarea w-full rounded-lg border border-slate-200 bg-slate-50/50 p-2.5 text-xs text-slate-700 placeholder-slate-400 focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition-all resize-none"
                                    rows="2"
                                    data-id="{{ $chapter->id }}"
                                    placeholder="💡 コマンドや重要箇所の個人メモ...（枠外クリックで自動保存）">{{ $chapter->progressLog ? $chapter->progressLog->memo : '' }}</textarea>
                            </div>

                        </div>
                    @endforeach
                </div>

            </div>
        @endforeach
    </div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {

        // アコーディオン開閉ロジック
        document.querySelectorAll('.accordion-trigger').forEach(trigger => {
            trigger.addEventListener('click', (e) => {
                const content = e.currentTarget.nextElementSibling;
                const arrow = e.currentTarget.querySelector('.arrow-icon');
                if (content) content.classList.toggle('hidden');
                if (arrow) arrow.classList.toggle('rotate-180');
            });
        });

        // 自動保存ロジック
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const sendProgressUpdate = async (id) => {
            const statusSelect = document.querySelector(`.status-select[data-id="${id}"]`);
            const flagCheckbox = document.querySelector(`.flag-checkbox[data-id="${id}"]`);
            const memoTextarea = document.querySelector(`.memo-textarea[data-id="${id}"]`);

            if (!statusSelect || !flagCheckbox || !memoTextarea) return;

            try {
                const res = await fetch(`/textbooks/${id}/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        status: statusSelect.value,
                        is_flagged: flagCheckbox.checked,
                        memo: memoTextarea.value
                    })
                });
                if (!res.ok) throw new Error();
            } catch (e) {
                alert('データの保存に失敗しました。再試行してください。');
            }
        };

        document.querySelectorAll('.status-select').forEach(el =>
            el.addEventListener('change', (e) => sendProgressUpdate(e.target.dataset.id)));
        document.querySelectorAll('.flag-checkbox').forEach(el =>
            el.addEventListener('change', (e) => sendProgressUpdate(e.target.dataset.id)));
        document.querySelectorAll('.memo-textarea').forEach(el =>
            el.addEventListener('blur', (e) => sendProgressUpdate(e.target.dataset.id)));
    });
</script>
@endpush
