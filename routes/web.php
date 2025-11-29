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
Route::get('invitations/{token}/download-image', [InvitationController::class, 'downloadImage'])->name('invitations.download-image');
Route::post('invitations/{token}/send-link-whatsapp', [InvitationController::class, 'sendLinkViaWhatsApp'])->name('invitations.send-link-whatsapp');
Route::post('invitations/{token}/send-image-whatsapp', [InvitationController::class, 'sendImageViaWhatsApp'])->name('invitations.send-image-whatsapp');
Route::post('invitations/{token}/send-pdf-whatsapp', [InvitationController::class, 'sendPdfViaWhatsApp'])->name('invitations.send-pdf-whatsapp');
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
        // Calculer le nombre total de personnes (couple = 2, solo = 1)
        $totalGuests = Guest::selectRaw('SUM(CASE WHEN type = "couple" THEN 2 ELSE 1 END) as total')
            ->whereNull('deleted_at')
            ->value('total') ?? 0;
        
        // Calculer le nombre de personnes confirmées (couple = 2, solo = 1)
        $confirmedGuests = Guest::selectRaw('SUM(CASE WHEN type = "couple" THEN 2 ELSE 1 END) as total')
            ->where('rsvp_status', 'confirmed')
            ->whereNull('deleted_at')
            ->value('total') ?? 0;
        
        // Calculer le nombre de personnes en attente (couple = 2, solo = 1)
        $pendingGuests = Guest::selectRaw('SUM(CASE WHEN type = "couple" THEN 2 ELSE 1 END) as total')
            ->where(function ($query) {
                $query->whereNull('rsvp_status')
                    ->orWhere('rsvp_status', 'pending');
            })
            ->whereNull('deleted_at')
            ->value('total') ?? 0;
        
        $tableCount = ReceptionTable::count();
        $invitationsSent = Guest::whereNotNull('whatsapp_sent_at')->whereNull('deleted_at')->count();

        // Données pour le graphique hebdomadaire (7 derniers jours)
        // Compter le nombre de personnes confirmées (couple = 2, solo = 1)
        $weeklyData = [];
        $weeklyLabels = [];
        $startOfWeek = now()->subDays(6)->startOfDay();

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayStart = $date->copy()->startOfDay();
            $dayEnd = $date->copy()->endOfDay();

            $count = Guest::selectRaw('SUM(CASE WHEN type = "couple" THEN 2 ELSE 1 END) as total')
                ->where('rsvp_status', 'confirmed')
                ->whereBetween('rsvp_confirmed_at', [$dayStart, $dayEnd])
                ->whereNull('deleted_at')
                ->value('total') ?? 0;

            $weeklyData[] = (int) $count;
            $weeklyLabels[] = $date->format('D');
        }

        // Données pour le graphique mensuel (12 derniers mois)
        // Compter le nombre de personnes confirmées (couple = 2, solo = 1)
        $monthlyData = [];
        $monthlyLabels = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthStart = $date->copy()->startOfMonth();
            $monthEnd = $date->copy()->endOfMonth();

            $count = Guest::selectRaw('SUM(CASE WHEN type = "couple" THEN 2 ELSE 1 END) as total')
                ->where('rsvp_status', 'confirmed')
                ->whereBetween('rsvp_confirmed_at', [$monthStart, $monthEnd])
                ->whereNull('deleted_at')
                ->value('total') ?? 0;

            $monthlyData[] = (int) $count;
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
                'invitations_sent' => $invitationsSent,
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
    Route::post('guests/{guest}/send-invitation-image', [GuestController::class, 'sendInvitationImage'])->name('guests.send_invitation_image');
    Route::post('guests/{guest}/send-invitation-pdf', [GuestController::class, 'sendInvitationPdf'])->name('guests.send_invitation_pdf');
    Route::post('guests/send-bulk-invitations', [GuestController::class, 'sendBulkInvitations'])->name('guests.send_bulk_invitations');
    Route::post('guests/confirm-bulk', [GuestController::class, 'confirmBulk'])->name('guests.confirm_bulk');
    Route::get('guests/presence', [GuestController::class, 'presence'])->name('guests.presence');
    Route::post('guests/{guest}/mark-arrived', [GuestController::class, 'markArrived'])->name('guests.mark_arrived');
    Route::get('guests/export', [GuestController::class, 'export'])->name('guests.export');
    Route::get('guests/import', [GuestController::class, 'showImport'])->name('guests.import.show');
    Route::get('guests/import/template', [GuestController::class, 'downloadTemplate'])->name('guests.import.template');
    Route::post('guests/import', [GuestController::class, 'import'])->name('guests.import');
    Route::resource('guests', GuestController::class)->except(['show']);
    Route::get('guests/trash', [GuestController::class, 'trash'])->name('guests.trash');
    Route::post('guests/{id}/restore', [GuestController::class, 'restore'])->name('guests.restore');
    Route::delete('guests/{id}/force-delete', [GuestController::class, 'forceDelete'])->name('guests.force-delete');

    Route::get('tables/search', [TableController::class, 'search'])->name('tables.search');
    Route::get('tables/export', [TableController::class, 'export'])->name('tables.export');
    Route::get('tables/import', [TableController::class, 'showImport'])->name('tables.import.show');
    Route::get('tables/import/template', [TableController::class, 'downloadTemplate'])->name('tables.import.template');
    Route::post('tables/import', [TableController::class, 'import'])->name('tables.import');
    Route::resource('tables', TableController::class)->except(['show']);
    Route::get('tables/trash', [TableController::class, 'trash'])->name('tables.trash');
    Route::post('tables/{id}/restore', [TableController::class, 'restore'])->name('tables.restore');
    Route::delete('tables/{id}/force-delete', [TableController::class, 'forceDelete'])->name('tables.force-delete');

    Route::get('preferences/search', [PreferenceController::class, 'search'])->name('preferences.search');
    Route::get('preferences/export', [PreferenceController::class, 'export'])->name('preferences.export');
    Route::get('preferences', [PreferenceController::class, 'index'])->name('preferences.index');

    Route::get('beverages/search', [BeverageController::class, 'search'])->name('beverages.search');
    Route::resource('beverages', BeverageController::class)->except(['show']);

    Route::get('users/search', [UserController::class, 'search'])->name('users.search');
    Route::resource('users', UserController::class)->except(['show']);
    Route::get('users/trash', [UserController::class, 'trash'])->name('users.trash');
    Route::post('users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
    Route::delete('users/{id}/force-delete', [UserController::class, 'forceDelete'])->name('users.force-delete');

    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile.show');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/change-password', [AuthController::class, 'changePassword'])->name('profile.change-password');

    Route::get('/notifications/count', [\App\Http\Controllers\NotificationController::class, 'count'])->name('notifications.count');
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');

    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});

