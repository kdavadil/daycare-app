<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user()->load(['schoolMemberships.school']);

        $memberships = $user->schoolMemberships
            ->sortBy([['school.name', 'asc'], ['role', 'asc']])
            ->values();

        return view('dashboard', [
            'user' => $user,
            'memberships' => $memberships,
            'isAdmin' => $memberships->contains('role', 'administrator'),
            'isTeacher' => $memberships->contains('role', 'teacher'),
            'isGuardian' => $memberships->contains('role', 'guardian'),
        ]);
    }
}
