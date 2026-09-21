<?php

use App\Http\Controllers\RosterController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::view('/about', 'about')->name('about');
Route::get('/roster', RosterController::class)->name('roster.index');
