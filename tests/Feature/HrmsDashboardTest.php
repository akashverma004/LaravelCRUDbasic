<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HrmsDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_loads_successfully(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('PeopleFlow Control Center');
    }

    public function test_department_can_be_created_from_dashboard_form(): void
    {
        $response = $this->post('/departments', [
            'name' => 'Customer Success',
            'code' => 'CS',
            'lead_name' => 'John Smith',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('departments', ['code' => 'CS']);
    }
}
