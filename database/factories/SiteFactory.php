<?php

namespace Database\Factories;

use App\Enums\SiteFramework;
use App\Models\Site;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Site>
 */
class SiteFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->company();
        $slug = Str::upper(Str::substr(Str::slug($name, ''), 0, 3));

        return [
            'name' => $name,
            'slug' => $slug !== '' ? $slug : 'SIT',
            'url' => fake()->url(),
            'api_key_prefix' => Str::lower($slug).'_live',
            'hmac_secret' => Str::random(32),
            'allowed_origins' => [fake()->url()],
            'framework' => fake()->randomElement(SiteFramework::cases()),
            'is_active' => true,
            'primary_color' => fake()->hexColor(),
            'logo_path' => null,
            'last_seen_at' => null,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_active' => false,
        ]);
    }
}
