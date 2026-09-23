<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class DemoPersonaController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        abort_unless(config('services.demo_login.enabled'), 404);

        $validated = $request->validate([
            'persona' => ['required', Rule::in(array_keys(config('services.demo_login.personas', [])))],
            'pin' => ['required', 'string'],
        ]);

        if (! hash_equals((string) config('services.demo_login.pin'), $validated['pin'])) {
            return back()
                ->withErrors(['pin' => 'That demo PIN did not match.'])
                ->onlyInput('persona');
        }

        $email = config('services.demo_login.personas.'.$validated['persona'].'.email');
        $user = User::query()->where('email', $email)->first();

        if (! $user) {
            return back()
                ->with('status', 'Demo users have not been seeded yet. Please run the database seed step.')
                ->onlyInput('persona');
        }

        Auth::login($user, remember: false);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
