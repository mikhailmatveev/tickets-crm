<?php

namespace App\Http\Controllers\API;

use App\DTO\UserUpdatePasswordData;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserUpdatePasswordRequest;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;

class UserUpdatePasswordController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    public function update(UserUpdatePasswordRequest $request, int $id): JsonResponse
    {
        // Удобочитаемый пароль, например: "Tiger-Lamp-River-47"
        $password = implode('-', array_map('ucfirst', fake()->words())) .
            '-' . fake()->numerify('##');

        $data = new UserUpdatePasswordData(
            $id,
            $password
        );

        $this->userService->updatePassword($data);

        return response()
            ->json()
            ->setStatusCode(204)
        ;
    }
}
