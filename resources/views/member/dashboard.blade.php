@extends('layouts.dashboard')

@section('title', 'My dashboard')
@section('page-heading', 'My links')

@section('content')
    <div class="mb-7 flex flex-col justify-between gap-3 sm:flex-row sm:items-end">
        <div>
            <p class="text-sm font-medium text-indigo-600">{{ auth()->user()->company?->name ?? 'No company assigned' }}</p>
            <h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-950">Welcome back, {{ auth()->user()->name }}</h2>
            <p class="mt-1 text-sm text-slate-500">Create new links and manage the links you have made.</p>
        </div>
        <span class="w-fit rounded-full border border-sky-100 bg-sky-50 px-3 py-1.5 text-xs font-semibold text-sky-700">Member</span>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm shadow-slate-100/70">
            <p class="text-sm font-medium text-slate-500">My links</p>
            <p class="mt-3 text-3xl font-bold tracking-tight text-slate-950">{{ number_format($urlCount) }}</p>
            <p class="mt-2 text-xs text-slate-500">Links you have created</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm shadow-slate-100/70">
            <p class="text-sm font-medium text-slate-500">Total clicks</p>
            <p class="mt-3 text-3xl font-bold tracking-tight text-slate-950">{{ number_format($totalClicks) }}</p>
            <p class="mt-2 text-xs text-slate-500">Across your short links</p>
        </div>
    </div>

    <section id="create-url" class="mt-6 rounded-xl border border-slate-200 bg-white p-5 sm:p-6">
        <h3 class="font-semibold text-slate-900">Create a short URL</h3>
        <p class="mt-1 text-sm text-slate-500">Paste the full URL you want to shorten.</p>
        <form method="POST" action="{{ route('short-urls.store') }}" class="mt-4 flex flex-col gap-3 sm:flex-row">
            @csrf
            <div class="min-w-0 flex-1">
                <label for="long_url" class="sr-only">Original URL</label>
                <input id="long_url" name="long_url" type="url" required value="{{ old('long_url') }}" placeholder="https://example.com/page" class="block w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('long_url') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">Shorten URL</button>
        </form>
    </section>

    <section id="my-links" class="mt-7 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm shadow-slate-100/70">
        <div class="flex flex-col justify-between gap-2 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:px-6">
            <div>
                <h3 class="font-semibold text-slate-950">Your short links</h3>
                <p class="mt-0.5 text-sm text-slate-500">Only links created by you are shown here.</p>
            </div>
            <span class="text-sm text-slate-500">{{ $shortUrls->total() }} total</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[700px] text-left text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr><th class="px-6 py-3">Short link</th><th class="px-6 py-3">Destination</th><th class="px-6 py-3">Clicks</th><th class="px-6 py-3">Created</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($shortUrls as $shortUrl)
                        <tr class="hover:bg-slate-50/70">
                            <td class="whitespace-nowrap px-6 py-4">
                                <a href="{{ route('short-urls.redirect', $shortUrl->code) }}" class="font-semibold text-indigo-700 hover:text-indigo-900">{{ route('short-urls.redirect', $shortUrl->code) }}</a>
                                <button type="button" data-copy="{{ route('short-urls.redirect', $shortUrl->code) }}" onclick="navigator.clipboard.writeText(this.dataset.copy).then(() => { this.textContent = 'Copied'; setTimeout(() => this.textContent = 'Copy', 1200); })" class="ml-2 text-xs font-medium text-slate-500 hover:text-indigo-700">Copy</button>
                            </td>
                            <td class="max-w-[320px] truncate px-6 py-4 text-slate-500" title="{{ $shortUrl->long_url }}">{{ \Illuminate\Support\Str::limit($shortUrl->long_url, 58) }}</td>
                            <td class="px-6 py-4 font-medium text-slate-700">{{ number_format($shortUrl->hits) }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-slate-500">{{ $shortUrl->created_at->format('M j, Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-12 text-center">
                            <p class="font-medium text-slate-700">No short URLs yet</p>
                            <p class="mt-1 text-sm text-slate-500">Create your first short URL using the form above.</p>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($shortUrls->hasPages())
            <div class="border-t border-slate-100 px-6 py-4">{{ $shortUrls->links() }}</div>
        @endif
    </section>
@endsection
