<?php

namespace Tests\Feature;

use App\Enums\AuthorType;
use App\Models\Site;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class StatusPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_status_page_shows_public_replies_and_hides_internal_notes(): void
    {
        $site = Site::factory()->create();
        $agent = User::factory()->create();

        $ticket = Ticket::factory()->forSite($site)->create([
            'customer_token' => Str::random(64),
            'customer_name' => 'Kwame Boateng',
        ]);

        TicketReply::factory()->create([
            'ticket_id' => $ticket->id,
            'author_type' => AuthorType::Customer,
            'body' => 'Public customer message',
            'is_internal' => false,
        ]);

        TicketReply::factory()->create([
            'ticket_id' => $ticket->id,
            'user_id' => $agent->id,
            'author_type' => AuthorType::Agent,
            'body' => 'Secret internal note',
            'is_internal' => true,
        ]);

        $response = $this->get('/status/'.$ticket->customer_token);

        $response->assertOk()
            ->assertSee('Public customer message')
            ->assertDontSee('Secret internal note')
            ->assertSee($ticket->number);
    }

    public function test_customer_can_reply_on_status_page(): void
    {
        $site = Site::factory()->create();
        $ticket = Ticket::factory()->forSite($site)->create([
            'customer_token' => 'status-token-example-'.Str::random(16),
        ]);

        $response = $this->post('/status/'.$ticket->customer_token.'/reply', [
            'body' => 'Thanks, that helped.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('ticket_replies', [
            'ticket_id' => $ticket->id,
            'body' => 'Thanks, that helped.',
            'is_internal' => false,
            'author_type' => AuthorType::Customer->value,
        ]);
    }
}
