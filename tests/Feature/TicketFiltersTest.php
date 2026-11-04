<?php

namespace Tests\Feature;

use App\Enums\StaffRole;
use App\Enums\TicketStatus;
use App\Models\Product;
use App\Models\Site;
use App\Models\Ticket;
use App\Models\User;
use App\Support\HubViewData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketFiltersTest extends TestCase
{
    use RefreshDatabase;

    public function test_view_counts_cover_solo_triage_buckets(): void
    {
        $admin = User::factory()->admin()->create();
        $site = Site::factory()->create();

        Ticket::factory()->forSite($site)->create(['assigned_to' => $admin->id]);
        Ticket::factory()->forSite($site)->create(['assigned_to' => null]);
        Ticket::factory()->forSite($site)->overdue()->create();
        Ticket::factory()->forSite($site)->escalated()->create();

        $counts = HubViewData::viewCounts($admin);

        $this->assertSame(4, $counts['all']);
        $this->assertGreaterThanOrEqual(1, $counts['unassigned']);
        $this->assertGreaterThanOrEqual(1, $counts['overdue']);
        $this->assertSame(1, $counts['escalated']);
    }

    public function test_apply_ticket_filters_supports_search_product_and_status(): void
    {
        $site = Site::factory()->create();
        $product = Product::factory()->forSite($site)->create(['name' => 'Mobile App']);
        $other = Product::factory()->forSite($site)->create(['name' => 'Web Portal']);

        Ticket::factory()->forSite($site)->create([
            'subject' => 'Login broken',
            'product_id' => $product->id,
            'status' => TicketStatus::Open,
            'customer_email' => 'alpha@example.com',
        ]);
        Ticket::factory()->forSite($site)->create([
            'subject' => 'Billing question',
            'product_id' => $other->id,
            'status' => TicketStatus::Resolved,
        ]);

        $filtered = HubViewData::applyTicketFilters(
            Ticket::query(),
            search: 'Login',
            productId: $product->id,
            status: TicketStatus::Open->value,
        )->get();

        $this->assertCount(1, $filtered);
        $this->assertSame('Login broken', $filtered->first()->subject);
    }

    public function test_agent_scoped_counts_respect_site_access(): void
    {
        $fragrance = Site::factory()->create();
        $partner = Site::factory()->create();
        $agent = User::factory()->create(['role' => StaffRole::Agent]);
        $agent->sites()->attach($fragrance);

        Ticket::factory()->forSite($fragrance)->escalated()->create();
        Ticket::factory()->forSite($partner)->escalated()->create();

        $counts = HubViewData::viewCounts($agent);

        $this->assertSame(1, $counts['all']);
        $this->assertSame(1, $counts['escalated']);
    }
}
