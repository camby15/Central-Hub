<?php

namespace Tests\Feature;

use App\Actions\RotateSiteKey;
use App\Models\Site;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteKeyTest extends TestCase
{
    use RefreshDatabase;

    public function test_rotating_a_site_key_invalidates_the_old_token(): void
    {
        $site = Site::factory()->create(['slug' => 'FRG']);
        $rotate = app(RotateSiteKey::class);

        $old = $rotate->handle($site)['plain_text'];
        $new = $rotate->handle($site->fresh())['plain_text'];

        $this->assertNotSame($old, $new);

        $this->postJson('/api/v1/tickets', [
            'customer_name' => 'Ama Mensah',
            'customer_email' => 'ama@example.com',
            'subject' => 'Old key',
            'body' => 'Should fail',
        ], [
            'Authorization' => 'Bearer '.$old,
        ])->assertUnauthorized();

        $this->postJson('/api/v1/tickets', [
            'customer_name' => 'Ama Mensah',
            'customer_email' => 'ama@example.com',
            'subject' => 'New key',
            'body' => 'Should work',
        ], [
            'Authorization' => 'Bearer '.$new,
        ])->assertCreated();
    }
}
