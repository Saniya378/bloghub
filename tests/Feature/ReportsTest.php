<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportsTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_can_access_reports_dashboard(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/reports')
            ->assertStatus(200)
            ->assertSee('Reports Dashboard');
    }

    public function test_guest_is_redirected(): void
    {
        $this->get('/admin/reports')->assertRedirect('/login');
    }
}