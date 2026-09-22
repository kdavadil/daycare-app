<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user()->load(['schoolMemberships.school']);

        return view('dashboard', [
            'user' => $user,
            'memberships' => $user->schoolMemberships
                ->sortBy([['school.name', 'asc'], ['role', 'asc']])
                ->values(),
        ]);
    }
}
