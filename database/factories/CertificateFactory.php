<?php

namespace Database\Factories;

use App\Models\Recipient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Certificate>
 */
class CertificateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'recipient_id' => Recipient::inRandomOrder()->first() ?? Recipient::factory(),
            'code' => $this->faker->unique()->regexify('[0-9]{2}/DISKOMINFOTIKSAN/I\.II/2025'),
            'issued_date' => $this->faker->date(),
            'created_by' => User::inRandomOrder()->first() ?? User::factory()
        ];
    }
}
