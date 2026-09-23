<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class RosterController extends Controller
{
    public function __invoke(Request $request): View
    {
        $school = School::query()
            ->where('slug', 'little-seeds-preschool')
            ->firstOrFail();

        $membership = $request->user()->schoolMemberships()
            ->where('school_id', $school->id)
            ->where('status', 'active')
            ->whereIn('role', ['administrator', 'teacher'])
            ->firstOrFail();

        $school->load([
            'classes' => function ($query) use ($membership): void {
                $query->with(['children.guardians', 'children.latestAttendanceRecord', 'staffMembers'])
                    ->orderBy('name');

                if ($membership->role === 'teacher' && $membership->source_type && $membership->source_id) {
                    $query->whereHas('staffMembers', fn ($staffQuery) => $staffQuery->where('staff_members.id', $membership->source_id));
                }
            },
            'staffMembers',
        ]);

        return view('roster.index', [
            'school' => $school,
        ]);
    }
}
