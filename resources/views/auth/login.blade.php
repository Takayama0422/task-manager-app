<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50 text-slate-800 antialiased flex items-center justify-center min-h-screen p-4">

    <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 w-full max-w-md">
        <h2 class="text-2xl font-black text-slate-950 mb-6 text-center">カリキュラム管理 ログイン</h2>

        @if ($errors->any())
            <div class="mb-4 p-3 bg-rose-50 border border-rose-100 text-rose-700 rounded-xl text-sm font-bold">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Email
                    Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:border-indigo-500 transition-colors">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Password</label>
                <input type="password" name="password" required
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:border-indigo-500 transition-colors">
            </div>

            <button type="submit"
                class="w-full py-3 bg-slate-900 text-white text-sm font-bold rounded-xl hover:bg-slate-800 transition-colors shadow-sm mt-2">
                ログイン
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('register') }}"
                class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition-colors">
                アカウントをお持ちでない方はこちら →
            </a>
        </div>
    </div>

</body>

</html>