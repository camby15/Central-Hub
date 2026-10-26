<?php

namespace Tests\Feature;

use App\Enums\StaffRole;
use App\Models\Site;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_agent_only_sees_tickets_for_assigned_sites(): void
    {
        $fragrance = Site::factory()->create(['slug' => 'FRG', 'name' => 'Fragrance']);
        $partner = Site::factory()->create(['slug' => 'PRT', 'name' => 'Partner']);

        $agent = User::factory()->create(['role' => StaffRole::Agent]);
        $agent->sites()->attach($fragrance);

        Ticket::factory()->forSite($fragrance)->create(['subject' => 'Fragrance issue']);
        Ticket::factory()->forSite($partner)->create(['subject' => 'Partner issue']);

        $this->assertTrue($agent->canAccessSite($fragrance));
        $this->assertFalse($agent->canAccessSite($partner));

        $accessible = Ticket::query()->forSites($agent->accessibleSiteIds())->pluck('subject');

        $this->assertTrue($accessible->contains('Fragrance issue'));
        $this->assertFalse($accessible->contains('Partner issue'));
    }

    public function test_admin_can_access_all_sites(): void
    {
        $site = Site::factory()->create();
        $admin = User::factory()->admin()->create();

        $this->assertTrue($admin->canAccessAllSites());
        $this->assertTrue($admin->canAccessSite($site));
    }
}
