<?php

namespace Database\Seeders;

use App\Actions\CreateTicket;
use App\Actions\RotateSiteKey;
use App\Enums\AuthorType;
use App\Enums\SiteFramework;
use App\Enums\StaffRole;
use App\Enums\TicketPriority;
use App\Enums\TicketSource;
use App\Enums\TicketStatus;
use App\Models\Site;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@centralhub.test'],
            [
                'name' => 'Central Hub Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => StaffRole::Admin,
                'is_active' => true,
            ],
        );

        $agent = User::query()->updateOrCreate(
            ['email' => 'agent@centralhub.test'],
            [
                'name' => 'Ama Mensah',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => StaffRole::Agent,
                'is_active' => true,
            ],
        );

        $fragrance = Site::query()->updateOrCreate(
            ['slug' => 'FRG'],
            [
                'name' => 'Fragrance Store',
                'url' => 'https://shop.fragrance.example',
                'framework' => SiteFramework::Laravel,
                'is_active' => true,
                'allowed_origins' => [
                    'https://shop.fragrance.example',
                    'https://www.fragrance.example',
                ],
                'primary_color' => '#00B4FF',
                'hmac_secret' => Str::random(32),
            ],
        );

        $partner = Site::query()->updateOrCreate(
            ['slug' => 'PRT'],
            [
                'name' => 'Partner Portal',
                'url' => 'https://partners.example.com',
                'framework' => SiteFramework::Django,
                'is_active' => true,
                'allowed_origins' => ['https://partners.example.com'],
                'primary_color' => '#0F766E',
                'hmac_secret' => Str::random(32),
            ],
        );

        $marketing = Site::query()->updateOrCreate(
            ['slug' => 'MKT'],
            [
                'name' => 'Marketing Site',
                'url' => 'https://www.example.com',
                'framework' => SiteFramework::Html,
                'is_active' => false,
                'allowed_origins' => ['https://www.example.com'],
                'primary_color' => '#B45309',
                'hmac_secret' => Str::random(32),
            ],
        );

        $admin->sites()->syncWithoutDetaching([$fragrance->id, $partner->id, $marketing->id]);
        $agent->sites()->syncWithoutDetaching([$fragrance->id]);

        $rotate = app(RotateSiteKey::class);

        if ($fragrance->tokens()->doesntExist()) {
            $result = $rotate->handle($fragrance);
            $this->command?->info('Fragrance Store API token: '.$result['plain_text']);
        }

        if ($partner->tokens()->doesntExist()) {
            $rotate->handle($partner);
        }

        if ($marketing->tokens()->doesntExist()) {
            $rotate->handle($marketing);
        }

        if (Ticket::query()->doesntExist()) {
            $this->seedDemoTickets($fragrance, $partner, $marketing, $agent, $admin);
        }
    }

    private function seedDemoTickets(
        Site $fragrance,
        Site $partner,
        Site $marketing,
        User $agent,
        User $admin,
    ): void {
        $createTicket = app(CreateTicket::class);

        $ticket1 = $createTicket->handle($fragrance, [
            'customer_name' => 'Kwame Boateng',
            'customer_email' => 'kwame@example.com',
            'subject' => 'Order #1043 not delivered',
            'body' => 'I paid on Monday but have not received my perfume. Order #1043 shows as shipped.',
            'priority' => TicketPriority::High,
            'source' => TicketSource::Widget,
            'meta' => [
                'page' => 'https://shop.fragrance.example/orders/1043',
                'browser' => 'Chrome 131 · macOS',
                'order_id' => '1043',
            ],
            'assigned_to' => $agent->id,
        ]);

        $ticket1->forceFill(['status' => TicketStatus::Open])->save();

        $ticket1->replies()->create([
            'user_id' => $agent->id,
            'author_type' => AuthorType::Agent,
            'body' => 'Thanks for reaching out. I am checking the courier tracking for order #1043 now.',
            'is_internal' => false,
        ]);

        $ticket1->replies()->create([
            'user_id' => $agent->id,
            'author_type' => AuthorType::Agent,
            'body' => 'Courier shows delayed in Accra sorting. Offering replacement or refund if not delivered by Friday.',
            'is_internal' => true,
        ]);

        $ticket1->replies()->create([
            'user_id' => null,
            'author_type' => AuthorType::Customer,
            'body' => 'A replacement would be great if it can arrive this week.',
            'is_internal' => false,
        ]);

        $createTicket->handle($fragrance, [
            'customer_name' => 'Esi Owusu',
            'customer_email' => 'esi@example.com',
            'subject' => 'Wrong scent in package',
            'body' => 'I ordered jasmine but received sandalwood.',
            'priority' => TicketPriority::Normal,
            'source' => TicketSource::Api,
            'assigned_to' => $admin->id,
            'meta' => [
                'page' => 'https://shop.fragrance.example/account/orders',
                'browser' => 'Safari 18 · iOS',
                'order_id' => '991',
            ],
        ])->forceFill([
            'status' => TicketStatus::InProgress,
            'assigned_to' => $admin->id,
        ])->save();

        $createTicket->handle($partner, [
            'customer_name' => 'Nana Adjei',
            'customer_email' => 'nana@partner.example',
            'subject' => 'Cannot reset partner password',
            'body' => 'The reset link keeps expiring immediately.',
            'priority' => TicketPriority::High,
            'source' => TicketSource::Api,
            'meta' => [
                'page' => 'https://partners.example.com/login',
                'browser' => 'Firefox 134 · Windows',
            ],
        ])->forceFill([
            'status' => TicketStatus::WaitingCustomer,
        ])->save();

        $createTicket->handle($fragrance, [
            'customer_name' => 'Abena Darko',
            'customer_email' => 'abena@example.com',
            'subject' => 'Checkout fails on mobile',
            'body' => 'Checkout button does nothing on Android Chrome.',
            'priority' => TicketPriority::Urgent,
            'source' => TicketSource::Widget,
            'meta' => [
                'page' => 'https://shop.fragrance.example/checkout',
                'browser' => 'Chrome 131 · Android',
            ],
        ]);

        $createTicket->handle($marketing, [
            'customer_name' => 'Yaw Mensah',
            'customer_email' => 'yaw@example.com',
            'subject' => 'Newsletter signup confirmation missing',
            'body' => 'I signed up but never got a confirmation email.',
            'priority' => TicketPriority::Low,
            'source' => TicketSource::Manual,
            'assigned_to' => $admin->id,
            'meta' => [
                'page' => 'https://www.example.com/newsletter',
                'browser' => 'Edge 131 · Windows',
            ],
        ])->forceFill([
            'status' => TicketStatus::Resolved,
            'resolved_at' => now()->subDay(),
        ])->save();

        $createTicket->handle($partner, [
            'customer_name' => 'Tech Ops',
            'customer_email' => 'ops@partner.example',
            'subject' => 'API key rotation request',
            'body' => 'Please rotate our integration key after the deploy window.',
            'priority' => TicketPriority::Normal,
            'source' => TicketSource::Email,
            'meta' => [
                'page' => 'https://partners.example.com/settings/api',
                'browser' => 'Chrome 131 · Linux',
            ],
        ])->forceFill([
            'status' => TicketStatus::Open,
        ])->save();
    }
}
