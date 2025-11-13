<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BeverageController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\PreferenceController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\UserController;
use App\Models\Guest;
use App\Models\ReceptionTable;
use Illuminate\Support\Facades\Route;

Route::get('invitations/{token}', [InvitationController::class, 'show'])->name('invitations.show');
Route::post('invitations/{token}/confirm', [InvitationController::class, 'confirm'])->name('invitations.confirm');
Route::get('invitations/{token}/download', [InvitationController::class, 'download'])->name('invitations.download');
Route::post('invitations/{token}/preferences', [InvitationController::class, 'updatePreferences'])->name('invitations.preferences');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');

Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.forgot');
Route::post('/forgot-password', [AuthController::class, 'sendResetCode'])->name('password.send-code');
Route::get('/verify-code', [AuthController::class, 'showCodeVerificationForm'])->name('password.code.verify');
Route::post('/verify-code', [AuthController::class, 'verifyResetCode'])->name('password.verify-code');
Route::post('/resend-code', [AuthController::class, 'resendResetCode'])->name('password.resend-code');
Route::get('/reset-password', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

Route::middleware('auth.session')->group(function () {
    Route::get('/', function () {
        $totalGuests = Guest::count();
        $confirmedGuests = Guest::where('rsvp_status', 'confirmed')->count();
        $pendingGuests = Guest::where(static function ($query) {
            $query->whereNull('rsvp_status')
                ->orWhere('rsvp_status', 'pending');
        })->count();
        $tableCount = ReceptionTable::count();

        // Données pour le graphique hebdomadaire (7 derniers jours)
        $weeklyData = [];
        $weeklyLabels = [];
        $startOfWeek = now()->subDays(6)->startOfDay();

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayStart = $date->copy()->startOfDay();
            $dayEnd = $date->copy()->endOfDay();

            $count = Guest::where('rsvp_status', 'confirmed')
                ->whereBetween('rsvp_confirmed_at', [$dayStart, $dayEnd])
                ->count();

            $weeklyData[] = $count;
            $weeklyLabels[] = $date->format('D');
        }

        // Données pour le graphique mensuel (12 derniers mois)
        $monthlyData = [];
        $monthlyLabels = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthStart = $date->copy()->startOfMonth();
            $monthEnd = $date->copy()->endOfMonth();

            $count = Guest::where('rsvp_status', 'confirmed')
                ->whereBetween('rsvp_confirmed_at', [$monthStart, $monthEnd])
                ->count();

            $monthlyData[] = $count;
            $monthlyLabels[] = $date->format('M');
        }

        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => url('/')],
        ];

        return view('index', [
            'stats' => [
                'guests_total' => $totalGuests,
                'guests_confirmed' => $confirmedGuests,
                'guests_pending' => $pendingGuests,
                'tables_total' => $tableCount,
            ],
            'chartData' => [
                'weekly' => [
                    'data' => $weeklyData,
                    'labels' => $weeklyLabels,
                ],
                'monthly' => [
                    'data' => $monthlyData,
                    'labels' => $monthlyLabels,
                ],
            ],
            'breadcrumbs' => $breadcrumbs,
        ])->with('pageTitle', 'Dashboard');
    });

    Route::get('guests/search', [GuestController::class, 'search'])->name('guests.search');
    Route::post('guests/{guest}/send-invitation', [GuestController::class, 'sendInvitation'])->name('guests.send_invitation');
    Route::post('guests/send-bulk-invitations', [GuestController::class, 'sendBulkInvitations'])->name('guests.send_bulk_invitations');
    Route::get('guests/export', [GuestController::class, 'export'])->name('guests.export');
    Route::get('guests/import', [GuestController::class, 'showImport'])->name('guests.import.show');
    Route::get('guests/import/template', [GuestController::class, 'downloadTemplate'])->name('guests.import.template');
    Route::post('guests/import', [GuestController::class, 'import'])->name('guests.import');
    Route::resource('guests', GuestController::class)->except(['show']);
    Route::post('guests/{id}/restore', [GuestController::class, 'restore'])->name('guests.restore');

    Route::get('tables/search', [TableController::class, 'search'])->name('tables.search');
    Route::get('tables/export', [TableController::class, 'export'])->name('tables.export');
    Route::get('tables/import', [TableController::class, 'showImport'])->name('tables.import.show');
    Route::get('tables/import/template', [TableController::class, 'downloadTemplate'])->name('tables.import.template');
    Route::post('tables/import', [TableController::class, 'import'])->name('tables.import');
    Route::resource('tables', TableController::class)->except(['show']);
    Route::post('tables/{id}/restore', [TableController::class, 'restore'])->name('tables.restore');

    Route::get('preferences/search', [PreferenceController::class, 'search'])->name('preferences.search');
    Route::get('preferences/export', [PreferenceController::class, 'export'])->name('preferences.export');
    Route::get('preferences', [PreferenceController::class, 'index'])->name('preferences.index');

    Route::get('beverages/search', [BeverageController::class, 'search'])->name('beverages.search');
    Route::resource('beverages', BeverageController::class)->except(['show']);

    Route::get('users/search', [UserController::class, 'search'])->name('users.search');
    Route::resource('users', UserController::class)->except(['show']);
    Route::post('users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');

    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile.show');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/change-password', [AuthController::class, 'changePassword'])->name('profile.change-password');

    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});

