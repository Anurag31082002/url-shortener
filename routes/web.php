<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\Member\DashboardController as MemberDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShortUrlController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\WorkspaceDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::get('/dashboard', function () {
    return match (auth()->user()->role) {
        'SuperAdmin' => redirect()->route('superadmin.dashboard'),
        'Admin' => redirect()->route('admin.dashboard'),
        'Member' => redirect()->route('member.dashboard'),
        'Sales' => redirect()->route('sales.dashboard'),
        'Manager' => redirect()->route('manager.dashboard'),
        default => abort(403),
    };
})->middleware('auth')->name('dashboard');

Route::middleware(['auth', 'role:SuperAdmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');
    Route::post('/invitations', [InvitationController::class, 'store'])->name('invitations.store');
});

Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::post('/invitations', [InvitationController::class, 'store'])->name('invitations.store');
});

Route::middleware(['auth', 'role:Member'])->prefix('member')->name('member.')->group(function () {
    Route::get('/dashboard', [MemberDashboardController::class, 'index'])->name('dashboard');
});

Route::middleware(['auth', 'role:Sales'])->prefix('sales')->name('sales.')->group(function () {
    Route::get('/dashboard', [WorkspaceDashboardController::class, 'index'])->name('dashboard');
});

Route::middleware(['auth', 'role:Manager'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/dashboard', [WorkspaceDashboardController::class, 'index'])->name('dashboard');
});

Route::middleware(['auth', 'role:Member,Sales,Manager'])->group(function () {
    Route::post('/short-urls', [ShortUrlController::class, 'store'])->name('short-urls.store');
});

Route::get('/s/{code}', [ShortUrlController::class, 'redirect'])->name('short-urls.redirect');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('guest')->group(function () {
    Route::get('/invitation/{token}', [InvitationController::class, 'show'])->name('invitation.show');
    Route::post('/invitation/{token}', [InvitationController::class, 'accept'])->name('invitation.accept');
});

require __DIR__.'/auth.php';
