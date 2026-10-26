<?php

namespace Database\Factories;

use App\Enums\AuthorType;
use App\Models\Ticket;
use App\Models\TicketReply;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TicketReply>
 */
class TicketReplyFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::factory(),
            'user_id' => null,
            'author_type' => AuthorType::Customer,
            'body' => fake()->paragraph(),
            'is_internal' => false,
        ];
    }

    public function internal(): static
    {
        return $this->state(fn (array $attributes): array => [
            'author_type' => AuthorType::Agent,
            'is_internal' => true,
        ]);
    }
}
