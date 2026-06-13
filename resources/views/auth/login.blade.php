@extends('layouts.auth')

@section('title', 'ログイン')

@section('content')
    <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
        <h2 class="text-2xl font-black text-slate-950 mb-6 text-center">カリキュラム管理 ログイン</h2>

        <x-error-message />

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Email Address</label>
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
@endsection
