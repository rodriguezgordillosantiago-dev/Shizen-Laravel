<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_home_route_points_to_the_business_dashboard(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('business.dashboard'));
    }
}
