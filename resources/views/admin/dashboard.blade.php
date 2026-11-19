@extends('layouts.dashboard')

@section('title', 'Company dashboard')
@section('page-heading', 'Company dashboard')

@section('content')
    <div class="mb-7 flex flex-col justify-between gap-3 sm:flex-row sm:items-end">
        <div>
            <p class="text-sm font-medium text-indigo-600">{{ $company?->name ?? 'Company not assigned' }}</p>
            <h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-950">Welcome back, {{ auth()->user()->name }}</h2>
            <p class="mt-1 text-sm text-slate-500">View all company URLs and manage your team.</p>
        </div>
        <span class="w-fit rounded-full border border-emerald-100 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700">Admin</span>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ([
            ['Company URLs', number_format($urlCount), 'Links created by your company'],
            ['Total clicks', number_format($totalClicks), 'Traffic across company URLs'],
            ['Team members', number_format($teamMembers->count()), 'Users in your company'],
        ] as [$label, $value, $hint])
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm shadow-slate-100/70">
                <p class="text-sm font-medium text-slate-500">{{ $label }}</p>
                <p class="mt-3 text-3xl font-bold tracking-tight text-slate-950">{{ $value }}</p>
                <p class="mt-2 text-xs text-slate-500">{{ $hint }}</p>
            </div>
        @endforeach
    </div>

    <section id="invite-team" class="mt-7 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm shadow-slate-100/70 sm:p-6">
        <div class="mb-5">
            <h3 class="text-lg font-semibold text-slate-950">Invite a team member</h3>
            <p class="mt-1 text-sm text-slate-500">Invited users are always added to {{ $company?->name ?? 'your company' }}.</p>
        </div>
        <form method="POST" action="{{ route('admin.invitations.store') }}" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @csrf
            <div>
                <label for="invite_name" class="mb-1.5 block text-sm font-medium text-slate-700">Name</label>
                <input id="invite_name" name="name" value="{{ old('name') }}" required maxlength="255" class="block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('name') <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="invite_email" class="mb-1.5 block text-sm font-medium text-slate-700">Email</label>
                <input id="invite_email" name="email" type="email" value="{{ old('email') }}" required class="block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('email') <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="invite_role" class="mb-1.5 block text-sm font-medium text-slate-700">Role</label>
                <select id="invite_role" name="role" required class="block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Select a role</option>
                    @foreach (['Admin', 'Member'] as $role)
                        <option value="{{ $role }}" @selected(old('role') === $role)>{{ $role }}</option>
                    @endforeach
                </select>
                @error('role') <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">Create invitation</button>
            </div>
        </form>
    </section>

    <section id="company-links" class="mt-7 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm shadow-slate-100/70">
        <div class="flex flex-col justify-between gap-2 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:px-6">
            <div>
                <h3 class="font-semibold text-slate-950">Company short URLs</h3>
                <p class="mt-0.5 text-sm text-slate-500">All links created in your company</p>
            </div>
            <span class="text-sm text-slate-500">{{ $shortUrls->total() }} total</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px] text-left text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr><th class="px-6 py-3">Short link</th><th class="px-6 py-3">Destination</th><th class="px-6 py-3">Created by</th><th class="px-6 py-3">Clicks</th><th class="px-6 py-3">Created</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($shortUrls as $shortUrl)
                        <tr class="hover:bg-slate-50/70">
                            <td class="whitespace-nowrap px-6 py-4">
                                <a href="{{ route('short-urls.redirect', $shortUrl->code) }}" class="font-semibold text-indigo-700 hover:text-indigo-900">{{ route('short-urls.redirect', $shortUrl->code) }}</a>
                                <button type="button" data-copy="{{ route('short-urls.redirect', $shortUrl->code) }}" onclick="navigator.clipboard.writeText(this.dataset.copy).then(() => { this.textContent = 'Copied'; setTimeout(() => this.textContent = 'Copy', 1200); })" class="ml-2 text-xs font-medium text-slate-500 hover:text-indigo-700">Copy</button>
                            </td>
                            <td class="max-w-[280px] truncate px-6 py-4 text-slate-500" title="{{ $shortUrl->long_url }}">{{ \Illuminate\Support\Str::limit($shortUrl->long_url, 52) }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $shortUrl->user?->name ?? '—' }}</td>
                            <td class="px-6 py-4 font-medium text-slate-700">{{ number_format($shortUrl->hits) }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-slate-500">{{ $shortUrl->created_at->format('M j, Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-12 text-center">
                            <p class="font-medium text-slate-700">No links yet</p>
                            <p class="mt-1 text-sm text-slate-500">Company members can create short URLs from their dashboards.</p>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($shortUrls->hasPages())
            <div class="border-t border-slate-100 px-6 py-4">{{ $shortUrls->links() }}</div>
        @endif
    </section>

    <section id="team" class="mt-7 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm shadow-slate-100/70">
        <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
            <h3 class="font-semibold text-slate-950">Company users</h3>
            <p class="mt-0.5 text-sm text-slate-500">Users assigned to {{ $company?->name ?? 'this company' }}</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[520px] text-left text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr><th class="px-6 py-3">Name</th><th class="px-6 py-3">Email</th><th class="px-6 py-3">Role</th><th class="px-6 py-3">Joined</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($teamMembers as $member)
                        <tr><td class="px-6 py-4 font-medium text-slate-800">{{ $member->name }}</td><td class="px-6 py-4 text-slate-600">{{ $member->email }}</td><td class="px-6 py-4 text-slate-600">{{ $member->role }}</td><td class="px-6 py-4 text-slate-500">{{ $member->created_at->format('M j, Y') }}</td></tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-9 text-center text-sm text-slate-500">No team members have joined this company yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="mt-7 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm shadow-slate-100/70">
        <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
            <h3 class="font-semibold text-slate-950">Pending invitations</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[560px] text-left text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr><th class="px-6 py-3">Name</th><th class="px-6 py-3">Email</th><th class="px-6 py-3">Role</th><th class="px-6 py-3">Invite link</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($pendingInvitations as $invitation)
                        <tr>
                            <td class="px-6 py-4 font-medium text-slate-800">{{ $invitation->name }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $invitation->email }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $invitation->role }}</td>
                            <td class="px-6 py-4"><a class="font-medium text-indigo-700 hover:text-indigo-900" href="{{ route('invitation.show', $invitation->token) }}">Open invite</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-9 text-center text-slate-500">No pending invitations.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
