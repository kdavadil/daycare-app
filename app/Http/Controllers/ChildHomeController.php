<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\SchoolUserMembership;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ChildHomeController extends Controller
{
    public function __invoke(Request $request, Child $child): View
    {
        $child->load([
            'school',
            'schoolClass',
            'latestAttendanceRecord',
            'journalEntries' => fn ($query) => $query
                ->with('author')
                ->where('status', 'published')
                ->latest('occurred_at')
                ->limit(10),
        ]);

        abort_unless($this->canViewChild($request, $child), 403);

        $linkedChildren = $this->linkedChildren($request, $child->school_id);

        return view('children.show', [
            'child' => $child,
            'linkedChildren' => $linkedChildren,
        ]);
    }

    private function canViewChild(Request $request, Child $child): bool
    {
        $membership = $this->activeMembership($request, $child->school_id);

        if (! $membership) {
            return false;
        }

        if (in_array($membership->role, ['administrator', 'teacher'], true)) {
            return true;
        }

        if ($membership->role !== 'guardian') {
            return false;
        }

        return $child->guardians()
            ->where(function ($query) use ($membership, $request): void {
                $query->where('guardians.email', $request->user()->email);

                if ($membership->source_type && $membership->source_id) {
                    $query->orWhere('guardians.id', $membership->source_id);
                }
            })
            ->exists();
    }

    private function activeMembership(Request $request, int $schoolId): ?SchoolUserMembership
    {
        return $request->user()->schoolMemberships()
            ->where('school_id', $schoolId)
            ->where('status', 'active')
            ->first();
    }

    /**
     * @return Collection<int, Child>
     */
    private function linkedChildren(Request $request, int $schoolId)
    {
        $membership = $this->activeMembership($request, $schoolId);

        if (! $membership || $membership->role !== 'guardian') {
            return collect();
        }

        return Child::query()
            ->where('children.school_id', $schoolId)
            ->whereHas('guardians', function ($query) use ($membership, $request): void {
                $query->where('guardians.email', $request->user()->email);

                if ($membership->source_type && $membership->source_id) {
                    $query->orWhere('guardians.id', $membership->source_id);
                }
            })
            ->with(['schoolClass', 'latestAttendanceRecord'])
            ->orderBy('preferred_name')
            ->get();
    }
}
