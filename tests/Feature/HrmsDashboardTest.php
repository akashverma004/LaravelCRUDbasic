<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HrmsDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_loads_successfully(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/');

        $response->assertOk();
        $response->assertSee('Workforce Distribution');
    }

    public function test_department_can_be_created_from_dashboard_form(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post('/departments', [
            'name' => 'Customer Success',
            'code' => 'CS',
            'lead_name' => 'John Smith',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('departments', ['code' => 'CS']);
    }
}
