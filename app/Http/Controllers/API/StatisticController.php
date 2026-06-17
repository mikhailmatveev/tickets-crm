<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StatisticRequest;
use App\Http\Resources\StatisticResourceCollection;
use App\Services\StatisticService;

class StatisticController extends Controller
{
    public function __construct(
        private readonly StatisticService $statisticService
    ) {}

    public function index(StatisticRequest $request): StatisticResourceCollection
    {
        return StatisticResourceCollection::make(
            $this->statisticService->getManagerStatistics($request->period())
        );
    }
}
