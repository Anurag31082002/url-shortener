<?php

namespace App\Http\Controllers;

class WorkspaceDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        return view('workspace.dashboard', [
            'company' => $user->company,
            'shortUrls' => $user->shortUrls()->latest()->paginate(8),
            'urlCount' => $user->shortUrls()->count(),
            'totalClicks' => $user->shortUrls()->sum('hits'),
        ]);
    }
}
