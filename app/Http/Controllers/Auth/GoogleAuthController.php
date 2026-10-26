<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Guardian;
use App\Models\SchoolUserMembership;
use App\Models\StaffMember;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;

class GoogleAuthController extends Controller
{
    public function redirect(SocialiteFactory $socialite): RedirectResponse|SymfonyRedirectResponse
    {
        if (! config('services.google.client_id') || ! config('services.google.client_secret')) {
            return redirect()->route('login')->with('status', 'Google sign-in is not configured yet.');
        }

        return $socialite->driver('google')
            ->scopes(['openid', 'profile', 'email'])
            ->redirect();
    }

    public function callback(SocialiteFactory $socialite): RedirectResponse
    {
        $googleUser = $socialite->driver('google')->stateless()->user();

        $user = User::query()->updateOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name' => $googleUser->getName() ?: $googleUser->getNickname() ?: 'Sibol user',
                'google_id' => $googleUser->getId(),
                'avatar_url' => $googleUser->getAvatar(),
                'email_verified_at' => now(),
                'password' => null,
            ],
        );

        $this->syncSchoolMemberships($user);

        Auth::login($user, remember: true);

        request()->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function logout(): RedirectResponse
    {
        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'You have signed out of Sibol.');
    }

    private function syncSchoolMemberships(User $user): void
    {
        $email = str($user->email)->lower()->toString();

        StaffMember::query()
            ->whereRaw('lower(email) = ?', [$email])
            ->where('status', 'active')
            ->each(function (StaffMember $staffMember) use ($user): void {
                SchoolUserMembership::query()->updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'school_id' => $staffMember->school_id,
                        'role' => $staffMember->role,
                    ],
                    [
                        'status' => 'active',
                        'source_type' => StaffMember::class,
                        'source_id' => $staffMember->id,
                    ],
                );
            });

        Guardian::query()
            ->whereRaw('lower(email) = ?', [$email])
            ->each(function (Guardian $guardian) use ($user): void {
                SchoolUserMembership::query()->updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'school_id' => $guardian->school_id,
                        'role' => 'guardian',
                    ],
                    [
                        'status' => 'active',
                        'source_type' => Guardian::class,
                        'source_id' => $guardian->id,
                    ],
                );
            });
    }
}
