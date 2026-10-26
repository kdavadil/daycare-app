<?php

namespace Tests\Feature;

use App\Models\Child;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductionFeatureAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_disabled_staff_features_return_not_found_even_for_an_admin(): void
    {
        $school = School::factory()->create();
        $admin = User::factory()->create();
        $admin->schoolMemberships()->create([
            'school_id' => $school->id,
            'role' => 'administrator',
            'status' => 'active',
        ]);

        config()->set([
            'features.roster' => false,
            'features.attendance' => false,
            'features.class_calendar' => false,
            'features.daily_updates' => false,
        ]);

        $this->actingAs($admin)
            ->get('/roster')->assertNotFound();

        $this->get('/attendance')->assertNotFound();
        $this->get('/class-calendar')->assertNotFound();
        $this->get('/daily-updates')->assertNotFound();
        $this->post('/attendance')->assertNotFound();
        $this->post('/daily-updates')->assertNotFound();
    }

    public function test_disabled_staff_features_are_hidden_from_the_dashboard(): void
    {
        $school = School::factory()->create();
        $admin = User::factory()->create();
        $admin->schoolMemberships()->create([
            'school_id' => $school->id,
            'role' => 'administrator',
            'status' => 'active',
        ]);

        config()->set([
            'features.roster' => false,
            'features.attendance' => false,
            'features.class_calendar' => false,
            'features.daily_updates' => false,
        ]);

        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Staff tools are being prepared.')
            ->assertDontSee('Manage roster')
            ->assertDontSee('Review attendance')
            ->assertDontSee('View class calendar');
    }

    public function test_disabled_family_portal_and_messaging_urls_return_not_found(): void
    {
        $school = School::factory()->create();
        $class = SchoolClass::factory()->create(['school_id' => $school->id]);
        $child = Child::factory()->create([
            'school_id' => $school->id,
            'school_class_id' => $class->id,
        ]);
        $parent = User::factory()->create();
        $parent->schoolMemberships()->create([
            'school_id' => $school->id,
            'role' => 'guardian',
            'status' => 'active',
        ]);

        config()->set([
            'features.family_portal' => false,
            'features.messaging' => false,
        ]);

        $this->actingAs($parent)
            ->get(route('children.show', $child))
            ->assertNotFound();

        $this->get(route('children.messages.index', $child))->assertNotFound();
        $this->post(route('children.messages.store', $child), ['body' => 'Hello'])->assertNotFound();
    }
}
