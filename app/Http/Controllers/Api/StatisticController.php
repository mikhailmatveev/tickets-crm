<?php

namespace App\Http\Controllers\Api;

use App\Enums\Ticket\Status;
use App\Http\Controllers\Controller;
use App\Http\Resources\StatisticResource;
use App\Models\TicketReply;
use App\Models\User;

class StatisticController extends Controller
{
    public function index(): StatisticResource
    {
        // Пользователи, которые ответили на тикеты
        // Выше всех в таблице отображается пользователь, ответивший на наибольшее кол-во тикетов
        $usersWithDoneTickets = TicketReply::whereHas('ticket', function ($query) {
            $query->where('status', Status::DONE);
        })
            ->select('user_id')
            ->selectRaw('COUNT(DISTINCT ticket_id) as tickets_done')
            ->groupBy('user_id')
            ->orderByDesc('tickets_done')
            ->get();
        // Пользователи с ролями
        $usersWithRoles = User::with('roles:id,name')
            ->select(
                'id',
                'name'
            )
            ->whereIn(
                'id',
                $usersWithDoneTickets->pluck('user_id')
            )
            ->get();
        $usersById = $usersWithRoles->keyBy('id');
        // Итоговая статистика
        $stats = $usersWithDoneTickets
            ->map(
                function ($stat) use ($usersById) {
                    $user = $usersById[$stat->user_id] ?? null;
                    return [
                        'id' => $stat->user_id,
                        'name' => $user->name ?? '',
                        'role' => $user?->roles->first() ?->name ?? '',
                        'tickets_done' => $stat->tickets_done,
                    ];
                }
            )
            ->values()
        ;
        return new StatisticResource($stats);
    }
}
