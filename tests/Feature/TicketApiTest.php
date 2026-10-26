<?php

namespace Tests\Feature;

use App\Actions\RotateSiteKey;
use App\Models\Site;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_site_can_create_a_ticket_with_bearer_token(): void
    {
        $site = Site::factory()->create(['slug' => 'FRG']);
        $plainText = app(RotateSiteKey::class)->handle($site)['plain_text'];

        $response = $this->postJson('/api/v1/tickets', [
            'customer_name' => 'Ama Mensah',
            'customer_email' => 'ama@example.com',
            'subject' => 'Order #1043 not delivered',
            'body' => 'I paid on Monday but have not received my perfume.',
            'priority' => 'high',
            'meta' => ['order_id' => 1043],
        ], [
            'Authorization' => 'Bearer '.$plainText,
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.number', 'FRG-00001')
            ->assertJsonPath('data.status', 'open')
            ->assertJsonPath('data.priority', 'high')
            ->assertJsonStructure(['data' => ['number', 'status', 'priority', 'status_url', 'created_at']]);

        $this->assertDatabaseHas('tickets', [
            'number' => 'FRG-00001',
            'customer_email' => 'ama@example.com',
            'site_id' => $site->id,
        ]);

        $ticket = Ticket::query()->where('number', 'FRG-00001')->first();
        $this->assertNotNull($ticket);
        $this->assertStringContainsString('/status/'.$ticket->customer_token, $response->json('data.status_url'));
    }

    public function test_create_ticket_validation_errors_return_422(): void
    {
        $site = Site::factory()->create();
        $plainText = app(RotateSiteKey::class)->handle($site)['plain_text'];

        $response = $this->postJson('/api/v1/tickets', [
            'customer_name' => 'Ama',
            'customer_email' => 'not-an-email',
            'subject' => 'Hi',
            'body' => 'Body',
        ], [
            'Authorization' => 'Bearer '.$plainText,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['customer_email']);
    }

    public function test_inactive_site_is_rejected(): void
    {
        $site = Site::factory()->inactive()->create();
        $plainText = app(RotateSiteKey::class)->handle($site)['plain_text'];

        $response = $this->postJson('/api/v1/tickets', [
            'customer_name' => 'Ama Mensah',
            'customer_email' => 'ama@example.com',
            'subject' => 'Help',
            'body' => 'Please help',
        ], [
            'Authorization' => 'Bearer '.$plainText,
        ]);

        $response->assertForbidden();
    }

    public function test_valid_hmac_signature_is_accepted(): void
    {
        $site = Site::factory()->create(['hmac_secret' => 'test-secret']);
        $plainText = app(RotateSiteKey::class)->handle($site)['plain_text'];
        $site->refresh();

        $payload = json_encode([
            'customer_name' => 'Ama Mensah',
            'customer_email' => 'ama@example.com',
            'subject' => 'Signed request',
            'body' => 'With HMAC',
        ], JSON_THROW_ON_ERROR);

        $timestamp = (string) now()->timestamp;
        $signature = hash_hmac('sha256', $timestamp.$payload, $site->hmac_secret);

        $response = $this->call(
            'POST',
            '/api/v1/tickets',
            [],
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => 'Bearer '.$plainText,
                'HTTP_X_TIMESTAMP' => $timestamp,
                'HTTP_X_SIGNATURE' => $signature,
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json',
            ],
            $payload,
        );

        $response->assertCreated();
    }

    public function test_invalid_hmac_signature_is_rejected(): void
    {
        $site = Site::factory()->create(['hmac_secret' => 'test-secret']);
        $plainText = app(RotateSiteKey::class)->handle($site)['plain_text'];

        $response = $this->postJson('/api/v1/tickets', [
            'customer_name' => 'Ama Mensah',
            'customer_email' => 'ama@example.com',
            'subject' => 'Signed request',
            'body' => 'With HMAC',
        ], [
            'Authorization' => 'Bearer '.$plainText,
            'X-Timestamp' => (string) now()->timestamp,
            'X-Signature' => 'invalid',
        ]);

        $response->assertUnauthorized();
    }

    public function test_expired_hmac_timestamp_is_rejected(): void
    {
        $site = Site::factory()->create(['hmac_secret' => 'test-secret']);
        $plainText = app(RotateSiteKey::class)->handle($site)['plain_text'];
        $site->refresh();

        $payload = json_encode([
            'customer_name' => 'Ama Mensah',
            'customer_email' => 'ama@example.com',
            'subject' => 'Old request',
            'body' => 'Too old',
        ], JSON_THROW_ON_ERROR);

        $timestamp = (string) now()->subMinutes(10)->timestamp;
        $signature = hash_hmac('sha256', $timestamp.$payload, $site->hmac_secret);

        $response = $this->call(
            'POST',
            '/api/v1/tickets',
            [],
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => 'Bearer '.$plainText,
                'HTTP_X_TIMESTAMP' => $timestamp,
                'HTTP_X_SIGNATURE' => $signature,
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json',
            ],
            $payload,
        );

        $response->assertUnauthorized();
    }

    public function test_site_can_list_and_show_tickets_by_email(): void
    {
        $site = Site::factory()->create(['slug' => 'FRG']);
        $plainText = app(RotateSiteKey::class)->handle($site)['plain_text'];

        $this->postJson('/api/v1/tickets', [
            'customer_name' => 'Ama Mensah',
            'customer_email' => 'ama@example.com',
            'subject' => 'First',
            'body' => 'Body',
        ], ['Authorization' => 'Bearer '.$plainText])->assertCreated();

        $list = $this->getJson('/api/v1/tickets?email=ama@example.com', [
            'Authorization' => 'Bearer '.$plainText,
        ]);

        $list->assertOk()->assertJsonCount(1, 'data');

        $show = $this->getJson('/api/v1/tickets/FRG-00001', [
            'Authorization' => 'Bearer '.$plainText,
        ]);

        $show->assertOk()->assertJsonPath('data.number', 'FRG-00001');
    }

    public function test_site_can_add_a_customer_reply(): void
    {
        $site = Site::factory()->create(['slug' => 'FRG']);
        $plainText = app(RotateSiteKey::class)->handle($site)['plain_text'];

        $this->postJson('/api/v1/tickets', [
            'customer_name' => 'Ama Mensah',
            'customer_email' => 'ama@example.com',
            'subject' => 'First',
            'body' => 'Body',
        ], ['Authorization' => 'Bearer '.$plainText])->assertCreated();

        $response = $this->postJson('/api/v1/tickets/FRG-00001/replies', [
            'body' => 'Any update?',
        ], ['Authorization' => 'Bearer '.$plainText]);

        $response->assertCreated()->assertJsonPath('data.body', 'Any update?');
    }
}
