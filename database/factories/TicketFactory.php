<?php

namespace Database\Factories;

use App\Enums\Ticket\StatusEnum;
use App\Models\Customer;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

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
        $status = fake()->randomElement(StatusEnum::cases());
        return [
            'customer_id' => Customer::factory(),
            'subject' => fake()->sentence(),
            'text' => fake()->paragraphs(3, true),
            'status' => $status,
            'manager_replied_at' => $status !== StatusEnum::DONE
                ? null
                : fake()
                    ->optional()
                    ->dateTimeBetween('-1 month')
        ];
    }

    public function asNew(): static
    {
        return $this->state(fn () => [
            'status' => StatusEnum::NEW,
            'manager_replied_at' => null
        ]);
    }

    public function asWorking(): static
    {
        return $this->state(fn () => [
            'status' => StatusEnum::WORKING,
            'manager_replied_at' => null
        ]);
    }

    public function asDone(): static
    {
        return $this->state(fn () => [
            'status' => StatusEnum::DONE,
            'manager_replied_at' => fake()->dateTimeBetween('-1 month'),
        ]);
    }
}
