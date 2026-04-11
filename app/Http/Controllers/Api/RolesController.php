<?php

namespace App\Http\Controllers\Api;

use App\Enums\User\Role;
use App\Http\Controllers\Controller;
use App\Http\Resources\RolesResource;

class RolesController extends Controller
{
    public function index(): RolesResource
    {
        return new RolesResource(Role::collection());
    }
}
