<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserUpdateRoleRequest;
use App\Http\Resources\UserResource;
use App\Models\User;

class UserUpdateRoleController extends Controller
{
    public function update(UserUpdateRoleRequest $request, int $id): UserResource
    {
        $validated = $request->validated();
        $user = User::findOrFail($id);
        $user->roles()->sync([$validated['role_id']]);
        return new UserResource($user);
    }
}
