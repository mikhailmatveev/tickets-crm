<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'TicketReply',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'ticket_id', type: 'integer', example: 1),
        new OA\Property(property: 'user_id', type: 'integer', example: 1),
        new OA\Property(property: 'text', type: 'string', example: 'Добрый день! Вы оставляли заявку на сайте о том, что у вас не прошёл заказ. Попробуйте сделать заказ повторно.'),
        new OA\Property(property: 'created_at', type: Carbon::class, format: 'date-time', example: '2026-04-17T10:00:00Z', nullable: true),
        new OA\Property(property: 'updated_at', type: Carbon::class, format: 'date-time', example: '2026-04-17T10:00:00Z', nullable: true),
        new OA\Property(property: 'ticket', ref: '#/components/schemas/Ticket'),
        new OA\Property(property: 'user', ref: '#/components/schemas/User')
    ],
    type: 'object'
)]
class TicketReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'text',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
}
