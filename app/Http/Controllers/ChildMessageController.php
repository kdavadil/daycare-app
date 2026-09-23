<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\ChildMessage;
use App\Models\SchoolUserMembership;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ChildMessageController extends Controller
{
    public function index(Request $request, Child $child): View
    {
        $child->load([
            'school',
            'schoolClass.staffMembers',
            'guardians',
            'messages' => fn ($query) => $query->with('sender')->orderBy('sent_at')->orderBy('id'),
        ]);

        $membership = $this->authorizedMembership($request, $child);

        return view('messages.show', [
            'child' => $child,
            'membership' => $membership,
        ]);
    }

    public function store(Request $request, Child $child): RedirectResponse
    {
        $child->load(['school', 'schoolClass.staffMembers', 'guardians']);
        $membership = $this->authorizedMembership($request, $child);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:1200'],
        ]);

        ChildMessage::query()->create([
            'school_id' => $child->school_id,
            'child_id' => $child->id,
            'sender_id' => $request->user()->id,
            'sender_role' => $membership->role,
            'body' => $validated['body'],
            'sent_at' => now($child->school->timezone),
        ]);

        return back()->with('message_status', 'Message sent for '.$child->preferred_name.'.');
    }

    private function authorizedMembership(Request $request, Child $child): SchoolUserMembership
    {
        $membership = $request->user()->schoolMemberships()
            ->where('school_id', $child->school_id)
            ->where('status', 'active')
            ->firstOrFail();

        abort_unless($this->canOpenThread($request, $child, $membership), 403);

        return $membership;
    }

    private function canOpenThread(Request $request, Child $child, SchoolUserMembership $membership): bool
    {
        if ($membership->role === ChildMessage::Administrator) {
            return true;
        }

        if ($membership->role === ChildMessage::Teacher) {
            return $membership->source_type
                && $membership->source_id
                && $child->schoolClass->staffMembers->contains('id', $membership->source_id);
        }

        if ($membership->role !== ChildMessage::Guardian) {
            return false;
        }

        return $child->guardians->contains(fn ($guardian) => $guardian->email === $request->user()->email
            || ($membership->source_type && $membership->source_id && $guardian->id === $membership->source_id));
    }
}
