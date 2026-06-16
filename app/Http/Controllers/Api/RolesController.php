<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RolesResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return RolesResource::collection(Role::all());
    }
}
