<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class InvitationController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        $request->merge([
            'email' => Str::lower(trim((string) $request->input('email'))),
        ]);

        if ($user->isSuperAdmin()) {
            $validated = $request->validate([
                'company_name' => ['required', 'string', 'max:255'],
                'email' => [
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('users', 'email'),
                    Rule::unique('companies', 'email'),
                ],
            ]);

            $this->ensureNoPendingInvitation($validated['email']);

            [$company, $invitation] = DB::transaction(function () use ($validated, $user) {
                $company = Company::create([
                    'name' => $validated['company_name'],
                    'email' => $validated['email'],
                ]);
                $invitation = $this->createInvitation(
                    Str::headline(Str::before($validated['email'], '@')),
                    $validated['email'],
                    'Admin',
                    $company->id,
                    $user->id
                );

                return [$company, $invitation];
            });

            return redirect()
                ->route('superadmin.dashboard')
                ->with('status', $company->name.' was created. Share the Admin invitation link below.')
                ->with('invite_url', route('invitation.show', $invitation->token));
        }

        abort_unless($user->isAdmin(), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'role' => ['required', Rule::in(['Admin', 'Member'])],
        ]);

        if (! $user->company_id) {
            throw ValidationException::withMessages([
                'role' => 'Your Admin account must be assigned to a company before inviting users.',
            ]);
        }

        $this->ensureNoPendingInvitation($validated['email']);
        $invitation = $this->createInvitation(
            $validated['name'],
            $validated['email'],
            $validated['role'],
            $user->company_id,
            $user->id
        );

        return redirect()
            ->route('admin.dashboard')
            ->with('status', 'Invitation created. Share the invitation link below.')
            ->with('invite_url', route('invitation.show', $invitation->token));
    }

    public function show(string $token): View
    {
        $invitation = Invitation::with('company')
            ->where('token', $token)
            ->whereNull('accepted_at')
            ->firstOrFail();

        return view('auth.invitation', compact('invitation'));
    }

    public function accept(Request $request, string $token): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = DB::transaction(function () use ($token, $validated) {
            $invitation = Invitation::where('token', $token)
                ->whereNull('accepted_at')
                ->firstOrFail();

            if (User::where('email', $invitation->email)->exists()) {
                throw ValidationException::withMessages([
                    'email' => 'This invitation has already been used.',
                ]);
            }

            $account = User::create([
                'name' => $validated['name'],
                'email' => $invitation->email,
                'password' => $validated['password'],
                'role' => $invitation->role,
                'company_id' => $invitation->company_id,
            ]);

            $invitation->update(['accepted_at' => now()]);

            return $account;
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    private function ensureNoPendingInvitation(string $email): void
    {
        if (Invitation::where('email', $email)->whereNull('accepted_at')->exists()) {
            throw ValidationException::withMessages([
                'email' => 'An invitation for this email is already pending.',
            ]);
        }
    }

    private function createInvitation(string $name, string $email, string $role, int $companyId, int $invitedBy): Invitation
    {
        return Invitation::create([
            'name' => $name,
            'email' => Str::lower($email),
            'role' => $role,
            'company_id' => $companyId,
            'token' => Str::random(48),
            'invited_by' => $invitedBy,
        ]);
    }
}
