@extends('layouts.dashboard')

@section('title', auth()->user()->role . ' dashboard')
@section('page-heading', 'Link dashboard')

@section('content')
    <div class="mb-7 flex flex-col justify-between gap-3 sm:flex-row sm:items-end">
        <div>
            <p class="text-sm font-medium text-indigo-600">{{ $company?->name ?? 'Company setup needed' }}</p>
            <h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-950">Welcome back, {{ auth()->user()->name }}</h2>
            <p class="mt-1 text-sm text-slate-500">Create short links and review the links you have made.</p>
        </div>
        <span class="w-fit rounded-full border border-indigo-100 bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700">{{ auth()->user()->role }}</span>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">My short links</p>
            <p class="mt-3 text-3xl font-bold tracking-tight text-slate-950">{{ number_format($urlCount) }}</p>
            <p class="mt-2 text-xs text-slate-500">Links you created</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Total clicks</p>
            <p class="mt-3 text-3xl font-bold tracking-tight text-slate-950">{{ number_format($totalClicks) }}</p>
            <p class="mt-2 text-xs text-slate-500">Across your links</p>
        </div>
    </div>

    <section id="create-url" class="mt-7 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="mb-5">
            <h3 class="text-lg font-semibold text-slate-950">Create a short link</h3>
            <p class="mt-1 text-sm text-slate-500">Paste a destination URL starting with https://.</p>
        </div>
        <form method="POST" action="{{ route('short-urls.store') }}" class="flex flex-col gap-3 sm:flex-row">
            @csrf
            <div class="min-w-0 flex-1">
                <label for="long_url" class="sr-only">Destination URL</label>
                <input id="long_url" name="long_url" type="url" required value="{{ old('long_url') }}" placeholder="https://example.com/your-long-link" class="block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('long_url') <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p> @enderror
                @error('company_id') <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">Generate link</button>
        </form>
    </section>

    <section id="my-links" class="mt-7 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col justify-between gap-2 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:px-6">
            <div>
                <h3 class="font-semibold text-slate-950">My short links</h3>
                <p class="mt-0.5 text-sm text-slate-500">Only links created by you are shown.</p>
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
                                <span class="font-semibold text-indigo-700">{{ route('short-urls.redirect', $shortUrl->code) }}</span>
                                <button type="button" data-copy="{{ route('short-urls.redirect', $shortUrl->code) }}" onclick="navigator.clipboard.writeText(this.dataset.copy).then(() => { this.textContent = 'Copied'; setTimeout(() => this.textContent = 'Copy', 1200); })" class="ml-2 text-xs font-medium text-slate-500 hover:text-indigo-700">Copy</button>
                            </td>
                            <td class="max-w-[320px] truncate px-6 py-4 text-slate-500" title="{{ $shortUrl->long_url }}">{{ \Illuminate\Support\Str::limit($shortUrl->long_url, 58) }}</td>
                            <td class="px-6 py-4 font-medium text-slate-700">{{ number_format($shortUrl->hits) }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-slate-500">{{ $shortUrl->created_at->format('M j, Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-12 text-center">
                            <p class="font-medium text-slate-700">No links yet</p>
                            <p class="mt-1 text-sm text-slate-500">Create your first short link above.</p>
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
