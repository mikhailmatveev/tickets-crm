<?php

namespace App\Models;

use App\Enums\Ticket\Status;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Ticket',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'customer_id', type: 'integer', example: 1),
        new OA\Property(property: 'subject', type: 'string', maxLength: 255, example: 'Проблема с заказом'),
        new OA\Property(property: 'text', type: 'string', example: 'Добрый день! Не прошёл заказ. Что делать?'),
        new OA\Property(property: 'status', ref: '#/components/schemas/Status', default: 'new'),
        new OA\Property(property: 'manager_replied_at', type: Carbon::class, format: 'date-time', example: '2026-04-17T10:00:00Z', nullable: true),
        new OA\Property(property: 'created_at', type: Carbon::class, format: 'date-time', example: '2026-04-17T10:00:00Z', nullable: true),
        new OA\Property(property: 'updated_at', type: Carbon::class, format: 'date-time', example: '2026-04-17T10:00:00Z', nullable: true),
        new OA\Property(property: 'customer', ref: '#/components/schemas/Customer'),
        new OA\Property(property: 'replies', items: new OA\Items(ref: '#/components/schemas/TicketReply')),
        new OA\Property(property: 'attachments', type: 'array', items: new OA\Items(ref: '#/components/schemas/MediaResource'))
    ],
    type: 'object'
)]
class Ticket extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'customer_id',
        'subject',
        'text',
        'status',
        'manager_replied_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => Status::class,
            'manager_replied_at' => 'datetime',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachments');
    }

    /**
     * Добавляет текст ответа менеджера в ticket_replies
     * @param string $text Текст ответа
     * @param int $userId ID пользователя
     * @return void
     */
    public function addReply(string $text, int $userId): void
    {
        $this->replies()->create([
            'text' => $text,
            'user_id' => $userId,
        ]);
    }

    /**
     * Обновляет статус тикету и если тикету присваивается статус "Выполнено",
     * то обновляется и manager_replied_at
     * @param Status $status Статус тикета
     * @return void
     */
    public function changeStatus(Status $status): void
    {
        $this->status = $status;

        // Если статус сменился на "выполнено" - обновляем время
        if ($this->isDone() && $this->manager_replied_at === null) {
            $this->manager_replied_at = now();
        }

        // Если статус изменился с "выполнено" на какой-то другой, то сбрасываем время
        if (!$this->isDone()) {
            $this->manager_replied_at = null;
        }
    }

    /**
     * Проверяет, находится ли тикет в статусе "Новый"
     * @return bool
     */
    public function isNew(): bool
    {
        return $this->status === Status::NEW;
    }

    /**
     * Проверяет, находится ли тикет в статусе "В работе"
     * @return bool
     */
    public function isWorking(): bool
    {
        return $this->status === Status::WORKING;
    }

    /**
     * Проверяет, находится ли тикет в статусе "Выполнено"
     * @return bool
     */
    public function isDone(): bool
    {
        return $this->status === Status::DONE;
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(TicketReply::class);
    }

    /**
     * Фильтр по полю email, которое есть у модели Customer
     * @param Builder $query Билдер
     * @param string $email E-mail
     * @return Builder Билдер
     */
    public function scopeByEmail(Builder $query, string $email): Builder
    {
        return $query->whereHas('customer', function (Builder $query) use ($email) {
            $query->whereLike('email', "%{$email}%");
        });
    }

    /**
     * Фильтр по полю phone, которое есть у модели Customer
     * @param Builder $query Билдер
     * @param string $phone Телефон
     * @return Builder Билдер
     */
    public function scopeByPhone(Builder $query, string $phone): Builder
    {
        return $query->whereHas('customer', function (Builder $query) use ($phone) {
            $query->whereLike('phone', "%{$phone}%");
        });
    }

    /**
     * Фильтр по дате ответа менеджера
     * @param Builder $query Билдер
     * @param string $date Дата
     * @return Builder Билдер
     */
    public function scopeByDate(Builder $query, string $date): Builder
    {
        return $query->whereDate('manager_replied_at', '<=', $date);
    }

    /**
     * Фильтр по статусу тикета
     * @param Builder $query Билдер
     * @param string $status Статус
     * @return Builder Билдер
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Фильтр для статистики тикетов за текущую неделю
     * @param Builder $query
     * @return Builder
     */
    public function scopeRepliedThisDay(Builder $query): Builder
    {
        $today = Carbon::today();
        $dayStart = $today->copy()->startOfDay();
        $dayEnd = $today->copy()->endOfDay();

        return $query->whereBetween('manager_replied_at', [
            $dayStart,
            $dayEnd
        ]);
    }

    /**
     * Фильтр для статистики тикетов за текущую неделю
     * @param Builder $query
     * @return Builder
     */
    public function scopeRepliedThisWeek(Builder $query): Builder
    {
        $today = Carbon::today();
        $weekStart = $today->copy()->startOfWeek();
        $weekEnd = $today->copy()->endOfWeek();

        return $query->whereBetween('manager_replied_at', [
            $weekStart,
            $weekEnd
        ]);
    }


    /**
     * Фильтр для статистики тикетов за текущий месяц
     * @param Builder $query
     * @return Builder
     */
    public function scopeRepliedThisMonth(Builder $query): Builder
    {
        $today = Carbon::today();
        $monthStart = $today->copy()->startOfMonth();
        $monthEnd = $today->copy()->endOfMonth();

        return $query->whereBetween('manager_replied_at', [
            $monthStart,
            $monthEnd
        ]);
    }
}
