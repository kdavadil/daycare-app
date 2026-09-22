<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoPersonaTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_login_is_hidden_and_unavailable_when_disabled(): void
    {
        config()->set('services.demo_login.enabled', false);

        $this->get('/login')
            ->assertOk()
            ->assertDontSee('Demo persona login');

        $this->post('/auth/demo', [
            'persona' => 'admin',
            'pin' => 'anything',
        ])->assertNotFound();
    }

    public function test_demo_login_authenticates_seeded_persona_with_pin(): void
    {
        config()->set('services.demo_login.enabled', true);
        config()->set('services.demo_login.pin', 'testing-pin');

        $school = School::factory()->create(['name' => 'Little Seeds Preschool']);
        $user = User::factory()->create([
            'name' => 'Mia Reyes',
            'email' => 'admin.mia@sibol.test',
            'password' => null,
        ]);
        $user->schoolMemberships()->create([
            'school_id' => $school->id,
            'role' => 'administrator',
            'status' => 'active',
        ]);

        $this->post('/auth/demo', [
            'persona' => 'admin',
            'pin' => 'testing-pin',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($user);
    }

    public function test_demo_login_rejects_wrong_pin(): void
    {
        config()->set('services.demo_login.enabled', true);
        config()->set('services.demo_login.pin', 'testing-pin');

        $this->from('/login')->post('/auth/demo', [
            'persona' => 'teacher',
            'pin' => 'wrong-pin',
        ])
            ->assertRedirect('/login')
            ->assertSessionHasErrors('pin');

        $this->assertGuest();
    }

    public function test_parent_dashboard_shows_family_features_without_staff_actions(): void
    {
        $school = School::factory()->create(['name' => 'Little Seeds Preschool']);
        $user = User::factory()->create([
            'name' => 'Rose Dela Cruz',
            'email' => 'rose.delacruz@sibol.test',
        ]);
        $user->schoolMemberships()->create([
            'school_id' => $school->id,
            'role' => 'guardian',
            'status' => 'active',
        ]);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Parent or guardian')
            ->assertSee('Maya Dela Cruz')
            ->assertSee('Pickup notes')
            ->assertDontSee('Manage roster')
            ->assertDontSee('Take attendance');
    }
}
