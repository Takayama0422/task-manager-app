<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>教材名の編集</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50 text-slate-800 antialiased flex items-center justify-center min-h-screen p-4">

    <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 w-full max-w-md">
        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">SYSTEM
            ADMINISTRATION</span>
        <h2 class="text-xl font-black text-slate-950 mb-6">⚙️ 教材名の編集 (ID: {{ sprintf('%02d', $majorId) }})</h2>

        <form method="POST" action="{{ route('textbooks.updateName', $majorId) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">教材名（大項目名称）</label>
                <input type="text" name="category_name" value="{{ old('category_name', $categoryName) }}" required
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:border-indigo-500 transition-colors">
            </div>

            <div class="flex gap-3 pt-2">
                <a href="{{ route('textbooks.index') }}"
                    class="w-1/2 py-3 bg-slate-100 text-slate-600 text-center text-sm font-bold rounded-xl hover:bg-slate-200 transition-colors">
                    キャンセル
                </a>
                <button type="submit"
                    class="w-1/2 py-3 bg-slate-900 text-white text-sm font-bold rounded-xl hover:bg-slate-800 transition-colors shadow-sm">
                    更新する
                </button>
            </div>
        </form>
    </div>

</body>

</html>