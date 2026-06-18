<?php

namespace App\Http\Controllers\API;

use App\DTO\UserCreateData;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserDeleteRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return UserResource::collection(
            User::with('roles')
                ->get()
        );
    }

    public function create(UserCreateRequest $request): JsonResponse
    {
        $user = $this->userService->create(
            UserCreateData::from($request)
        );

        return new UserResource($user)
            ->response()
            ->setStatusCode(201)
        ;
    }

    public function destroy(UserDeleteRequest $request): UserResource
    {
        $user = User::findOrFail($request->id);
        $user->delete();

        return new UserResource($user);
    }
}
