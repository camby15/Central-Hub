<?php

namespace Database\Factories;

use App\Enums\TicketPriority;
use App\Enums\TicketSource;
use App\Enums\TicketStatus;
use App\Models\Site;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'number' => 'TST-'.str_pad((string) fake()->unique()->numberBetween(1, 99999), 5, '0', STR_PAD_LEFT),
            'site_id' => Site::factory(),
            'assigned_to' => null,
            'customer_name' => fake()->name(),
            'customer_email' => fake()->safeEmail(),
            'customer_token' => Str::random(64),
            'subject' => fake()->sentence(6),
            'body' => fake()->paragraph(),
            'status' => TicketStatus::Open,
            'priority' => TicketPriority::Normal,
            'source' => TicketSource::Api,
            'meta' => [],
        ];
    }

    public function forSite(Site $site): static
    {
        return $this->state(fn (array $attributes): array => [
            'site_id' => $site->id,
            'number' => $site->slug.'-'.str_pad((string) fake()->unique()->numberBetween(1, 99999), 5, '0', STR_PAD_LEFT),
        ]);
    }
}
