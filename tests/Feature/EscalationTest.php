<?php

namespace Tests\Feature;

use App\Filament\Pages\ViewTicket;
use App\Models\Site;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EscalationTest extends TestCase
{
    use RefreshDatabase;

    public function test_ticket_can_be_escalated_and_cleared(): void
    {
        $admin = User::factory()->admin()->create();
        $site = Site::factory()->create();
        $ticket = Ticket::factory()->forSite($site)->create(['escalated_at' => null]);

        $this->assertFalse($ticket->isEscalated());

        Livewire::actingAs($admin)
            ->test(ViewTicket::class, ['ticket' => $ticket->id])
            ->call('toggleEscalation');

        $this->assertNotNull($ticket->fresh()->escalated_at);

        Livewire::actingAs($admin)
            ->test(ViewTicket::class, ['ticket' => $ticket->id])
            ->call('toggleEscalation');

        $this->assertNull($ticket->fresh()->escalated_at);
    }

    public function test_csat_can_be_saved_on_ticket(): void
    {
        $admin = User::factory()->admin()->create();
        $site = Site::factory()->create();
        $ticket = Ticket::factory()->forSite($site)->create();

        Livewire::actingAs($admin)
            ->test(ViewTicket::class, ['ticket' => $ticket->id])
            ->call('saveCsat', 5);

        $ticket->refresh();

        $this->assertSame(5, $ticket->csat_score);
        $this->assertNotNull($ticket->csat_submitted_at);
    }

    public function test_overdue_scope_uses_due_dates(): void
    {
        $site = Site::factory()->create();
        $overdue = Ticket::factory()->forSite($site)->overdue()->create();
        $onTrack = Ticket::factory()->forSite($site)->create([
            'first_response_due_at' => now()->addDay(),
            'resolve_due_at' => now()->addDays(2),
        ]);

        $ids = Ticket::query()->overdue()->pluck('id');

        $this->assertTrue($ids->contains($overdue->id));
        $this->assertFalse($ids->contains($onTrack->id));
        $this->assertTrue($overdue->isOverdue());
        $this->assertFalse($onTrack->isOverdue());
    }
}
