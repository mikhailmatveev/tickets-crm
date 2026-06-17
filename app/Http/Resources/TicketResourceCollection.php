<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TicketResourceCollection extends ResourceCollection
{
    public $collects = TicketResource::class;
}
