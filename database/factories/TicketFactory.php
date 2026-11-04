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
        $priority = TicketPriority::Normal;
        $windows = $priority->slaWindows();

        return [
            'number' => 'TST-'.str_pad((string) fake()->unique()->numberBetween(1, 99999), 5, '0', STR_PAD_LEFT),
            'site_id' => Site::factory(),
            'product_id' => null,
            'assigned_to' => null,
            'customer_name' => fake()->name(),
            'customer_email' => fake()->safeEmail(),
            'customer_token' => Str::random(64),
            'subject' => fake()->sentence(6),
            'body' => fake()->paragraph(),
            'status' => TicketStatus::Open,
            'priority' => $priority,
            'source' => TicketSource::Api,
            'meta' => [],
            'first_response_due_at' => now()->addHours($windows['first_response_hours']),
            'resolve_due_at' => now()->addHours($windows['resolve_hours']),
            'escalated_at' => null,
            'csat_score' => null,
            'csat_submitted_at' => null,
        ];
    }

    public function forSite(Site $site): static
    {
        return $this->state(fn (array $attributes): array => [
            'site_id' => $site->id,
            'number' => $site->slug.'-'.str_pad((string) fake()->unique()->numberBetween(1, 99999), 5, '0', STR_PAD_LEFT),
        ]);
    }

    public function escalated(): static
    {
        return $this->state(fn (array $attributes): array => [
            'escalated_at' => now(),
        ]);
    }

    public function overdue(): static
    {
        return $this->state(fn (array $attributes): array => [
            'first_response_due_at' => now()->subHour(),
            'resolve_due_at' => now()->subMinutes(30),
            'first_responded_at' => null,
            'status' => TicketStatus::Open,
        ]);
    }
}
