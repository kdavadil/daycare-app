<?php

namespace Tests\Feature;

use App\Livewire\Foundation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class FoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_foundation_renders_without_exposing_account_registration(): void
    {
        $this->get('/')->assertOk()->assertSee('Sibol')->assertSee('About');
        $this->get('/register')->assertNotFound();
    }

    public function test_about_page_renders_placeholder_origin_story(): void
    {
        $this->get('/about')
            ->assertOk()
            ->assertSee('About Sibol')
            ->assertSee('placeholder origin copy')
            ->assertSee('Do not enter or publish real child, parent, staff, or payment information yet.');
    }

    public function test_livewire_round_trip_changes_the_language(): void
    {
        Livewire::test(Foundation::class)
            ->assertSee('A little space to grow.')
            ->call('toggleLanguage')
            ->assertSee('Munting espasyo para lumago.')
            ->call('toggleLanguage')
            ->assertSee('A little space to grow.');
    }

    public function test_readiness_checks_the_real_postgresql_database(): void
    {
        $this->assertSame('pgsql', DB::connection()->getDriverName());
        $this->getJson('/ready')->assertOk()->assertExactJson(['status' => 'ready'])
            ->assertHeader('Cache-Control', 'no-store, private');
    }

    public function test_readiness_fails_closed_without_leaking_database_errors(): void
    {
        DB::shouldReceive('select')->once()->andThrow(new \RuntimeException('secret database connection detail'));
        $this->getJson('/ready')->assertStatus(503)->assertExactJson(['status' => 'unavailable']);
    }
}
