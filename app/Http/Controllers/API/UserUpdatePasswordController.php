<?php

namespace App\Http\Controllers\API;

use App\DTO\UserUpdatePasswordData;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserUpdatePasswordRequest;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;

class UserUpdatePasswordController extends Controller
{
    const int PASSWORD_MIN_LENGTH = 8;
    const int PASSWORD_MAX_LENGTH = 8;

    public function __construct(
        protected UserService $userService
    ) {}

    public function update(UserUpdatePasswordRequest $request, int $id): JsonResponse
    {
        $data = new UserUpdatePasswordData(
            $id,
            fake()->password(
                self::PASSWORD_MIN_LENGTH,
                self::PASSWORD_MAX_LENGTH
            )
        );

        $this->userService->updatePassword($data);

        return response()
            ->json()
            ->setStatusCode(204)
        ;
    }
}
