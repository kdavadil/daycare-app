<?php

namespace App\Http\Controllers;

use App\Models\Child;
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

        $guardianChildren = collect();

        foreach ($memberships->where('role', 'guardian') as $membership) {
            $guardianChildren = $guardianChildren->merge(Child::query()
                ->where('school_id', $membership->school_id)
                ->whereHas('guardians', function ($query) use ($membership, $user): void {
                    $query->where('guardians.email', $user->email);

                    if ($membership->source_type && $membership->source_id) {
                        $query->orWhere('guardians.id', $membership->source_id);
                    }

                    if (! $membership->source_type && $membership->source_id) {
                        $query->orWhere('guardians.id', $membership->source_id);
                    }
                })
                ->with(['school', 'schoolClass', 'latestAttendanceRecord'])
                ->orderBy('preferred_name')
                ->get());
        }

        return view('dashboard', [
            'user' => $user,
            'memberships' => $memberships,
            'isAdmin' => $memberships->contains('role', 'administrator'),
            'isTeacher' => $memberships->contains('role', 'teacher'),
            'isGuardian' => $memberships->contains('role', 'guardian'),
            'guardianChildren' => $guardianChildren->unique('id')->values(),
        ]);
    }
}
