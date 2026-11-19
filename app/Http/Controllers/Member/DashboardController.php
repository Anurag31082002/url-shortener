<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        return view('member.dashboard', [
            'company' => $user->company,
            'shortUrls' => $user->shortUrls()->latest()->paginate(8),
            'urlCount' => $user->shortUrls()->count(),
            'totalClicks' => $user->shortUrls()->sum('hits'),
        ]);
    }
}
