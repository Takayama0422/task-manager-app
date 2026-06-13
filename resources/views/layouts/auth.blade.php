<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'カリキュラム管理')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>

<body class="bg-slate-50 text-slate-800 antialiased flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-md">
        @yield('content')
    </div>

</body>

</html>
