<?php
/**
 * ICTHospital - basic application boot test.
 *
 * This replaces the stock Laravel scaffold test, which asserted a 200 from `/`
 * without migrating anything. The root route reads settings out of the database,
 * so against an empty schema it returned a 500 and the assertion said nothing
 * about whether the application works.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_application_boots_and_answers_the_root_route(): void
    {
        $response = $this->get('/');

        // A guest is sent to the login screen rather than served the dashboard.
        // Either is a working application; a 500 is not.
        $this->assertContains(
            $response->getStatusCode(),
            [200, 302],
            'the root route should render or redirect, not error'
        );
    }

    public function test_the_dashboard_is_closed_to_guests(): void
    {
        $this->get('/dashboard')->assertRedirect();
    }
}
