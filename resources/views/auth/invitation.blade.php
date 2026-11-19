<x-guest-layout>
    <div class="mb-6">
        <p class="text-sm font-semibold text-indigo-600">URL Shortener invitation</p>
        <h1 class="mt-1 text-xl font-bold text-slate-900">Join {{ $invitation->company->name }}</h1>
        <p class="mt-2 text-sm text-slate-600">You were invited as a {{ $invitation->role }}. Confirm your name and set a password.</p>
    </div>

    <div class="mb-5 rounded-lg bg-slate-50 p-3 text-sm">
        <p class="mt-1 text-slate-500">{{ $invitation->email }}</p>
    </div>
    @error('email')
        <p class="mb-4 text-sm text-rose-600">{{ $message }}</p>
    @enderror

    <form method="POST" action="{{ route('invitation.accept', $invitation->token) }}" class="space-y-4">
        @csrf
        <div>
            <label for="name" class="mb-1 block text-sm font-medium text-slate-700">Your name</label>
            <input id="name" name="name" type="text" required maxlength="255" value="{{ old('name', $invitation->name) }}" autocomplete="name" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            @error('name') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="password" class="mb-1 block text-sm font-medium text-slate-700">Password</label>
            <input id="password" name="password" type="password" required autocomplete="new-password" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            @error('password') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="password_confirmation" class="mb-1 block text-sm font-medium text-slate-700">Confirm password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <button type="submit" class="w-full rounded-md bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700">Accept invitation</button>
    </form>
</x-guest-layout>
