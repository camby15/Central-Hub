<?php

namespace Tests\Feature;

use App\Enums\TicketStatus;
use App\Models\Site;
use App\Models\Ticket;
use App\Models\User;
use App\Support\HubDashboardData;
use App\Support\HubViewData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HubDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_stats_use_real_overdue_and_response_metrics(): void
    {
        $admin = User::factory()->admin()->create();
        $site = Site::factory()->create();

        Ticket::factory()->forSite($site)->overdue()->create(['status' => TicketStatus::Open]);
        Ticket::factory()->forSite($site)->create([
            'status' => TicketStatus::InProgress,
            'created_at' => now()->subHour(),
            'first_responded_at' => now()->subMinutes(30),
        ]);

        $stats = HubViewData::stats($admin);

        $this->assertSame(1, $stats['overdue_sla']);
        $this->assertSame(1, $stats['in_progress']);
        $this->assertNotSame('—', $stats['avg_first_response']);
    }

    public function test_daily_volume_and_status_breakdown_are_shaped_for_ui(): void
    {
        $admin = User::factory()->admin()->create();
        $site = Site::factory()->create();

        Ticket::factory()->forSite($site)->create(['created_at' => now()]);
        Ticket::factory()->forSite($site)->create([
            'status' => TicketStatus::Closed,
            'created_at' => now()->subDay(),
        ]);

        $volume = HubDashboardData::dailyVolume($admin, '7');
        $byStatus = HubDashboardData::statusBreakdown($admin);
        $dashboard = HubDashboardData::forUser($admin, '7');

        $this->assertCount(7, $volume);
        $this->assertNotEmpty($byStatus);
        $this->assertArrayHasKey('activity', $dashboard);
        $this->assertArrayHasKey('total_in_range', $dashboard['stats']);
    }

    public function test_csat_summary_averages_scored_tickets(): void
    {
        $admin = User::factory()->admin()->create();
        $site = Site::factory()->create();

        Ticket::factory()->forSite($site)->create(['csat_score' => 4, 'csat_submitted_at' => now()]);
        Ticket::factory()->forSite($site)->create(['csat_score' => 5, 'csat_submitted_at' => now()]);
        Ticket::factory()->forSite($site)->create(['csat_score' => null]);

        $summary = HubDashboardData::csatSummary($admin);

        $this->assertSame(2, $summary['scored']);
        $this->assertSame(4.5, $summary['average']);
        $this->assertSame(1, $summary['distribution'][4]);
        $this->assertSame(1, $summary['distribution'][5]);
    }
}
