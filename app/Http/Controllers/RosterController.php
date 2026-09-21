<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Contracts\View\View;

class RosterController extends Controller
{
    public function __invoke(): View
    {
        $school = School::query()
            ->with([
                'classes' => ['children.guardians', 'children.latestAttendanceRecord', 'staffMembers'],
                'staffMembers',
            ])
            ->where('slug', 'little-seeds-preschool')
            ->firstOrFail();

        return view('roster.index', [
            'school' => $school,
        ]);
    }
}
