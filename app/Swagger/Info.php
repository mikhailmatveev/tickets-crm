<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\Info(version: '1.0', title: 'Tickets CRM')]
#[OA\Server(url: 'http://localhost:8000', description: 'Local dev server')]
class Info {}
