<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BeverageController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\PreferenceController;
use App\Http\Controllers\TableController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');

Route::middleware('auth.session')->group(function () {
    Route::get('/', function () {
        return view('index');
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

    Route::get('invitations/show', function () {
        return view('invitations.invitation');
    })->name('invitations.show');

    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});

