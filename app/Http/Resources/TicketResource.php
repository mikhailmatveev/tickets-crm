<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'subject' => $this->subject,
            'text' => $this->text,
            'status' => $this->status,
            'manager_replied_at' => $this->manager_replied_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'replies' => TicketReplyResource::collection($this->whenLoaded('replies')),
            'attachments' => MediaResource::collection($this->getMedia('attachments'))
        ];
    }
}
