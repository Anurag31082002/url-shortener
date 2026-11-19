<?php

namespace App\Http\Controllers;

use App\Models\ShortUrl;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ShortUrlController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();
        abort_unless($user->canCreateShortUrls(), 403);

        $validated = $request->validate([
            'long_url' => ['required', 'url:http,https', 'max:2048'],
        ]);

        if (! $user->company_id) {
            throw ValidationException::withMessages([
                'company_id' => 'Your account must be assigned to a company before creating a short link.',
            ]);
        }

        do {
            $code = Str::random(8);
        } while (ShortUrl::where('code', $code)->exists());

        ShortUrl::create([
            'user_id' => $user->id,
            'company_id' => $user->company_id,
            'code' => $code,
            'long_url' => $validated['long_url'],
        ]);

        return redirect()
            ->route($this->dashboardRoute($user->role))
            ->with('status', 'Your short link is ready.');
    }

    public function redirect(string $code)
    {
        $shortUrl = ShortUrl::where('code', $code)->firstOrFail();
        $shortUrl->increment('hits');

        return redirect()->away($shortUrl->long_url);
    }

    private function dashboardRoute(string $role): string
    {
        return match ($role) {
            'Member' => 'member.dashboard',
            'Sales' => 'sales.dashboard',
            'Manager' => 'manager.dashboard',
            default => abort(403),
        };
    }
}
