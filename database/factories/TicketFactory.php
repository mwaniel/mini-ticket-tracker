<?php

namespace Database\Factories;

use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title'       => fake()->sentence(5),
            'description' => fake()->optional(0.7)->paragraph(),
            'status'      => fake()->randomElement(TicketStatus::cases())->value,
            'priority'    => fake()->randomElement(TicketPriority::cases())->value,
        ];
    }
    // wiill help me later in test
    public function open(): static
    {
        return $this->state(['status' => TicketStatus::Open->value]);
    }

    public function closed(): static
    {
        return $this->state(['status' => TicketStatus::Closed->value]);
    }

    public function inProgress(): static
    {
        return $this->state(['status' => TicketStatus::InProgress->value]);
    }

    public function high(): static
    {
        return $this->state(['priority' => TicketPriority::High->value]);
    }
}
