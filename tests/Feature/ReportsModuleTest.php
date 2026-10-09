<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportsModuleTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function author(): User
    {
        return User::factory()->create(['role' => 'author']);
    }

    private function reader(): User
    {
        return User::factory()->create(['role' => 'reader']);
    }

    // ==================== AUTH & ACCESS CONTROL ====================

    public function test_guest_is_redirected(): void
    {
        $this->get('/admin/reports')->assertRedirect('/login');
        $this->get('/admin/reports/authors')->assertRedirect('/login');
        $this->get('/admin/reports/articles')->assertRedirect('/login');
        $this->get('/admin/reports/approvals/articles')->assertRedirect('/login');
        $this->get('/admin/reports/engagement/views')->assertRedirect('/login');
        $this->get('/admin/reports/activity')->assertRedirect('/login');
    }

    public function test_author_cannot_access_admin_reports(): void
    {
        $this->actingAs($this->author())
            ->get('/admin/reports')
            ->assertRedirect(route('dashboard'));
    }

    public function test_reader_cannot_access_admin_reports(): void
    {
        $this->actingAs($this->reader())
            ->get('/admin/reports')
            ->assertRedirect('/');
    }

    // ==================== REPORTS DASHBOARD ====================

    public function test_admin_can_view_reports_dashboard(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/reports')
            ->assertStatus(200)
            ->assertSee('Reports Dashboard')
            ->assertSee('Total Authors')
            ->assertSee('Total Articles')
            ->assertSee('Monthly Activity')
            ->assertSee('Distribution Breakdown');
    }

    public function test_admin_can_export_dashboard_overview_csv(): void
    {
        $response = $this->actingAs($this->admin())
            ->get('/admin/reports/export')
            ->assertStatus(200);

        $this->assertTrue($response->headers->contains('content-type', 'text/csv; charset=UTF-8'));
    }

    // ==================== AUTHOR & USER REPORTS ====================

    public function test_admin_can_view_author_registrations(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/reports/authors')
            ->assertStatus(200)
            ->assertSee('Author &amp; User Registrations', false)
            ->assertSee('Total Registrations');
    }

    public function test_admin_can_view_author_activity(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/reports/authors/activity')
            ->assertStatus(200)
            ->assertSee('Author Activity &amp; Productivity', false)
            ->assertSee('Author Productivity League Table');
    }

    public function test_admin_can_export_authors_csv(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/reports/authors/export/registrations')
            ->assertStatus(200);

        $this->actingAs($this->admin())
            ->get('/admin/reports/authors/export/activity')
            ->assertStatus(200);
    }

    // ==================== ARTICLE REPORTS ====================

    public function test_admin_can_view_article_summary(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/reports/articles')
            ->assertStatus(200)
            ->assertSee('Article Summary')
            ->assertSee('Articles Log');
    }

    public function test_admin_can_view_articles_by_author(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/reports/articles/authors')
            ->assertStatus(200)
            ->assertSee('Articles Grouped by Author');
    }

    public function test_admin_can_view_articles_by_category(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/reports/articles/categories')
            ->assertStatus(200)
            ->assertSee('Articles Grouped by Category');
    }

    public function test_admin_can_export_articles_csv(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/reports/articles/export')
            ->assertStatus(200);
    }

    // ==================== APPROVAL REPORTS ====================

    public function test_admin_can_view_article_approvals(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/reports/approvals/articles')
            ->assertStatus(200)
            ->assertSee('Article Approvals &amp; Editorial Moderation', false)
            ->assertSee('Editorial Review Decisions');
    }

    public function test_admin_can_view_author_approvals(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/reports/approvals/authors')
            ->assertStatus(200)
            ->assertSee('Author Onboarding &amp; Status Approvals', false);
    }

    public function test_admin_can_export_article_approvals_csv(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/reports/approvals/articles/export')
            ->assertStatus(200);
    }

    // ==================== ENGAGEMENT REPORTS ====================

    public function test_admin_can_view_engagement_reports(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/reports/engagement/views')
            ->assertStatus(200)
            ->assertSee('Engagement')
            ->assertSee('Most Popular')
            ->assertSee('Total Views');
    }

    public function test_admin_can_export_engagement_csv(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/reports/engagement/export')
            ->assertStatus(200);
    }

    // ==================== ACTIVITY & AUDIT LOG ====================

    public function test_admin_can_view_activity_audit_log(): void
    {
        $admin = $this->admin();
        ActivityLog::create([
            'user_id' => $admin->id,
            'action' => 'Approved',
            'module' => 'Article',
            'description' => 'Admin approved article Test Post',
            'ip_address' => '127.0.0.1',
        ]);

        $this->actingAs($admin)
            ->get('/admin/reports/activity')
            ->assertStatus(200)
            ->assertSee('Activity')
            ->assertSee('Audit Log')
            ->assertSee('Admin approved article Test Post');
    }

    public function test_admin_can_export_activity_log_csv(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/reports/activity/export')
            ->assertStatus(200);
    }
}
