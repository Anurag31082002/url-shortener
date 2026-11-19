@extends('layouts.dashboard')

@section('title', 'Super Admin dashboard')
@section('page-heading', 'Super Admin dashboard')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold tracking-tight text-slate-950">Welcome, {{ auth()->user()->name }}</h2>
        <p class="mt-1 text-sm text-slate-500">Invite a new client and manage platform-wide companies and links.</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ([
            ['Clients', number_format($companyCount)],
            ['Users', number_format($userCount)],
            ['Short URLs', number_format($urlCount)],
            ['Clicks', number_format($totalClicks)],
        ] as [$label, $value])
            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <p class="text-sm text-slate-500">{{ $label }}</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $value }}</p>
            </div>
        @endforeach
    </div>

    <section id="invite-client" class="mt-6 rounded-xl border border-slate-200 bg-white p-5 sm:p-6">
        <h3 class="text-lg font-semibold text-slate-900">Invite a new client</h3>
        <p class="mt-1 text-sm text-slate-500">This creates a company and an Admin invitation for that company.</p>
        <form method="POST" action="{{ route('superadmin.invitations.store') }}" class="mt-5 grid gap-4 sm:grid-cols-2">
            @csrf
            <div>
                <label for="company_name" class="mb-1.5 block text-sm font-medium text-slate-700">Client / company name</label>
                <input id="company_name" name="company_name" value="{{ old('company_name') }}" required maxlength="255" class="block w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Sembark Tech">
                @error('company_name') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="email" class="mb-1.5 block text-sm font-medium text-slate-700">New Admin email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required class="block w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="ravi@sembark.com">
                @error('email') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div class="sm:col-span-2">
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">Create client and Admin invite</button>
            </div>
        </form>
    </section>

    <section id="clients" class="mt-6 overflow-hidden rounded-xl border border-slate-200 bg-white">
        <div class="border-b border-slate-100 px-5 py-4">
            <h3 class="font-semibold text-slate-900">Clients</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[560px] text-left text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500">
                    <tr><th class="px-5 py-3">Company</th><th class="px-5 py-3">Contact</th><th class="px-5 py-3">Users</th><th class="px-5 py-3">Created</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($companies as $company)
                        <tr>
                            <td class="px-5 py-3.5 font-medium text-slate-800">{{ $company->name }}</td>
                            <td class="px-5 py-3.5 text-slate-600">{{ $company->email }}</td>
                            <td class="px-5 py-3.5 text-slate-600">{{ $company->users_count }}</td>
                            <td class="px-5 py-3.5 text-slate-500">{{ $company->created_at->format('M j, Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-8 text-center text-slate-500">No clients yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($companies->hasPages())
            <div class="border-t border-slate-100 px-5 py-3">{{ $companies->links() }}</div>
        @endif
    </section>

    <section id="all-links" class="mt-6 overflow-hidden rounded-xl border border-slate-200 bg-white">
        <div class="border-b border-slate-100 px-5 py-4">
            <h3 class="font-semibold text-slate-900">All short URLs</h3>
            <p class="mt-1 text-sm text-slate-500">Read-only platform-wide list.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px] text-left text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500">
                    <tr><th class="px-5 py-3">Short URL</th><th class="px-5 py-3">Original URL</th><th class="px-5 py-3">Company</th><th class="px-5 py-3">Created by</th><th class="px-5 py-3">Hits</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($shortUrls as $shortUrl)
                        <tr>
                            <td class="whitespace-nowrap px-5 py-3.5 font-medium text-indigo-700">{{ route('short-urls.redirect', $shortUrl->code) }}</td>
                            <td class="max-w-[260px] truncate px-5 py-3.5 text-slate-600" title="{{ $shortUrl->long_url }}">{{ \Illuminate\Support\Str::limit($shortUrl->long_url, 48) }}</td>
                            <td class="px-5 py-3.5 text-slate-600">{{ $shortUrl->company?->name ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-slate-600">{{ $shortUrl->user?->name ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-slate-600">{{ number_format($shortUrl->hits) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-8 text-center text-slate-500">No short URLs yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($shortUrls->hasPages())
            <div class="border-t border-slate-100 px-5 py-3">{{ $shortUrls->links() }}</div>
        @endif
    </section>

    <section class="mt-6 overflow-hidden rounded-xl border border-slate-200 bg-white">
        <div class="border-b border-slate-100 px-5 py-4">
            <h3 class="font-semibold text-slate-900">Pending invitations</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[600px] text-left text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500">
                    <tr><th class="px-5 py-3">Invitee</th><th class="px-5 py-3">Email</th><th class="px-5 py-3">Role</th><th class="px-5 py-3">Company</th><th class="px-5 py-3">Invitation</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($pendingInvitations as $invitation)
                        <tr>
                            <td class="px-5 py-3.5 text-slate-800">{{ $invitation->name }}</td>
                            <td class="px-5 py-3.5 text-slate-600">{{ $invitation->email }}</td>
                            <td class="px-5 py-3.5 text-slate-600">{{ $invitation->role }}</td>
                            <td class="px-5 py-3.5 text-slate-600">{{ $invitation->company?->name ?? '—' }}</td>
                            <td class="px-5 py-3.5"><a class="font-medium text-indigo-700 hover:text-indigo-900" href="{{ route('invitation.show', $invitation->token) }}">Open link</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-8 text-center text-slate-500">No pending invitations.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
