<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Site;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'site_id' => Site::factory(),
            'name' => Str::title($name),
            'slug' => Str::slug($name),
            'is_active' => true,
        ];
    }

    public function forSite(Site $site): static
    {
        return $this->state(fn (array $attributes): array => [
            'site_id' => $site->id,
        ]);
    }
}
