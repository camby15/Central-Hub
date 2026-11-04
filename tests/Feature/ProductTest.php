<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageProducts;
use App\Models\Product;
use App\Models\Site;
use App\Models\Ticket;
use App\Models\User;
use App\Support\HubViewData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_belongs_to_site_and_appears_on_ticket_payload(): void
    {
        $site = Site::factory()->create();
        $product = Product::factory()->forSite($site)->create(['name' => 'Pension App']);
        $ticket = Ticket::factory()->forSite($site)->create(['product_id' => $product->id]);

        $payload = HubViewData::ticket($ticket);

        $this->assertSame('Pension App', $payload['product']);
        $this->assertSame($product->id, $payload['product_id']);
    }

    public function test_admin_can_create_product_from_manage_products_page(): void
    {
        $admin = User::factory()->admin()->create();
        $site = Site::factory()->create();

        Livewire::actingAs($admin)
            ->test(ManageProducts::class)
            ->set('site_id', $site->id)
            ->set('name', 'BlacVolta Mobile')
            ->call('createProduct')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('products', [
            'site_id' => $site->id,
            'name' => 'BlacVolta Mobile',
            'slug' => 'blacvolta-mobile',
        ]);
    }

    public function test_site_payload_includes_product_and_ticket_counts(): void
    {
        $site = Site::factory()->create([
            'contact_email' => 'ops@example.com',
            'contact_phone' => '+233000000000',
        ]);
        Product::factory()->forSite($site)->count(2)->create();
        Ticket::factory()->forSite($site)->count(3)->create();

        $payload = HubViewData::site($site->fresh());

        $this->assertSame(2, $payload['products_count']);
        $this->assertSame(3, $payload['tickets_count']);
        $this->assertSame('ops@example.com', $payload['contact_email']);
    }
}
