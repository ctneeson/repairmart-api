<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Listings>
 */
class ListingsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'userId' => $this->faker->numberBetween(1, 1),
            'listingStatusId' => $this->faker->numberBetween(1, 1),
            'manufacturerId' => $this->faker->numberBetween(1, 118),
            'listingTitle' => $this->faker->sentence(),
            'listingDetail' => $this->faker->sentence(),
            'listingBudgetCurrencyId' => $this->faker->numberBetween(1, 154),
            'listingBudget' => $this->faker->numberBetween(1, 99),
            'listingExpiry' => $this->faker->numberBetween(1, 90),
            'runId' => $this->faker->numberBetween(1, 1)
        ];
    }
}
