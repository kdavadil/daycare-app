<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\DemoPersonaController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\ChildHomeController;
use App\Http\Controllers\DailyUpdateController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RosterController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::view('/about', 'about')->name('about');
Route::view('/login', 'auth.login')->middleware('guest')->name('login');
Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->middleware('guest')->name('auth.google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->middleware('guest')->name('auth.google.callback');
Route::post('/auth/demo', DemoPersonaController::class)->middleware('guest')->name('auth.demo');
Route::post('/logout', [GoogleAuthController::class, 'logout'])->middleware('auth')->name('logout');
Route::get('/dashboard', DashboardController::class)->middleware('auth')->name('dashboard');
Route::get('/children/{child}', ChildHomeController::class)->middleware('auth')->name('children.show');
Route::get('/daily-updates/{journalEntry}/photo', [DailyUpdateController::class, 'photo'])->middleware('auth')->name('daily-updates.photo');
Route::middleware(['auth', 'school.role:administrator,teacher'])->group(function (): void {
    Route::get('/roster', RosterController::class)->name('roster.index');
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('/daily-updates', [DailyUpdateController::class, 'index'])->name('daily-updates.index');
    Route::post('/daily-updates', [DailyUpdateController::class, 'store'])->name('daily-updates.store');
});
