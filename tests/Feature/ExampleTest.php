<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_redirects_to_services_index(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('services.index'));
    }

    public function test_services_index_returns_a_successful_response(): void
    {
        $response = $this->get('/services');

        $response->assertStatus(200);
    }
}
