<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') · URL Shortener</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-800 antialiased">
    @php
        $role = auth()->user()->role;
        $roleLabel = match ($role) {
            'SuperAdmin' => 'Platform admin',
            'Admin' => 'Company Admin',
            'Member' => 'Company Member',
            'Sales' => 'Sales',
            'Manager' => 'Manager',
            default => 'Company user',
        };
        $homeRoute = match ($role) {
            'SuperAdmin' => 'superadmin.dashboard',
            'Admin' => 'admin.dashboard',
            'Member' => 'member.dashboard',
            'Sales' => 'sales.dashboard',
            'Manager' => 'manager.dashboard',
            default => 'dashboard',
        };
        $dashboardUrl = route($homeRoute);
        $navigation = match ($role) {
            'SuperAdmin' => [
                ['label' => 'Dashboard', 'mark' => 'D', 'url' => $dashboardUrl],
                ['label' => 'Clients', 'mark' => 'C', 'url' => $dashboardUrl.'#clients'],
                ['label' => 'Invite new client', 'mark' => '+', 'url' => $dashboardUrl.'#invite-client'],
                ['label' => 'All short URLs', 'mark' => 'U', 'url' => $dashboardUrl.'#all-links'],
            ],
            'Admin' => [
                ['label' => 'Dashboard', 'mark' => 'D', 'url' => $dashboardUrl],
                ['label' => 'Team', 'mark' => 'T', 'url' => $dashboardUrl.'#team'],
                ['label' => 'Invite team member', 'mark' => '+', 'url' => $dashboardUrl.'#invite-team'],
                ['label' => 'Company URLs', 'mark' => 'U', 'url' => $dashboardUrl.'#company-links'],
            ],
            'Member' => [
                ['label' => 'Dashboard', 'mark' => 'D', 'url' => $dashboardUrl],
                ['label' => 'Create short URL', 'mark' => '+', 'url' => $dashboardUrl.'#create-url'],
                ['label' => 'My URLs', 'mark' => 'U', 'url' => $dashboardUrl.'#my-links'],
            ],
            default => [
                ['label' => 'Dashboard', 'mark' => 'D', 'url' => $dashboardUrl],
                ['label' => 'Create short URL', 'mark' => '+', 'url' => $dashboardUrl.'#create-url'],
                ['label' => 'My URLs', 'mark' => 'U', 'url' => $dashboardUrl.'#my-links'],
            ],
        };
    @endphp

    <div class="min-h-screen lg:grid lg:grid-cols-[250px_minmax(0,1fr)]">
        <aside class="flex flex-col border-b border-slate-200 bg-white px-5 py-5 lg:min-h-screen lg:border-b-0 lg:border-r">
            <a href="{{ route($homeRoute) }}" class="flex items-center gap-3">
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-indigo-600 text-sm font-bold text-white">US</span>
                <span>
                    <span class="block text-lg font-bold tracking-tight text-slate-950">URL Shortener</span>
                    <span class="block text-xs text-slate-500">Company dashboard</span>
                </span>
            </a>

            <div class="mt-9">
                <p class="px-3 text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">Menu</p>
                <nav class="mt-3 space-y-1">
                    @foreach ($navigation as $item)
                        <a href="{{ $item['url'] }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-indigo-50 hover:text-indigo-700 {{ $loop->first ? 'bg-indigo-50 font-semibold text-indigo-700' : '' }}">
                            <span class="grid h-8 w-8 place-items-center rounded-lg bg-white text-sm font-semibold text-indigo-600">{{ $item['mark'] }}</span>
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>
            </div>

            <div class="mt-auto hidden rounded-2xl bg-slate-50 p-4 lg:block">
                <p class="text-sm font-semibold text-slate-800">{{ $roleLabel }}</p>
                <p class="mt-1 truncate text-xs text-slate-500">{{ auth()->user()->company?->name ?? 'Platform' }}</p>
            </div>
        </aside>

        <div class="min-w-0">
            <header class="flex min-h-[76px] items-center justify-between border-b border-slate-200 bg-white px-5 sm:px-8">
                <div>
                    <p class="text-xs font-medium text-slate-500">{{ $roleLabel }}</p>
                    <h1 class="text-lg font-semibold tracking-tight text-slate-950">@yield('page-heading', 'Dashboard')</h1>
                </div>
                <div class="flex items-center gap-3">
                    <div class="hidden text-right sm:block">
                        <p class="text-sm font-semibold text-slate-800">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-500">{{ auth()->user()->email }}</p>
                    </div>
                    <span class="grid h-10 w-10 place-items-center rounded-full bg-indigo-100 text-sm font-bold text-indigo-700">
                        {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                    </span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium text-slate-600 transition hover:border-slate-300 hover:bg-slate-50">Log out</button>
                    </form>
                </div>
            </header>

            <main class="mx-auto max-w-[1440px] px-5 py-8 sm:px-8">
                @if (session('status'))
                    <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800" role="status">
                        {{ session('status') }}
                    </div>
                @endif
                @if (session('invite_url'))
                    <div class="mb-6 rounded-xl border border-indigo-200 bg-white p-4 shadow-sm">
                        <label for="invite-url" class="mb-2 block text-sm font-semibold text-slate-800">Invitation link</label>
                        <div class="flex flex-col gap-2 sm:flex-row">
                            <input id="invite-url" readonly value="{{ session('invite_url') }}" class="min-w-0 flex-1 rounded-lg border-slate-300 bg-slate-50 text-sm text-slate-600">
                            <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('invite-url').value).then(() => { this.textContent = 'Copied'; setTimeout(() => this.textContent = 'Copy link', 1200); })" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Copy link</button>
                        </div>
                        <p class="mt-2 text-xs text-slate-500">Share this link with the invited user so they can set a password.</p>
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
