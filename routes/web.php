<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BeverageController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\PreferenceController;
use App\Http\Controllers\TableController;
use App\Models\Guest;
use App\Models\ReceptionTable;
use Illuminate\Support\Facades\Route;

Route::get('invitations/{token}', [InvitationController::class, 'show'])->name('invitations.show');
Route::post('invitations/{token}/confirm', [InvitationController::class, 'confirm'])->name('invitations.confirm');
Route::get('invitations/{token}/download', [InvitationController::class, 'download'])->name('invitations.download');
Route::post('invitations/{token}/preferences', [InvitationController::class, 'updatePreferences'])->name('invitations.preferences');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');

Route::middleware('auth.session')->group(function () {
    Route::get('/', function () {
        $totalGuests = Guest::count();
        $confirmedGuests = Guest::where('rsvp_status', 'confirmed')->count();
        $pendingGuests = Guest::where(static function ($query) {
            $query->whereNull('rsvp_status')
                ->orWhere('rsvp_status', 'pending');
        })->count();
        $tableCount = ReceptionTable::count();

        return view('index', [
            'stats' => [
                'guests_total' => $totalGuests,
                'guests_confirmed' => $confirmedGuests,
                'guests_pending' => $pendingGuests,
                'tables_total' => $tableCount,
            ],
        ]);
    });

    Route::get('guests/search', [GuestController::class, 'search'])->name('guests.search');
    Route::resource('guests', GuestController::class)->except(['show']);
    Route::post('guests/{id}/restore', [GuestController::class, 'restore'])->name('guests.restore');

    Route::get('tables/search', [TableController::class, 'search'])->name('tables.search');
    Route::resource('tables', TableController::class)->except(['show']);
    Route::post('tables/{id}/restore', [TableController::class, 'restore'])->name('tables.restore');

    Route::get('preferences/search', [PreferenceController::class, 'search'])->name('preferences.search');
    Route::get('preferences', [PreferenceController::class, 'index'])->name('preferences.index');

    Route::get('beverages/search', [BeverageController::class, 'search'])->name('beverages.search');
    Route::resource('beverages', BeverageController::class)->except(['show']);

    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});

