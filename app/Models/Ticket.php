<?php

namespace App\Models;

use App\Enums\Ticket\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

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

    public function changeStatus(Status $status): void
    {
        $this->status = $status;
        // Если статус сменился на "выполнено" - обновляем время
        if ($this->isDone() && $this->manager_replied_at === null) {
            $this->manager_replied_at = now();
            return;
        }
        // Если статус изменился с "выполнено" на какой-то другой, то сбрасываем время
        if (!$this->isDone()) {
            $this->manager_replied_at = null;
        }
    }

    public function isNew(): bool
    {
        return $this->status === Status::NEW;
    }

    public function isWorking(): bool
    {
        return $this->status === Status::WORKING;
    }

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
}
