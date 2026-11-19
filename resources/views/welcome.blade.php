<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>URL Shortener</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-900">
    <main class="mx-auto flex min-h-screen max-w-3xl flex-col items-center justify-center px-6 text-center">
        <span class="grid h-14 w-14 place-items-center rounded-2xl bg-indigo-600 text-xl font-bold text-white">U</span>
        <h1 class="mt-5 text-3xl font-bold tracking-tight">URL Shortener</h1>
        <p class="mt-2 text-slate-600">Create and manage short links for your company.</p>
        <a href="{{ route('login') }}" class="mt-6 rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-indigo-700">Sign in</a>
    </main>
</body>
</html>
