<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApplicationHealthTest extends TestCase
{
    public function test_application_health_endpoint_is_available(): void
    {
        $this->get('/up')->assertOk();
    }
}
