<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class StatisticResourceCollection extends ResourceCollection
{
    public $collects = StatisticResource::class;
}
