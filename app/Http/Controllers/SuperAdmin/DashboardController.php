<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Invitation;
use App\Models\ShortUrl;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        return view('superadmin.dashboard', [
            'companies' => Company::withCount('users')->latest()->paginate(6, ['*'], 'companies'),
            'pendingInvitations' => Invitation::with('company')->whereNull('accepted_at')->latest()->get(),
            'companyCount' => Company::count(),
            'userCount' => User::where('role', '!=', 'SuperAdmin')->count(),
            'urlCount' => ShortUrl::count(),
            'totalClicks' => ShortUrl::sum('hits'),
            'shortUrls' => ShortUrl::with(['user', 'company'])->latest()->paginate(8, ['*'], 'urls'),
        ]);
    }
}
