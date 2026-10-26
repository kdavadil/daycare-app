<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\DemoPersonaController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\ChildHomeController;
use App\Http\Controllers\ChildMessageController;
use App\Http\Controllers\ClassCalendarController;
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
Route::get('/children/{child}', ChildHomeController::class)->middleware(['auth', 'feature:family_portal'])->name('children.show');
Route::get('/children/{child}/messages', [ChildMessageController::class, 'index'])->middleware(['auth', 'feature:messaging'])->name('children.messages.index');
Route::post('/children/{child}/messages', [ChildMessageController::class, 'store'])->middleware(['auth', 'feature:messaging'])->name('children.messages.store');
Route::get('/daily-updates/{journalEntry}/photo', [DailyUpdateController::class, 'photo'])->middleware(['auth', 'feature:daily_updates'])->name('daily-updates.photo');
Route::middleware(['auth', 'school.role:administrator,teacher'])->group(function (): void {
    Route::middleware('feature:roster')->get('/roster', RosterController::class)->name('roster.index');
    Route::middleware('feature:attendance')->get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::middleware('feature:class_calendar')->get('/class-calendar', [ClassCalendarController::class, 'index'])->name('class-calendar.index');
    Route::middleware('feature:attendance')->post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::middleware('feature:daily_updates')->get('/daily-updates', [DailyUpdateController::class, 'index'])->name('daily-updates.index');
    Route::middleware('feature:daily_updates')->post('/daily-updates', [DailyUpdateController::class, 'store'])->name('daily-updates.store');
});
