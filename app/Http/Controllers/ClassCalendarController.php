<?php

namespace App\Http\Controllers;

use App\Models\ClassCalendarEvent;
use App\Models\School;
use App\Models\SchoolClass;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ClassCalendarController extends Controller
{
    public function index(Request $request): View
    {
        $school = $this->demoSchool();
        $classes = $this->visibleClasses($request, $school);
        $month = $this->selectedMonth($request, $school);
        $calendarStartsAt = $month->copy()->startOfMonth()->startOfWeek(Carbon::MONDAY);
        $calendarEndsAt = $month->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        $events = ClassCalendarEvent::query()
            ->with('schoolClass')
            ->where('school_id', $school->id)
            ->whereIn('school_class_id', $classes->pluck('id'))
            ->whereBetween('event_date', [$calendarStartsAt->toDateString(), $calendarEndsAt->toDateString()])
            ->orderBy('event_date')
            ->orderBy('starts_at')
            ->orderBy('title')
            ->get();

        return view('class-calendar.index', [
            'school' => $school,
            'classes' => $classes,
            'month' => $month,
            'previousMonth' => $month->copy()->subMonthNoOverflow()->format('Y-m'),
            'nextMonth' => $month->copy()->addMonthNoOverflow()->format('Y-m'),
            'calendarWeeks' => $this->calendarWeeks($calendarStartsAt, $calendarEndsAt, $events, $month),
            'events' => $events,
        ]);
    }

    private function demoSchool(): School
    {
        return School::query()
            ->where('slug', 'little-seeds-preschool')
            ->firstOrFail();
    }

    private function selectedMonth(Request $request, School $school): Carbon
    {
        $month = $request->query('month');

        if (is_string($month) && preg_match('/^\d{4}-\d{2}$/', $month) === 1) {
            $selectedMonth = Carbon::createFromFormat('!Y-m-d', $month.'-01', $school->timezone);

            if ($selectedMonth) {
                return $selectedMonth->startOfMonth();
            }
        }

        return now($school->timezone)->startOfMonth();
    }

    /**
     * @return Collection<int, SchoolClass>
     */
    private function visibleClasses(Request $request, School $school): Collection
    {
        $membership = $request->user()->schoolMemberships()
            ->where('school_id', $school->id)
            ->where('status', 'active')
            ->whereIn('role', ['administrator', 'teacher'])
            ->firstOrFail();

        $query = SchoolClass::query()
            ->where('school_id', $school->id)
            ->orderBy('name');

        if ($membership->role === 'teacher' && $membership->source_type && $membership->source_id) {
            $query->whereHas('staffMembers', fn ($staffQuery) => $staffQuery->where('staff_members.id', $membership->source_id));
        }

        return $query->get();
    }

    /**
     * @param  Collection<int, ClassCalendarEvent>  $events
     * @return Collection<int, Collection<int, array{date: Carbon, inCurrentMonth: bool, isToday: bool, events: Collection<int, ClassCalendarEvent>}>>
     */
    private function calendarWeeks(Carbon $startsAt, Carbon $endsAt, Collection $events, Carbon $month): Collection
    {
        $eventsByDate = $events->groupBy(fn (ClassCalendarEvent $event) => $event->event_date->toDateString());
        $weeks = collect();
        $cursor = $startsAt->copy();

        while ($cursor->lessThanOrEqualTo($endsAt)) {
            $week = collect();

            for ($day = 0; $day < 7; $day++) {
                $date = $cursor->copy();

                $week->push([
                    'date' => $date,
                    'inCurrentMonth' => $date->isSameMonth($month),
                    'isToday' => $date->isToday(),
                    'events' => $eventsByDate->get($date->toDateString(), collect()),
                ]);

                $cursor->addDay();
            }

            $weeks->push($week);
        }

        return $weeks;
    }
}
