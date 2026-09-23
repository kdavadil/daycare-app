<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\JournalEntry;
use App\Models\School;
use App\Models\SchoolClass;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DailyUpdateController extends Controller
{
    public function index(Request $request): View
    {
        $school = $this->demoSchool();
        $classes = $this->visibleClasses($request, $school);

        return view('daily-updates.index', [
            'school' => $school,
            'classes' => $classes,
            'categories' => JournalEntry::categories(),
            'recentEntries' => JournalEntry::query()
                ->with(['child.schoolClass', 'author'])
                ->where('school_id', $school->id)
                ->whereIn('school_class_id', $classes->pluck('id'))
                ->latest('occurred_at')
                ->limit(8)
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $school = $this->demoSchool();
        $classes = $this->visibleClasses($request, $school);
        $classIds = $classes->pluck('id')->all();

        $validated = $request->validate([
            'child_id' => ['required', 'integer', Rule::exists('children', 'id')->where('school_id', $school->id)],
            'category' => ['required', 'string', Rule::in(array_keys(JournalEntry::categories()))],
            'title' => ['required', 'string', 'max:120'],
            'body' => ['nullable', 'string', 'max:1200'],
            'meal_amount' => ['nullable', 'string', 'max:80', Rule::requiredIf(fn () => $request->input('category') === JournalEntry::Meal)],
            'occurred_at' => ['required', 'date'],
            'photo' => ['nullable', 'image', 'max:4096'],
        ]);

        $child = Child::query()
            ->where('school_id', $school->id)
            ->whereIn('school_class_id', $classIds)
            ->findOrFail($validated['child_id']);

        $photoPath = null;
        $photoOriginalName = null;

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoPath = $photo->store('journal-photos/'.$school->id, 'local');
            $photoOriginalName = $photo->getClientOriginalName();
        }

        JournalEntry::query()->create([
            'school_id' => $school->id,
            'school_class_id' => $child->school_class_id,
            'child_id' => $child->id,
            'author_id' => $request->user()->id,
            'category' => $validated['category'],
            'title' => $validated['title'],
            'body' => $validated['body'] ?? null,
            'meal_amount' => $validated['meal_amount'] ?? null,
            'photo_path' => $photoPath,
            'photo_original_name' => $photoOriginalName,
            'occurred_at' => Carbon::parse($validated['occurred_at'], $school->timezone),
            'status' => 'published',
        ]);

        return back()->with('daily_update_status', 'Daily update posted for '.$child->preferred_name.'.');
    }

    public function photo(Request $request, JournalEntry $journalEntry): StreamedResponse
    {
        $journalEntry->load('child');

        abort_unless($journalEntry->photo_path, 404);
        abort_unless($this->canViewEntry($request, $journalEntry), 403);
        abort_unless(Storage::disk('local')->exists($journalEntry->photo_path), 404);

        return Storage::disk('local')->response($journalEntry->photo_path, $journalEntry->photo_original_name, [
            'Cache-Control' => 'private, no-store',
        ]);
    }

    private function canViewEntry(Request $request, JournalEntry $entry): bool
    {
        $membership = $request->user()->schoolMemberships()
            ->where('school_id', $entry->school_id)
            ->where('status', 'active')
            ->first();

        if (! $membership) {
            return false;
        }

        if (in_array($membership->role, ['administrator', 'teacher'], true)) {
            return true;
        }

        if ($membership->role !== 'guardian') {
            return false;
        }

        return $entry->child->guardians()
            ->where(function ($query) use ($membership, $request): void {
                $query->where('guardians.email', $request->user()->email);

                if ($membership->source_type && $membership->source_id) {
                    $query->orWhere('guardians.id', $membership->source_id);
                }
            })
            ->exists();
    }

    private function demoSchool(): School
    {
        return School::query()
            ->where('slug', 'little-seeds-preschool')
            ->firstOrFail();
    }

    /**
     * @return Collection<int, SchoolClass>
     */
    private function visibleClasses(Request $request, School $school)
    {
        $membership = $request->user()->schoolMemberships()
            ->where('school_id', $school->id)
            ->where('status', 'active')
            ->whereIn('role', ['administrator', 'teacher'])
            ->firstOrFail();

        $query = SchoolClass::query()
            ->with(['children' => fn ($query) => $query->orderBy('preferred_name')])
            ->where('school_id', $school->id)
            ->orderBy('name');

        if ($membership->role === 'teacher' && $membership->source_type && $membership->source_id) {
            $query->whereHas('staffMembers', fn ($staffQuery) => $staffQuery->where('staff_members.id', $membership->source_id));
        }

        return $query->get();
    }
}
