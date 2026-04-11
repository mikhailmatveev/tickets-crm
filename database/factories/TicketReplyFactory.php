<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TicketReply>
 */
class TicketReplyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::factory(),
            // Здесь намеренно null, чтобы не создавать новых пользователей, а использовать существующих
            'user_id' => null,
            'text' => fake()->paragraph()
        ];
    }

    public function fromUser(User $user): static
    {
        return $this->state(fn () => [
            'user_id' => $user->id,
        ]);
    }

    public function forTicket(Ticket $ticket): static
    {
        return $this->state(fn () => [
            'ticket_id' => $ticket->id
        ]);
    }
}
