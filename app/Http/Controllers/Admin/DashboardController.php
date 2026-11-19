<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\ShortUrl;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $company = $user->company;

        return view('admin.dashboard', [
            'company' => $company,
            'shortUrls' => ShortUrl::with('user')
                ->where('company_id', $user->company_id)
                ->latest()
                ->paginate(8),
            'teamMembers' => $company
                ? $company->users()->where('role', '!=', 'SuperAdmin')->latest()->get()
                : collect(),
            'pendingInvitations' => $company
                ? Invitation::with('company')->where('company_id', $company->id)->whereNull('accepted_at')->latest()->get()
                : collect(),
            'urlCount' => ShortUrl::where('company_id', $user->company_id)->count(),
            'totalClicks' => ShortUrl::where('company_id', $user->company_id)->sum('hits'),
        ]);
    }
}
