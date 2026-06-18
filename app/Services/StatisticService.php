<?php

namespace App\Services;

use App\Enums\Ticket\PeriodEnum;
use App\Enums\Ticket\StatusEnum;
use App\Enums\User\RoleEnum;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Collection;

class StatisticService
{
    /**
     * Возвращает статистику по тикетам с разбивкой по каждому менеджеру и его кол-ву обработанных тикетов
     * с сортировкой по убыванию
     * @param PeriodEnum $period Период
     * @return Collection
     */
    public function getManagerStatistics(PeriodEnum $period): Collection
    {
        // Завершенные тикеты
        $ticketsQuery = Ticket::query()->byStatus(StatusEnum::DONE);
        // Применяем скоуп по периоду
        switch ($period) {
            case PeriodEnum::DAY: $ticketsQuery->scopes('repliedThisDay'); break;
            case PeriodEnum::WEEK: $ticketsQuery->scopes('repliedThisWeek'); break;
            case PeriodEnum::MONTH: $ticketsQuery->scopes('repliedThisMonth'); break;
        };
        // Пользователи, которые ответили на тикеты
        // Выше всех в таблице отображается пользователь, ответивший на наибольшее кол-во тикетов
        return User::whereHas(
            'roles',
            function ($query) {
                $query->where('name', RoleEnum::MANAGER);
            })
            ->withCount([
                'ticketReplies as tickets_done' => function ($query) use ($ticketsQuery) {
                    // фильтруем только реплаи к нужным тикетам
                    $query->whereIn('ticket_id', $ticketsQuery->select('id'));
                }
            ])
            ->get()
            ->sortByDesc('tickets_done')
            ->values()
        ;
    }
}
