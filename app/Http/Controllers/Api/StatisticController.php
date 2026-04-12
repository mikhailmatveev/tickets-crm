<?php

namespace App\Http\Controllers\Api;

use App\Enums\Ticket\Period;
use App\Enums\Ticket\Status;
use App\Enums\User\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\StatisticRequest;
use App\Http\Resources\StatisticResource;
use App\Models\Ticket;
use App\Models\User;

class StatisticController extends Controller
{
    public function index(StatisticRequest $request): StatisticResource
    {
        // Если period не передан, по умолчанию 'day'
        $period = $request->input('period', 'day');
        // Завершенные тикеты
        $ticketsQuery = Ticket::where('status', Status::DONE);
        // Применяем скоуп по периоду
        switch ($period) {
            case Period::DAY: $ticketsQuery->scopes('repliedThisDay'); break;
            case Period::WEEK: $ticketsQuery->scopes('repliedThisWeek'); break;
            case Period::MONTH: $ticketsQuery->scopes('repliedThisMonth'); break;
        };
        // Пользователи, которые ответили на тикеты
        // Выше всех в таблице отображается пользователь, ответивший на наибольшее кол-во тикетов
        $usersWithDoneTickets = User::whereHas(
            'roles',
            function ($query) {
                $query->where('name', Role::MANAGER);
            })
            ->withCount([
                'ticketReplies as tickets_done' => function ($query) use ($ticketsQuery) {
                    // фильтруем только реплаи к нужным тикетам
                    $query->whereIn('ticket_id', $ticketsQuery->pluck('id'));
                }
            ])
            ->get()
            ->sortByDesc('tickets_done')
        ;
        // Итоговая статистика
        $stats = $usersWithDoneTickets
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'role' => $user->roles->first() ?->name ?? '',
                    'tickets_done' => $user->tickets_done,
                ];
            })
            ->values();
        return new StatisticResource($stats);
    }
}
