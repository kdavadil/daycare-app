<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\Child;
use App\Models\School;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AttendanceController extends Controller
{
    public function index(): View
    {
        return view('attendance.index', [
            'school' => $this->demoSchool(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'child_id' => ['required', 'integer', 'exists:children,id'],
            'type' => ['required', 'string', Rule::in([AttendanceRecord::CheckIn, AttendanceRecord::CheckOut])],
        ]);

        $school = $this->demoSchool();

        $child = Child::query()
            ->with(['latestAttendanceRecord', 'schoolClass'])
            ->where('school_id', $school->id)
            ->findOrFail($validated['child_id']);

        if ($child->latestAttendanceRecord?->type === $validated['type']) {
            return back()->with('attendance_error', $child->preferred_name.' is already '.($validated['type'] === AttendanceRecord::CheckIn ? 'checked in.' : 'checked out.'));
        }

        AttendanceRecord::query()->create([
            'school_id' => $school->id,
            'school_class_id' => $child->school_class_id,
            'child_id' => $child->id,
            'type' => $validated['type'],
            'occurred_at' => now($school->timezone),
            'actor_name' => 'Teacher Ana Cruz',
            'note' => 'Preview attendance action',
        ]);

        return back()->with('attendance_status', $child->preferred_name.' was '.($validated['type'] === AttendanceRecord::CheckIn ? 'checked in.' : 'checked out.'));
    }

    private function demoSchool(): School
    {
        return School::query()
            ->with([
                'classes' => ['children.latestAttendanceRecord', 'staffMembers'],
            ])
            ->where('slug', 'little-seeds-preschool')
            ->firstOrFail();
    }
}
