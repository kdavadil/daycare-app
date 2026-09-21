<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Tests\TestCase;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_offers_google_sign_in(): void
    {
        config()->set('services.google.client_id', 'testing-google-client');
        config()->set('services.google.client_secret', 'testing-google-secret');

        $this->get('/login')
            ->assertOk()
            ->assertSee('Continue with Google')
            ->assertSee(route('auth.google.redirect', absolute: false));
    }

    public function test_login_page_handles_missing_google_credentials(): void
    {
        config()->set('services.google.client_id', null);
        config()->set('services.google.client_secret', null);

        $this->get('/login')
            ->assertOk()
            ->assertSee('Google sign-in is ready in code')
            ->assertDontSee('Continue with Google');

        $this->get('/auth/google/redirect')
            ->assertRedirect('/login')
            ->assertSessionHas('status', 'Google sign-in is not configured yet.');
    }

    public function test_dashboard_requires_authentication(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_google_redirect_sends_user_to_provider(): void
    {
        config()->set('services.google.client_id', 'testing-google-client');
        config()->set('services.google.client_secret', 'testing-google-secret');

        $provider = Mockery::mock();
        $provider->shouldReceive('scopes')
            ->once()
            ->with(['openid', 'profile', 'email'])
            ->andReturnSelf();
        $provider->shouldReceive('redirect')
            ->once()
            ->andReturn(new RedirectResponse('https://accounts.google.com/o/oauth2/auth'));

        $socialite = Mockery::mock(SocialiteFactory::class);
        $socialite->shouldReceive('driver')->once()->with('google')->andReturn($provider);

        $this->app->instance(SocialiteFactory::class, $socialite);

        $this->get('/auth/google/redirect')
            ->assertRedirect('https://accounts.google.com/o/oauth2/auth');
    }

    public function test_google_callback_creates_user_and_logs_them_in(): void
    {
        $provider = Mockery::mock();
        $provider->shouldReceive('stateless')->once()->andReturnSelf();
        $provider->shouldReceive('user')
            ->once()
            ->andReturn(SocialiteUser::fake([
                'id' => 'google-123',
                'name' => 'Teacher Ana Cruz',
                'email' => 'teacher.ana@sibol.test',
                'avatar' => 'https://example.test/avatar.jpg',
            ]));

        $socialite = Mockery::mock(SocialiteFactory::class);
        $socialite->shouldReceive('driver')->once()->with('google')->andReturn($provider);

        $this->app->instance(SocialiteFactory::class, $socialite);

        $this->get('/auth/google/callback')
            ->assertRedirect('/dashboard');

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'teacher.ana@sibol.test',
            'google_id' => 'google-123',
            'avatar_url' => 'https://example.test/avatar.jpg',
        ]);
    }

    public function test_user_can_sign_out(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/logout')
            ->assertRedirect('/login')
            ->assertSessionHas('status', 'You have signed out of Sibol.');

        $this->assertGuest();
    }
}
