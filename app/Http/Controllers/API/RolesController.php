<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResourceCollection;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{
    public function index(): RoleResourceCollection
    {
        return RoleResourceCollection::make(Role::all());
    }
}
