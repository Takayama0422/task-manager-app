<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'カリキュラム進捗管理')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>

<body class="bg-slate-50 text-slate-800 antialiased min-h-screen">

    <div class="max-w-7xl mx-auto p-4 md:p-8">

        <x-flash-message />

        @yield('content')

    </div>

    @stack('scripts')

</body>

</html>
